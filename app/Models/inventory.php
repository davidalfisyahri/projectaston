<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'id_inventory';

    protected $fillable = ['name_material','type','stock'];

    public function composition()
    {
        return $this->hasMany(Composition::class, 'inventory_id');
    }
}