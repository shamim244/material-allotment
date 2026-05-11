<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_number',
        'description',
        'import_date',
    ];

    protected function casts(): array
    {
        return [
            'import_date' => 'date',
        ];
    }

    /**
     * Auto-generate lot number on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Lot $lot) {
            if (empty($lot->lot_number)) {
                $lastLot = static::orderBy('id', 'desc')->first();
                $nextNum = $lastLot ? ((int) str_replace('LOT-', '', $lastLot->lot_number)) + 1 : 1;
                $lot->lot_number = 'LOT-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function meters()
    {
        return $this->hasMany(Meter::class);
    }

    public function groups()
    {
        return $this->hasMany(MeterGroup::class);
    }
}
