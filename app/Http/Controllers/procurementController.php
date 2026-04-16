<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\purchase_order;
use App\Models\purchase_order_detail;
use App\Models\supplier;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class procurementcontroller extends Controller
{
    public function index()
    {
        $suppliers = supplier::all();
        $po = purchase_order::with('details.inventory','supplier')->latest()->get();
        $inventories = Inventory::all();

        return view('procurement', compact('suppliers','po', 'inventories'));
    }

    public function store(Request $request)
{
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

        $qty = $request->qty[$i];
        $price = str_replace('.', '', $request->price[$i]);
        $subtotal = $qty * $price;

        purchase_order_detail::create([
            'po_id' => $po->id_po,
            'inventory_id' => $inv_id,
            'unit' => $request->unit[$i],
            'qty' => $qty,
            'price' => $price,
            'total' => $subtotal,
        ]);

        // Tambah Stok di Inventory
        $inventory = Inventory::find($inv_id);
        if ($inventory) {
            $inventory->update(['stock' => $inventory->stock + $qty]);
        }

        $total += $subtotal;
    }

    $po->update(['total' => $total]);

    return back();
}

public function pdf($id)
{
    $po = purchase_order::with(['details.inventory', 'supplier'])->findOrFail($id);

    $pdf = Pdf::loadView('procurement_pdf', compact('po'));

    return $pdf->download('PO-'.$po->no_po.'.pdf');
}

public function delete($id)
{
    $po = purchase_order::with('details')->findOrFail($id);

    // Kembalikan Stok (Kurangi stok yang tadinya ditambah)
    foreach ($po->details as $detail) {
        $inventory = Inventory::find($detail->inventory_id);
        if ($inventory) {
            $inventory->update(['stock' => $inventory->stock - $detail->qty]);
        }
    }

    $po->delete();
    return back();
}

}