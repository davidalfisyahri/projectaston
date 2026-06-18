<?php

namespace App\Http\Controllers;

use App\Models\grade;
use App\Models\composition;
use App\Models\gradebeton;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradebetonController extends Controller
{
    public function store(Request $request)
{
    if (auth()->user()->role !== 'superadmin' && auth()->user()->position === 'direktur_utama') {
        abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
    }

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
    if (auth()->user()->role !== 'superadmin' && auth()->user()->position === 'direktur_utama') {
        abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
    }

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
        if (auth()->user()->role !== 'superadmin' && auth()->user()->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        GradeBeton::find($id)->delete();
        return back();
    }

    public function bulkStore(Request $request)
    {
        if (auth()->user()->role !== 'superadmin' && auth()->user()->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        $request->validate([
            'grades'                    => 'required|array',
            'grades.*.name_grade'       => 'required|string',
            'grades.*.mpa'              => 'nullable|string',
            'grades.*.harga_fa'         => 'nullable|numeric',
            'grades.*.harga_nfa'        => 'nullable|numeric',
            'grades.*.recipe_type'      => 'nullable|string',
            'grades.*.compositions'     => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->grades as $gradeData) {
                $hasCompositions = !empty($gradeData['compositions']);
                $recipeType      = strtoupper(trim($gradeData['recipe_type'] ?? ''));

                // ── 1. Upsert the grade row ──────────────────────────────────────
                $grade = GradeBeton::firstOrCreate(
                    ['name_grade' => $gradeData['name_grade']],
                    [
                        'mpa'       => $gradeData['mpa'] ?? '-',
                        'harga_fa'  => $gradeData['harga_fa']  ?? 0,
                        'harga_nfa' => $gradeData['harga_nfa'] ?? 0,
                    ]
                );

                // Always update prices / mpa if provided
                $grade->update([
                    'mpa'       => ($gradeData['mpa'] ?? '-') !== '-' ? $gradeData['mpa'] : $grade->mpa,
                    'harga_fa'  => ($gradeData['harga_fa']  ?? 0) > 0 ? $gradeData['harga_fa']  : $grade->harga_fa,
                    'harga_nfa' => ($gradeData['harga_nfa'] ?? 0) > 0 ? $gradeData['harga_nfa'] : $grade->harga_nfa,
                ]);

                // ── 2. Handle compositions ───────────────────────────────────────
                // Price-only import (no compositions) → skip, never touch compositions
                if (!$hasCompositions) {
                    continue;
                }

                // Recipe import: delete the matching slice, then re-insert
                $deleteQuery = Composition::where('grade_id', $grade->id_grade);
                if (in_array($recipeType, ['FA', 'NFA'])) {
                    // Only wipe compositions of THIS recipe type
                    $deleteQuery->where('recipe_type', $recipeType);
                }
                $deleteQuery->delete();

                foreach ($gradeData['compositions'] as $materialName => $qty) {
                    if (!$qty) continue;

                    // Determine inventory type from material name
                    $invType   = 'Admixture';
                    $lowerName = strtolower(trim($materialName));
                    if (str_contains($lowerName, 'semen') || str_contains($lowerName, 'cement')) {
                        $invType = 'cement';
                    } elseif (str_contains($lowerName, 'fly ash') || preg_match('/\bfa\b/', $lowerName)) {
                        $invType = 'FA';
                    } elseif (str_contains($lowerName, 'pasir') || str_contains($lowerName, 'sand')) {
                        $invType = 'Sand';
                    } elseif (str_contains($lowerName, 'split') || str_contains($lowerName, 'batu') || str_contains($lowerName, 'aggregate')) {
                        $invType = 'Aggregate';
                    }

                    $inventory = Inventory::firstOrCreate(
                        ['name_material' => strtoupper(trim($materialName))],
                        ['type' => $invType, 'stock' => 0]
                    );

                    Composition::create([
                        'grade_id'    => $grade->id_grade,
                        'inventory_id'=> $inventory->id_inventory,
                        'recipe_type' => $recipeType ?: null,
                        'qty'         => $qty,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil import '.count($request->grades).' data mutu beton.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal import data: ' . $e->getMessage()], 500);
        }
    }
}