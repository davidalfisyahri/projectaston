<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class purchase_order_detail extends Model
{
    protected $table = 'purchase_order_details';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'po_id',
        'item_name',
        'unit',
        'qty',
        'price',
        'total'
    ];

    // RELASI KE PO
    public function po()
    {
        return $this->belongsTo(purchase_order::class, 'po_id');
    }
}