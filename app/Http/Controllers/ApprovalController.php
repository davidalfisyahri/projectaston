<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestApproval;
use App\Models\purchase_order;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Halaman utama approval — tampilkan semua CR & PO yang pending.
     */
    public function index()
    {
        $crPending = CustomerRequest::with(['details.grade', 'user', 'approvals'])
            ->where('status', 'waiting_approval')
            ->latest()
            ->paginate(10, ['*'], 'cr_pending');

        $crHistory = CustomerRequest::with(['details.grade', 'user', 'approvals'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(10, ['*'], 'cr_history');

        $poPending = purchase_order::with(['supplier', 'details.inventory'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'po_pending');

        $poHistory = purchase_order::with(['supplier', 'details.inventory'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(10, ['*'], 'po_history');

        return view('approval', compact('crPending', 'crHistory', 'poPending', 'poHistory'));
    }

    /**
     * Approve / Reject Customer Request.
     */
    public function approveCustomerRequest(Request $request, $id)
    {
        $data = CustomerRequest::findOrFail($id);
        $user = Auth::user();

        // Cari approval row berdasarkan position user
        $approval = CustomerRequestApproval::where('customer_request_id', $id)
            ->where('role', $user->position)
            ->first();

        if (!$approval) {
            return back()->with('error', 'Anda tidak memiliki hak approval untuk request ini.');
        }

        $approval->update([
            'status'      => $request->action, // approved / rejected
            'approved_by' => $user->id_user,
            'approved_at' => now(),
        ]);

        if ($request->action == 'rejected') {
            $data->update(['status' => 'rejected']);
        } else {
            // Cukup salah satu direktur/wakil direktur approve, request langsung approved
            $data->update(['status' => 'approved']);
        }

        return back()->with('success', 'Customer Request berhasil di-' . $request->action . '.');
    }

    /**
     * Approve / Reject Procurement (Purchase Order).
     */
    public function approveProcurement(Request $request, $id)
    {
        $po = purchase_order::findOrFail($id);

        $po->update([
            'status'      => $request->action, // approved / rejected
            'approved_by' => Auth::user()->id_user,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Procurement berhasil di-' . $request->action . '.');
    }
}
