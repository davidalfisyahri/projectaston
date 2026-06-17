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

    public function getUnitAttribute()
    {
        $name = strtolower($this->name_material ?? '');
        $type = strtolower($this->type ?? '');
        
        if ($type === 'admixture' || str_contains($name, 'air') || str_contains($name, 'type d') || str_contains($name, 'type f') || str_contains($name, 'liter') || str_contains($name, 'water')) {
            return 'L';
        }
        
        return 'Kg';
    }
}