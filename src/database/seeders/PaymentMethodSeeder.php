<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            'カード支払い',
            'コンビニ払い',
        ];

        foreach ($labels as $label) {
            PaymentMethod::firstOrCreate([
                'label' => $label
            ]);
        }
    }
}