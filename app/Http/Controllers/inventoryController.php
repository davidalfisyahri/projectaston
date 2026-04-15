<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\gradebeton;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::paginate(10, ['*'], 'inventory_page');
        $grade = gradebeton::with('composition.inventory')->paginate(10, ['*'], 'grade_page');
        $inventoryList = Inventory::all();

        return view('inventory', compact('inventories', 'grade', 'inventoryList'));
    }

    public function store(Request $request)
    {
        Inventory::create([
            'name_material' => $request->name_material,
            'type' => $request->type,
            'stock' => $request->stock ?? 0,
        ]);

        return redirect('/inventory');
    }

    public function update(Request $request, $id)
{
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
        Inventory::find($id)->delete();
        return back();
    }
}