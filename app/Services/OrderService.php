<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Asset;
use App\Models\Trade;
use App\Events\OrderMatched;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    const COMMISSION_RATE = 0.015; // 1.5%

    public function createOrder(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $order = new Order([
                'user_id' => $user->id,
                'symbol' => $data['symbol'],
                'side' => $data['side'],
                'price' => $data['price'],
                'amount' => $data['amount'],
                'status' => 'open',
            ]);

            if ($data['side'] === 'buy') {
                $this->processBuyOrder($order, $user);
            } else {
                $this->processSellOrder($order, $user);
            }

            $order->save();
            
            // Try to match immediately
            $this->matchOrder($order);

            return $order;
        });
    }

    private function processBuyOrder(Order $order, $user)
    {
        $totalCost = bcmul($order->price, $order->amount, 8);
        
        if (bccomp($user->balance, $totalCost, 8) < 0) {
            throw new \Exception('Insufficient balance');
        }

        // Lock USD balance
        $user->decrement('balance', $totalCost);
    }

    private function processSellOrder(Order $order, $user)
    {
        $asset = $user->getAsset($order->symbol);
        
        if (bccomp($asset->available, $order->amount, 8) < 0) {
            throw new \Exception('Insufficient asset balance');
        }

        // Lock assets
        $asset->increment('locked_amount', $order->amount);
    }

    public function matchOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            if ($order->side === 'buy') {
                $counterOrder = Order::where('symbol', $order->symbol)
                    ->where('side', 'sell')
                    ->where('status', 'open')
                    ->where('price', '<=', $order->price)
                    ->orderBy('price')
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->first();
            } else {
                $counterOrder = Order::where('symbol', $order->symbol)
                    ->where('side', 'buy')
                    ->where('status', 'open')
                    ->where('price', '>=', $order->price)
                    ->orderBy('price', 'desc')
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->first();
            }

            if (!$counterOrder) {
                return null;
            }

            // Execute trade
            $tradeAmount = min($order->remaining_amount, $counterOrder->remaining_amount);
            $tradePrice = $counterOrder->price; // Match at counter order price
            
            // Calculate fees
            $tradeValue = bcmul($tradeAmount, $tradePrice, 8);
            $commission = bcmul($tradeValue, self::COMMISSION_RATE, 8);
            
            // Split commission (buyer pays USD fee, seller pays asset fee)
            $buyerFee = $commission;
            $sellerFeeAssetAmount = bcdiv($commission, $tradePrice, 8);

            // Update orders
            $order->increment('filled_amount', $tradeAmount);
            $counterOrder->increment('filled_amount', $tradeAmount);

            if ($order->isFilled()) {
                $order->status = 'filled';
            }
            if ($counterOrder->isFilled()) {
                $counterOrder->status = 'filled';
            }

            $order->save();
            $counterOrder->save();

            // Create trade record
            $trade = Trade::create([
                'buy_order_id' => $order->side === 'buy' ? $order->id : $counterOrder->id,
                'sell_order_id' => $order->side === 'sell' ? $order->id : $counterOrder->id,
                'symbol' => $order->symbol,
                'price' => $tradePrice,
                'amount' => $tradeAmount,
                'buyer_fee' => $buyerFee,
                'seller_fee' => $sellerFeeAssetAmount,
            ]);

            // Transfer funds and assets
            $this->executeTradeSettlement($order, $counterOrder, $tradeAmount, $tradePrice, $buyerFee, $sellerFeeAssetAmount);

            // Broadcast event
            event(new OrderMatched($order, $counterOrder, [
                'price' => (float) $tradePrice,
                'amount' => (float) $tradeAmount,
                'buyer_fee' => (float) $buyerFee,
                'seller_fee' => (float) $sellerFeeAssetAmount,
            ]));

            return $trade;
        });
    }

    private function executeTradeSettlement($order, $counterOrder, $amount, $price, $buyerFee, $sellerFee)
    {
        $buyOrder = $order->side === 'buy' ? $order : $counterOrder;
        $sellOrder = $order->side === 'sell' ? $order : $counterOrder;

        // Buyer receives assets (minus seller's asset fee)
        $buyerAsset = $buyOrder->user->getAsset($order->symbol);
        $buyerAsset->increment('amount', bcsub($amount, $sellerFee, 8));

        // Seller receives USD (minus buyer's USD fee)
        $totalValue = bcmul($amount, $price, 8);
        $sellerReceives = bcsub($totalValue, $buyerFee, 8);
        $sellOrder->user->increment('balance', $sellerReceives);

        // Release locked amounts
        if ($buyOrder->status !== 'filled') {
            $remainingValue = bcmul($buyOrder->remaining_amount, $buyOrder->price, 8);
            $buyOrder->user->increment('balance', $remainingValue);
        }

        if ($sellOrder->status !== 'filled') {
            $sellAsset = $sellOrder->user->getAsset($order->symbol);
            $sellAsset->decrement('locked_amount', $sellOrder->remaining_amount);
        }
    }

    public function cancelOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            if ($order->status !== 'open') {
                throw new \Exception('Order cannot be cancelled');
            }

            $order->status = 'cancelled';
            $order->save();

            // Release locked funds
            if ($order->side === 'buy') {
                $lockedValue = bcmul($order->remaining_amount, $order->price, 8);
                $order->user->increment('balance', $lockedValue);
            } else {
                $asset = $order->user->getAsset($order->symbol);
                $asset->decrement('locked_amount', $order->remaining_amount);
            }

            return $order;
        });
    }
}