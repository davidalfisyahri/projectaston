<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Composition extends Model
{
    protected $table = 'composition';
    protected $primaryKey = 'id_composition';

    protected $fillable = ['grade_id','inventory_id','qty'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function grade()
    {
        return $this->belongsTo(GradeBeton::class, 'grade_id');
    }
}