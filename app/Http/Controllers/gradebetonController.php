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
        'harga_fa' => 'required|numeric',
        'harga_nfa' => 'required|numeric',
    ]);

    $grade = GradeBeton::create([
        'name_grade' => $request->name_grade,
        'mpa' => $request->mpa,
        'harga_fa' => $request->harga_fa,
        'harga_nfa' => $request->harga_nfa,
    ]);

    foreach ($request->inventory_id as $key => $inv) {
        Composition::create([
            'grade_id' => $grade->id_grade,
            'inventory_id' => $inv,
            'qty' => $request->qty[$key],
        ]);
    }

    return back();
}

    public function destroy($id)
    {
        GradeBeton::find($id)->delete();
        return back();
    }
}