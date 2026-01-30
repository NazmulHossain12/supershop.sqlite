<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoyaltySettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'points_per_currency_unit', 'value' => '1'], // 1 point per $1
            ['key' => 'redemption_value', 'value' => '100'],      // 100 points = $1
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
