<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_po';

    protected $fillable = [
        'no_po',
        'supplier_id',
        'tanggal',
        'created_by',
        'total',
        'status',
        'approved_by',
        'approved_at'
    ];

    // RELASI KE SUPPLIER
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // RELASI KE DETAIL
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }
}