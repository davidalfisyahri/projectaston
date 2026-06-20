<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeBeton extends Model
{
    protected $table = 'gradebeton';
    protected $primaryKey = 'id_grade';

    protected $fillable = ['name_grade','mpa','harga'];

    public function composition()
    {
        return $this->hasMany(Composition::class, 'grade_id');
    }
}
