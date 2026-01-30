<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'revenue' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getRoiAttribute()
    {
        if ($this->spent == 0)
            return 0;
        return (($this->revenue - $this->spent) / $this->spent) * 100;
    }

    public function getCpaAttribute()
    {
        if ($this->conversions == 0)
            return 0;
        return $this->spent / $this->conversions;
    }

    public function getCtrAttribute()
    {
        if ($this->clicks == 0)
            return 0;
        return ($this->conversions / $this->clicks) * 100;
    }
}
