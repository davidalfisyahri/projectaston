<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\CustomerRequestApproval;
use App\Models\GradeBeton;
use App\Models\User;

class CustomerRequestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // ambil user pertama
        $grades = GradeBeton::all();

        for ($i = 1; $i <= 20; $i++) {

            // =====================
            // CUSTOMER REQUEST
            // =====================
            // Buat status bervariasi untuk testing
            $status = 'waiting_approval';
            if ($i <= 5) {
                $status = 'paid';
            } elseif ($i <= 15) {
                $status = 'approved';
            }

            $req = CustomerRequest::create([
                'request_code' => 'REQ-'.date('YmdHis').rand(1000,9999),
                'created_by' => $user->id_user,
                'tanggal' => now(),

                'customer_name' => 'Customer '.$i,
                'phone' => '08123'.rand(100000,999999),
                'region' => 'Jakarta',
                'customer_number' => 'CUST-00'.$i,
                'address' => 'Alamat Customer '.$i,
                'note' => 'Note sample',

                'form_business' => 'PT',
                'business_ownership' => 'milik_sendiri',
                'section_business' => 'Konstruksi',

                'npwp' => '123456789'.$i,
                'tax_name' => 'PT Pajak '.$i,
                'tax_address' => 'Alamat Pajak',

                'owner_name' => 'Owner '.$i,
                'email' => 'customer'.$i.'@mail.com',

                'ongoing_project' => 'Project '.$i,
                'status' => $status,
                'is_paid' => $status == 'paid' ? true : false,
            ]);

            // =====================
            // DETAIL (2-3 ITEM)
            // =====================
            foreach ($grades->random(2) as $g) {

                $qty = rand(1, 10);
                $price = $g->harga_fa ?? 100000;

                CustomerRequestDetail::create([
                    'customer_request_id' => $req->id,
                    'grade_id' => $g->id_grade,
                    'type' => 'fa',
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $qty * $price,
                ]);
            }

            // =====================
            // APPROVAL (2 ROLE)
            // =====================
            $approvalStatus = in_array($status, ['approved', 'paid', 'done']) ? 'approved' : 'pending';

            CustomerRequestApproval::create([
                'customer_request_id' => $req->id,
                'role' => 'wakil_direktur',
                'status' => $approvalStatus,
            ]);

            CustomerRequestApproval::create([
                'customer_request_id' => $req->id,
                'role' => 'direktur_utama',
                'status' => $approvalStatus,
            ]);
        }
    }
}