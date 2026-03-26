<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'condition_id',
        'name', 'brand_name', 'description',
        'price', 'status', 'image_path',
    ];

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 状態（マスタ）
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // カテゴリ（N:N）
    public function categories()
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
    }

    // いいね
    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }
    
    // コメント
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    // 購入（1:0..1）
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}