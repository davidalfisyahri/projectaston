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
    public function index()
    {
        $data = CustomerRequest::with('details.grade')->latest()->get();
        $grades = GradeBeton::all();

        return view('customer_req', compact('data','grades'));
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

            'region' => $request->region,
            'customer_number' => $request->customer_number,
            'note' => $request->note,

            'status' => 'waiting_approval'
        ]);

        // 🔥 SIMPAN DETAIL
        if($request->grade_id){
            foreach($request->grade_id as $i => $g){
                CustomerRequestDetail::create([
                    'customer_request_id' => $req->id,
                    'grade_id' => $g,
                    'type' => $request->type[$i],
                    'qty' => $request->qty[$i],
                    'price' => $request->price[$i],
                    'total' => $request->qty[$i] * $request->price[$i],
                ]);
            }
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

    public function pdf($id)
    {
        $data = CustomerRequest::with(['details.grade','user'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.customer_request', compact('data'))
                ->setPaper('A4', 'portrait');

        if(request('download')){
            return $pdf->download('customer_request_'.$data->request_code.'.pdf');
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
    
}
