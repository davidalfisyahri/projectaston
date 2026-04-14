<?php

namespace App\Http\Controllers;

use App\Models\grade;
use App\Models\composition;
use App\Models\gradebeton;
use Illuminate\Http\Request;

class GradebetonController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'name_grade' => 'required',
        'mpa' => 'required|string',
        'harga_fa' => 'required',
        'harga_nfa' => 'required',
    ]);

    $grade = GradeBeton::create([
        'name_grade' => $request->name_grade,
        'mpa' => $request->mpa,
        'harga_fa' => str_replace('.', '', $request->harga_fa),
        'harga_nfa' => str_replace('.', '', $request->harga_nfa),
    ]);

    // ✅ CEK DULU ADA COMPOSITION ATAU TIDAK
    if ($request->inventory_id) {
        foreach ($request->inventory_id as $key => $inv) {

            // skip kalau kosong
            if (!$inv || !$request->qty[$key]) continue;

            Composition::create([
                'grade_id' => $grade->id_grade,
                'inventory_id' => $inv,
                'qty' => $request->qty[$key],
            ]);
        }
    }

    return back();
}

public function update(Request $request, $id)
{
    $grade = GradeBeton::find($id);

    $grade->update([
        'name_grade' => $request->name_grade,
        'mpa' => $request->mpa,
        'harga_fa' => str_replace('.', '', $request->harga_fa),
        'harga_nfa' => str_replace('.', '', $request->harga_nfa),
    ]);

    // hapus lama
    Composition::where('grade_id', $id)->delete();

    // insert kalau ada
    if ($request->inventory_id) {
        foreach ($request->inventory_id as $key => $inv) {

            if (!$inv || !$request->qty[$key]) continue;

            Composition::create([
                'grade_id' => $id,
                'inventory_id' => $inv,
                'qty' => $request->qty[$key],
            ]);
        }
    }

    return redirect('/inventory');
}


    public function destroy($id)
    {
        GradeBeton::find($id)->delete();
        return back();
    }
}