<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GradeBeton;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\CustomerRequestApproval;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerRequest::with('details.grade')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name_user', 'like', "%{$search}%");
                  });
            });
        }

        $pendingQuery = clone $query;
        $historyQuery = clone $query;

        $pendingQuery->whereIn('status', ['draft', 'waiting_approval']);
        $historyQuery->whereIn('status', ['approved', 'rejected', 'done']);

        $pendingCR = $pendingQuery->paginate(5, ['*'], 'pendingPage')->appends($request->query());
        $historyCR = $historyQuery->paginate(10, ['*'], 'historyPage')->appends($request->query());
        $grades = GradeBeton::all();

        // 🔥 TAMBAHAN INI
        $projects = CustomerRequest::select('ongoing_project')
            ->whereNotNull('ongoing_project')
            ->where('ongoing_project', '!=', '')
            ->distinct()
            ->get();

        return view('customer_req', compact('pendingCR', 'historyCR', 'grades', 'projects'));
    }

    public function store(Request $request)
    {
        $req = CustomerRequest::create([
            'request_code' => 'REQ-' . date('Ymd') . rand(100, 999),
            'created_by' => Auth::id(),
            'tanggal' => now(),

            // IDENTITAS
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'region' => $request->region,
            'customer_number' => $request->customer_number,
            'note' => $request->note,

            // PROFIL BISNIS
            'no_identitas' => $request->no_identitas,
            'form_business' => $request->form_business,
            'business_ownership' => $request->business_ownership,
            'section_business' => $request->section_business,
            'address_business' => $request->address_business,

            // PAJAK
            'npwp' => $request->npwp,
            'tax_name' => $request->tax_name,
            'tax_address' => $request->tax_address,

            // IZIN
            'izin_tdp' => $request->izin_tdp,
            'tdp_date' => $request->tdp_date,
            'izin_siup' => $request->izin_siup,
            'siup_date' => $request->siup_date,
            'izin_sio' => $request->izin_sio,
            'sio_date' => $request->sio_date,

            // OWNER
            'owner_name' => $request->owner_name,
            'owner_address' => $request->owner_address,
            'email' => $request->email,

            // PROJECT
            'office_address' => $request->office_address,
            'ongoing_project' => $request->ongoing_project,

            'status' => 'waiting_approval',

            //Schedule
            'schedule_date' => $request->schedule_date,
        ]);

        // DETAIL
        if ($request->grade_id) {
            foreach ($request->grade_id as $i => $g) {

                $price = str_replace('.', '', $request->price[$i]); // 🔥 FIX

                CustomerRequestDetail::create([
                    'customer_request_id' => $req->id,
                    'grade_id' => $g,
                    'type' => $request->type[$i],
                    'qty' => $request->qty[$i],
                    'price' => $price,
                    'total' => $request->qty[$i] * $price,
                ]);
            }
        }

        // 🔥 BUAT APPROVAL ROWS (untuk direktur & wakil direktur)
        foreach (['wakil_direktur', 'direktur_utama'] as $role) {
            CustomerRequestApproval::create([
                'customer_request_id' => $req->id,
                'role' => $role,
                'status' => 'pending',
            ]);
        }

        return back();
    }

    public function approve(Request $request, $id)
    {
        $data = CustomerRequest::findOrFail($id);

        $approval = CustomerRequestApproval::where('customer_request_id', $id)
            ->where('role', Auth::user()->role)
            ->first();

        $approval->update([
            'status' => $request->action,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        if ($request->action == 'rejected') {
            $data->update(['status' => 'rejected']);
        } else {
            $check = CustomerRequestApproval::where('customer_request_id', $id)
                ->where('status', '!=', 'approved')
                ->count();

            if ($check == 0) {
                $data->update(['status' => 'approved']);
            }
        }

        return back();
    }

    public function pay($id)
    {
        CustomerRequest::find($id)->update([
            'is_paid' => true,
            'status' => 'paid'
        ]);

        return back();
    }

    public function confirmWa($id)
    {
        CustomerRequest::find($id)->update([
            'is_wa_confirmed' => true,
            'status' => 'confirmed_wa'
        ]);

        return back();
    }

    public function pdf($id)
    {
        $data = CustomerRequest::with([
            'details.grade',
            'user',
            'approvals.user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.customer_request', compact('data'))
            ->setPaper('A4', 'portrait');

        if (request('download')) {
            return $pdf->download('customer_request_' . $data->request_code . '.pdf');
        }

        return $pdf->stream();
    }

    public function schedule(Request $request, $id)
    {
        CustomerRequest::find($id)->update([
            'schedule_date' => $request->schedule_date,
            'status' => 'scheduled'
        ]);

        return back();
    }

    public function destroy($id)
    {
        $data = CustomerRequest::findOrFail($id);
        CustomerRequestDetail::where('customer_request_id', $id)->delete();
        CustomerRequestApproval::where('customer_request_id', $id)->delete();
        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function markAsDone($id)
    {
        $cr = CustomerRequest::findOrFail($id);
        
        if ($cr->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya Customer Request dengan status Approved yang dapat ditandai selesai.');
        }

        $cr->status = 'done';
        $cr->save();

        return redirect()->back()->with('success', 'Customer Request berhasil ditandai sebagai Selesai (Done).');
    }
}
