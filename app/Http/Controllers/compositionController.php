<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\grade;
use App\Models\composition;

class compositionController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'fc' => 'required',
    ]);

    $grade = Grade::create([
        'name' => $request->name,
        'fc' => $request->fc,
        'harga_fa' => $request->harga_fa,
        'harga_nfa' => $request->harga_nfa,
    ]);

    // SIMPAN KOMPOSISI
    foreach ($request->composition as $item) {
        Composition::create([
            'grade_id' => $grade->id_grade,
            'inventory_id' => $item['inventory_id'],
            'qty' => $item['qty']
        ]);
    }

    return back()->with('success', 'Grade berhasil ditambahkan');
}

public function update(Request $request, $id)
{
    $grade = Grade::findOrFail($id);

    $grade->update([
        'name' => $request->name,
        'fc' => $request->fc,
        'harga_fa' => $request->harga_fa,
        'harga_nfa' => $request->harga_nfa,
    ]);

    // HAPUS KOMPOSISI LAMA
    Composition::where('grade_id', $grade->id_grade)->delete();

    // INSERT ULANG
    foreach ($request->composition as $item) {
        Composition::create([
            'grade_id' => $grade->id_grade,
            'inventory_id' => $item['inventory_id'],
            'qty' => $item['qty']
        ]);
    }

    return back()->with('success', 'Grade berhasil diupdate');
}

public function destroy($id)
{
    $grade = Grade::findOrFail($id);

    // otomatis kehapus karena cascade
    $grade->delete();

    return back()->with('success', 'Grade berhasil dihapus');
}

}
