<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_details';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'po_id',
        'inventory_id',
        'unit',
        'qty',
        'price',
        'total'
    ];

    // RELASI KE PO
    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    // RELASI KE INVENTORY
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id_inventory');
    }
}