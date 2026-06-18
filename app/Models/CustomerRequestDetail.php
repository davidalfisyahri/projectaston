<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequestDetail extends Model
{
    protected $fillable = [
        'customer_request_id',
        'grade_id',
        'type',
        'qty',
        'price',
        'total'
    ];

    public function grade()
    {
        return $this->belongsTo(GradeBeton::class, 'grade_id', 'id_grade');
    }

    
}
