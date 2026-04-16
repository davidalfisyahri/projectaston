<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'inventory_id',
        'stock_system',
        'stock_actual',
        'difference',
        'notes',
        'opname_date',
        'checked_by',
    ];

    protected $casts = [
        'opname_date' => 'date',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id_inventory');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id_user');
    }
}
