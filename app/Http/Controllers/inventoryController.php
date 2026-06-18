<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\gradebeton;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && $user->position === 'logistik') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $search = $request->search;

        $invQuery = Inventory::query();
        if ($search) {
            $invQuery->where(function($q) use ($search) {
                $q->where('name_material', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        $inventories = $invQuery->paginate(10, ['*'], 'inventory_page')->appends($request->query());

        $gradeQuery = gradebeton::with('composition.inventory');
        if ($search) {
            $gradeQuery->where(function($q) use ($search) {
                $q->where('name_grade', 'like', "%{$search}%")
                  ->orWhere('mpa', 'like', "%{$search}%");
            });
        }
        $grade = $gradeQuery->orderBy('id_grade', 'desc')->paginate(10, ['*'], 'grade_page')->appends($request->query());

        $inventoryList = Inventory::all();

        return view('inventory', compact('inventories', 'grade', 'inventoryList'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && $user->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        Inventory::create([
            'name_material' => $request->name_material,
            'type' => $request->type,
            'stock' => $request->stock ?? 0,
        ]);

        return redirect('/inventory');
    }

    public function update(Request $request, $id)
{
    $user = auth()->user();
    if ($user && $user->role !== 'superadmin' && $user->position === 'direktur_utama') {
        abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
    }

    $inv = Inventory::find($id);

    $inv->update([
        'name_material' => $request->name_material,
        'type' => $request->type,
        'stock' => $request->stock ?? 0,
    ]);

    return redirect('/inventory');
}

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && $user->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        Inventory::find($id)->delete();
        return back();
    }
}