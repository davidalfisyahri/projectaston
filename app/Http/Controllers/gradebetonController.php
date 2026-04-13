<?php

namespace App\Http\Controllers;

use App\Models\grade;
use App\Models\composition;
use Illuminate\Http\Request;

class gradebetonController extends Controller
{
    public function store(Request $request)
    {
        // VALIDASI (optional tapi bagus)
        $request->validate([
            'name_grade' => 'required',
            'mpa' => 'required'
        ]);

        $grade = grade::create([
            'name_grade' => $request->name_grade,
            'mpa' => $request->mpa,
            'harga_fa' => $request->harga_fa,
            'harga_nfa' => $request->harga_nfa,
        ]);

        if($request->composition){
            foreach($request->composition as $c){
                composition::create([
                    'grade_id' => $grade->id_grade,
                    'inventory_id' => $c['inventory_id'],
                    'qty' => $c['qty']
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $grade = grade::findOrFail($id);

        $grade->update([
            'name_grade' => $request->name_grade,
            'mpa' => $request->mpa,
            'harga_fa' => $request->harga_fa,
            'harga_nfa' => $request->harga_nfa,
        ]);

        // hapus lama
        composition::where('grade_id', $id)->delete();

        if($request->composition){
            foreach ($request->composition as $comp) {
                composition::create([
                    'grade_id' => $id,
                    'inventory_id' => $comp['inventory_id'],
                    'qty' => $comp['qty'],
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        grade::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}