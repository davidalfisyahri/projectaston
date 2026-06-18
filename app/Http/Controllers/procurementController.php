<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\purchase_order;
use App\Models\purchase_order_detail;
use App\Models\supplier;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class procurementcontroller extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && in_array($user->position, ['sales_internal', 'sales_external', 'kepala_plant'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $suppliers = supplier::all();
        $po = purchase_order::with('details.inventory','supplier')->latest()->get();
        $inventories = Inventory::all();

        return view('procurement', compact('suppliers','po', 'inventories'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'superadmin' && auth()->user()->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        DB::transaction(function () use ($request) {
            // 1. BUAT SUPPLIER OTOMATIS
            $supplier = supplier::create([
                'name_pt' => $request->name_pt,
                'name' => $request->supplier_name,
                'address' => $request->supplier_address,
            ]);

            // 2. BUAT PO
            $po = purchase_order::create([
                'no_po' => $request->no_po,
                'supplier_id' => $supplier->id_supplier, // 🔥 FIX DISINI
                'tanggal' => $request->tanggal,
                'created_by' => $request->created_by,
            ]);

            $total = 0;

            foreach ($request->inventory_id as $i => $inv_id) {

                $qty_input = $request->qty[$i]; // angka yg diketik user
                $unit = $request->unit[$i];
                $price = str_replace('.', '', $request->price[$i]);

                // 🔥 1. KONVERSI KE KG (UNTUK DATABASE)
                if($unit == 'ton'){
                    $qty_db = $qty_input * 1000;
                } else {
                    $qty_db = $qty_input;
                }

                // 🔥 2. HITUNG TOTAL (PAKAI INPUT ASLI, BUKAN KG)
                $subtotal = $qty_input * $price;
            
                purchase_order_detail::create([
                    'po_id' => $po->id_po,
                    'inventory_id' => $inv_id,
                    'unit' => 'kg', // 🔥 SIMPAN SELALU KG
                    'qty' => $qty_db,
                    'price' => $price,
                    'total' => $subtotal,
                ]);
            
                // 🔥 TAMBAH STOCK (SUDAH KG)
                $inventory = Inventory::find($inv_id);
                if ($inventory) {
                    $inventory->update(['stock' => $inventory->stock + $qty_db]);
                }
            
                $total += $subtotal;
            }

            $po->update(['total' => $total]);
        });

        return back();
    }

public function pdf($id)
{
    $po = purchase_order::with(['details.inventory', 'supplier'])->findOrFail($id);

    $pdf = Pdf::loadView('pdf.procurement_pdf', compact('po'));

    // 🔥 kalau ada ?download=1 → download
    if(request('download')){
        return $pdf->download('PO-'.$po->no_po.'.pdf');
    }

    return $pdf->stream('PO-'.$po->no_po.'.pdf');
}


    public function delete($id)
    {
        if (auth()->user()->role !== 'superadmin' && auth()->user()->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        DB::transaction(function () use ($id) {
            $po = purchase_order::with('details')->findOrFail($id);

            // Kembalikan Stok (Kurangi stok yang tadinya ditambah)
            foreach ($po->details as $detail) {
                $inventory = Inventory::find($detail->inventory_id);
                if ($inventory) {
                    $inventory->update(['stock' => $inventory->stock - $detail->qty]);
                }
            }

            $po->delete();
        });

        return back();
    }

}