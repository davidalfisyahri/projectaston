<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestApproval;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerRequestController extends Controller
{
    public function index()
    {
        $data = CustomerRequest::latest()->get();
        return view('customer_req', compact('data'));
    }

    public function store(Request $request)
    {
        $req = CustomerRequest::create([
            'request_code' => 'REQ-'.date('Ymd').rand(100,999),
            'created_by' => Auth::id(),
            'tanggal' => now(),
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'waiting_approval'
        ]);

        // approval auto create
        CustomerRequestApproval::create([
            'customer_request_id' => $req->id,
            'role' => 'wadir'
        ]);

        CustomerRequestApproval::create([
            'customer_request_id' => $req->id,
            'role' => 'direktur'
        ]);

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

        if($request->action == 'rejected'){
            $data->update(['status' => 'rejected']);
        } else {
            $check = CustomerRequestApproval::where('customer_request_id', $id)
                ->where('status','!=','approved')
                ->count();

            if($check == 0){
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

    public function schedule(Request $request, $id)
    {
        CustomerRequest::find($id)->update([
            'schedule_date' => $request->schedule_date,
            'status' => 'scheduled'
        ]);

        return back();
    }

    public function pdf($id)
    {
        $data = CustomerRequest::find($id);

        $pdf = Pdf::loadView('customer_req_pdf', compact('data'));

        return $pdf->download('customer_request.pdf');
    }
}
