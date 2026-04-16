<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\purchase_order;
use App\Models\purchase_order_detail;
use App\Models\supplier;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class procurementcontroller extends Controller
{
    public function index()
    {
        $suppliers = supplier::all();
        $po = purchase_order::with('details','supplier')->latest()->get();

        return view('procurement', compact('suppliers','po'));
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

    foreach ($request->item_name as $i => $item) {

        $qty = $request->qty[$i];
        $price = str_replace('.', '', $request->price[$i]);
        $subtotal = $qty * $price;

        purchase_order_detail::create([
            'po_id' => $po->id_po,
            'item_name' => $item,
            'unit' => $request->unit[$i],
            'qty' => $qty,
            'price' => $price,
            'total' => $subtotal,
        ]);

        $total += $subtotal;
    }

    $po->update(['total' => $total]);

    return back();
}

public function pdf($id)
{
    $po = purchase_order::with('details')->findOrFail($id);

    $pdf = Pdf::loadView('procurement_pdf', compact('po'));

    return $pdf->download('PO-'.$po->no_po.'.pdf');
}

public function delete($id)
{
    purchase_order::findOrFail($id)->delete();
    return back();
}

}