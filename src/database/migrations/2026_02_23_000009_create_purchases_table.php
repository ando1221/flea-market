<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->cascadeOnDelete();

            // 配送先（購入時点のスナップショット）
            $table->string('shipping_zip', 255);
            $table->string('shipping_address', 255);
            $table->string('shipping_building', 255)->nullable();

            $table->timestamp('purchased_at')->useCurrent();

            $table->timestamps();

            // 1商品=1購入（売り切り）
            $table->unique('item_id');
            $table->index(['buyer_id', 'purchased_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};