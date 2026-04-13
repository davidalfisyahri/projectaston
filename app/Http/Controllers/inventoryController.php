<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::all();
        $grade = Grade::with('composition.inventory')->get();

        return view('inventory', compact('inventory', 'grade'));
    }

    public function store(Request $request)
{
    $inv = Inventory::create($request->all());
    return response()->json($inv);
}

public function update(Request $request, $id)
{
    $inv = Inventory::findOrFail($id);
    $inv->update($request->all());

    return response()->json(['success'=>true]);
}

public function destroy($id)
{
    Inventory::findOrFail($id)->delete();
    return response()->json(['success'=>true]);
}
}