<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    protected $fillable = [

        'request_code',
        'created_by',
        'tanggal',

        // IDENTITAS
        'customer_name',
        'phone',
        'address',
        'region',
        'customer_number',
        'note',

        // PROFIL BISNIS
        'no_identitas',
        'form_business',
        'business_ownership',
        'section_business',
        'address_business',

        // PAJAK
        'npwp',
        'tax_name',
        'tax_address',

        // IZIN
        'izin_tdp',
        'tdp_date',
        'izin_siup',
        'siup_date',
        'izin_sio',
        'sio_date',

        // OWNER
        'owner_name',
        'owner_address',
        'email',

        // PROJECT
        'office_address',
        'ongoing_project',

        // JADWAL
        'schedule_date',

        // STATUS
        'status',

        // DELIVERY
        'delivery_distance',
        'delivery_fee',
        'grand_total',
        'delivery_latitude',
        'delivery_longitude',

        // FILE UPLOADS
        'ktp_file',
        'npwp_file',

        // DISCOUNTS
        'discount_type',
        'discount_value',
        'discount_amount',

        // INVOICE DATE
        'invoice_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(CustomerRequestDetail::class);
    }

    public function approvals()
    {
        return $this->hasMany(CustomerRequestApproval::class);
    }
}
