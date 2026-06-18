<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequestApproval extends Model
{
    protected $fillable = [
        'customer_request_id',
        'role',
        'approved_by',
        'status',
        'approved_at'
    ];

    public function request()
    {
        return $this->belongsTo(CustomerRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }
}