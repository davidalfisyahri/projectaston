<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    protected $fillable = [
        'request_code',
        'created_by',
        'tanggal',
        'region',
        'customer_number',
        'customer_name',
        'phone',
        'address',
        'note',
        'status',
        'is_paid',
        'is_wa_confirmed',
        'schedule_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(CustomerRequestApproval::class);
    }
}
