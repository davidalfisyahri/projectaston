<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GradeBeton;
use App\Models\inventory;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::all();
        $grade = GradeBeton::with('composition.inventory')->get();

        return view('inventory', compact('inventory', 'grade'));
    }

    public function store(Request $request)
    {
        $inv = inventory::create([
            'name_material' => $request->name_material,
            'type' => $request->type,
            'stock' => $request->stock,
        ]);

        return response()->json($inv);
    }

    public function update(Request $request, $id)
    {
        $inv = inventory::findOrFail($id);

        $inv->update([
            'name_material' => $request->name_material,
            'type' => $request->type,
            'stock' => $request->stock,
        ]);

        return redirect('/inventory');
    }

    public function destroy($id)
    {
        inventory::findOrFail($id)->delete();

        return redirect('/inventory');
    }
}