<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $matchedOrder;
    public $trade;

    public function __construct(Order $order, Order $matchedOrder, $trade)
    {
        $this->order = $order;
        $this->matchedOrder = $matchedOrder;
        $this->trade = $trade;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->order->user_id),
            new PrivateChannel('user.' . $this->matchedOrder->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'order.matched';
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'matched_order_id' => $this->matchedOrder->id,
            'symbol' => $this->order->symbol,
            'price' => (float) $this->trade['price'],
            'amount' => (float) $this->trade['amount'],
            'buyer_fee' => (float) $this->trade['buyer_fee'],
            'seller_fee' => (float) $this->trade['seller_fee'],
            'timestamp' => now()->toISOString(),
        ];
    }
}