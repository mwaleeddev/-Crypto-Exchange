<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Order::where('status', 'open')
            ->orderBy('price', $request->get('side') === 'sell' ? 'asc' : 'desc');

        if ($request->has('symbol')) {
            $query->where('symbol', $request->get('symbol'));
        }

        $orders = $query->get()->groupBy('side');

        return response()->json([
            'buy' => $orders->get('buy', collect())->map(function ($order) {
                return $this->formatOrder($order);
            }),
            'sell' => $orders->get('sell', collect())->map(function ($order) {
                return $this->formatOrder($order);
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string|in:BTC,ETH',
            'side' => 'required|string|in:buy,sell',
            'price' => 'required|numeric|min:0.00000001',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();
            $order = $this->orderService->createOrder($request->all(), $user);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $this->formatOrder($order),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancel($id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id);
            $this->orderService->cancelOrder($order);

            return response()->json([
                'message' => 'Order cancelled successfully',
                'order' => $this->formatOrder($order),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function userOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return $this->formatOrder($order);
            });

        return response()->json($orders);
    }

    private function formatOrder(Order $order)
    {
        return [
            'id' => $order->id,
            'symbol' => $order->symbol,
            'side' => $order->side,
            'status' => $order->status,
            'price' => (float) $order->price,
            'amount' => (float) $order->amount,
            'filled_amount' => (float) $order->filled_amount,
            'remaining_amount' => (float) $order->remaining_amount,
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
}