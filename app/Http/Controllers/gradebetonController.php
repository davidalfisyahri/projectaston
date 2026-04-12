<?php

namespace App\Http\Controllers;
use App\Models\grade;
use App\Models\composition;
use Illuminate\Http\Request;

class gradebetonController extends Controller
{
    
public function store(Request $request)
{
    $grade = grade::create([
        'name' => $request->name,
        'fc' => $request->fc,
        'harga_fa' => $request->harga_fa,
        'harga_nfa' => $request->harga_nfa,
    ]);

    foreach($request->compositions as $c){
        composition::create([
            'grade_id' => $grade->id_grade,
            'inventory_id' => $c['inventory_id'],
            'qty' => $c['qty']
        ]);
    }

    return redirect('/inventory')->json(['success' => true]);
}

public function update(Request $request, $id)
{
    $grade = grade::findOrFail($id);

    $grade->update([
        'name' => $request->name,
        'fc' => $request->fc,
        'harga_fa' => $request->harga_fa,
        'harga_nfa' => $request->harga_nfa,
    ]);

    // hapus komposisi lama
    Composition::where('grade_id', $id)->delete();

    // insert ulang
    foreach ($request->composition as $comp) {
        Composition::create([
            'grade_id' => $id,
            'inventory_id' => $comp['inventory_id'],
            'qty' => $comp['qty'],
        ]);
    }

    return redirect('/inventory');
}

public function destroy($id)
{
    grade::findOrFail($id)->delete(); // otomatis hapus composition (cascade)

    return redirect('/inventory');
}

}
