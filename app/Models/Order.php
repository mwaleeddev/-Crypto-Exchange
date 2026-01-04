<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'status',
        'price',
        'amount',
        'filled_amount',
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'amount' => 'decimal:8',
        'filled_amount' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyerTrades()
    {
        return $this->hasMany(Trade::class, 'buy_order_id');
    }

    public function sellerTrades()
    {
        return $this->hasMany(Trade::class, 'sell_order_id');
    }

    public function getRemainingAmountAttribute()
    {
        return bcsub($this->amount, $this->filled_amount, 8);
    }

    public function isFilled()
    {
        return bccomp($this->filled_amount, $this->amount, 8) === 0;
    }
}