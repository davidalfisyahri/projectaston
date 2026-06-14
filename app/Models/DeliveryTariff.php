<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTariff extends Model
{
    protected $fillable = [
        'min_km',
        'max_km',
        'fee',
        'label',
        'is_active',
    ];

    protected $casts = [
        'min_km' => 'decimal:2',
        'max_km' => 'decimal:2',
        'fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Cari delivery fee berdasarkan jarak (km).
     * Return null kalau di luar jangkauan (>40km = custom input).
     */
    public static function getFeeByDistance(float $distance): ?float
    {
        $tariff = self::where('is_active', true)
            ->where('min_km', '<=', $distance)
            ->where('max_km', '>=', $distance)
            ->first();

        return $tariff ? (float) $tariff->fee : null;
    }

    /**
     * Get max km dari semua tarif aktif.
     */
    public static function getMaxDistance(): float
    {
        return (float) self::where('is_active', true)->max('max_km') ?? 0;
    }
}
