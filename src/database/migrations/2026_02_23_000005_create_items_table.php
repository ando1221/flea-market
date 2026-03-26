<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 255);
            $table->string('brand_name', 255)->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('price');

            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            $table->string('status', 255)->default('on_sale');

            $table->string('image_path', 255)->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};