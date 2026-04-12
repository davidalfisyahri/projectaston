<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'gradebeton';
    protected $primaryKey = 'id_grade';

    protected $fillable = [
        'name',
        'fc',
        'harga_fa',
        'harga_nfa'
    ];

    public function composition()
    {
        return $this->hasMany(Composition::class, 'grade_id', 'id_grade');
    }
}
