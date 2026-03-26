<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'item_id', 'payment_method_id',
        'shipping_zip', 'shipping_address', 'shipping_building',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    // 購入者
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // 商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 支払方法
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}