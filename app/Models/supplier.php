<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class supplier extends Model
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
        return $this->hasMany(purchase_order::class, 'supplier_id');
    }
}