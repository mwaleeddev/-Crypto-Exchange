<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $assets = $user->assets()->get()->map(function ($asset) {
            return [
                'symbol' => $asset->symbol,
                'amount' => (float) $asset->amount,
                'locked_amount' => (float) $asset->locked_amount,
                'available' => (float) $asset->available,
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'balance' => (float) $user->balance,
            ],
            'assets' => $assets,
        ]);
    }
}