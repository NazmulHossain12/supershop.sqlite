<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingMetric extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'revenue' => 'decimal:2',
        'avg_order_value' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
    ];

    public static function recordMetric($data)
    {
        return self::updateOrCreate(
            [
                'date' => $data['date'],
                'source' => $data['source'] ?? null,
                'medium' => $data['medium'] ?? null,
                'campaign' => $data['campaign'] ?? null,
            ],
            $data
        );
    }
}
