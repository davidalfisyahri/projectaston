<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class purchase_order extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_po';

    protected $fillable = [
        'no_po',
        'supplier_id',
        'tanggal',
        'created_by',
        'total'
    ];

    // RELASI KE SUPPLIER
    public function supplier()
    {
        return $this->belongsTo(supplier::class, 'supplier_id');
    }

    // RELASI KE DETAIL
    public function details()
    {
        return $this->hasMany(purchase_order_detail::class, 'po_id');
    }
}