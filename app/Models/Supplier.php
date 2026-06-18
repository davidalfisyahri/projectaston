<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'name_pt',
        'name',
        'address',
    ];

    // RELASI KE PO
    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }
}