<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\CustomerRequestApproval;
use App\Models\PurchaseOrder;
use App\Models\Composition;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Halaman utama approval — tampilkan semua CR & PO yang pending.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $crQuery = CustomerRequest::with(['details.grade', 'user', 'approvals']);
        if ($search) {
            $crQuery->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $poQuery = PurchaseOrder::with(['supplier', 'details.inventory']);
        if ($search) {
            $poQuery->where(function ($q) use ($search) {
                $q->where('no_po', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name_pt', 'like', "%{$search}%");
                    });
            });
        }

        $crPending = (clone $crQuery)->where('status', 'waiting_approval')
            ->latest()
            ->paginate(10, ['*'], 'cr_pending')
            ->appends($request->query());

        $crHistory = (clone $crQuery)->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(10, ['*'], 'cr_history')
            ->appends($request->query());

        $poPending = (clone $poQuery)->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'po_pending')
            ->appends($request->query());

        $poHistory = (clone $poQuery)->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(10, ['*'], 'po_history')
            ->appends($request->query());

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
            'status' => $request->action, // approved / rejected
            'approved_by' => $user->id_user,
            'approved_at' => now(),
        ]);

        if ($request->action == 'rejected') {
            $data->update(['status' => 'rejected']);
        } else {
            // Cukup salah satu direktur/wakil direktur approve, request langsung approved
            $data->update(['status' => 'approved']);

            // PENGURANGAN INVENTORY (tetap dikurangi meskipun stok mungkin tidak mencukupi)
            $required_stock = [];
            $details = CustomerRequestDetail::where('customer_request_id', $id)->get();
            foreach ($details as $detail) {
                $qty_ordered = $detail->qty;
                $compositions = Composition::where('grade_id', $detail->grade_id)->get();
                foreach ($compositions as $comp) {
                    $inv_id = $comp->inventory_id;
                    if (!isset($required_stock[$inv_id])) {
                        $required_stock[$inv_id] = 0;
                    }
                    $required_stock[$inv_id] += ($comp->qty * $qty_ordered);
                }
            }

            foreach ($required_stock as $inv_id => $needed) {
                $inventory = Inventory::find($inv_id);
                if ($inventory) {
                    $inventory->stock -= $needed;
                    $inventory->save();
                }
            }
        }

        return back()->with('success', 'Customer Request berhasil di-' . $request->action . '.');
    }

    /**
     * Approve / Reject Procurement (Purchase Order).
     */
    public function approveProcurement(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        $po->update([
            'status' => $request->action, // approved / rejected
            'approved_by' => Auth::user()->id_user,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Procurement berhasil di-' . $request->action . '.');
    }
}
