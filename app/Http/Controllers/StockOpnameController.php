<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $inventories = Inventory::whereNotIn('type', ['utility', 'Water', 'Slump'])
            ->whereNotIn('name_material', ['Air', 'Air PAM', 'Water'])
            ->get();

        $query = StockOpname::with(['inventory', 'checker'])->orderBy('created_at', 'desc');

        // Filter by date
        if ($request->date) {
            $query->whereDate('opname_date', $request->date);
        }

        // Filter by material
        if ($request->material) {
            $query->where('inventory_id', $request->material);
        }

        $history = $query->paginate(10)->appends($request->query());

        return view('stock_opname', compact('inventories', 'history'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'superadmin' && $request->user()->position === 'direktur_utama') {
            abort(403, 'Direktur Utama hanya memiliki akses lihat di halaman ini.');
        }

        $items = $request->input('items', []);
        foreach ($items as $key => $item) {
            if (isset($item['stock_actual']) && is_string($item['stock_actual'])) {
                $items[$key]['stock_actual'] = str_replace(',', '.', $item['stock_actual']);
            }
        }
        $request->merge(['items' => $items]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventory,id_inventory',
            'items.*.stock_actual' => 'required|numeric|min:0',
            'opname_date' => 'required|date',
        ], [
            'items.required' => 'Minimal 1 material harus diisi.',
            'items.*.stock_actual.required' => 'Stok aktual wajib diisi.',
            'items.*.stock_actual.numeric' => 'Stok aktual harus berupa angka.',
            'items.*.stock_actual.min' => 'Stok aktual tidak boleh negatif.',
            'opname_date.required' => 'Tanggal opname wajib diisi.',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $inventory = Inventory::find($item['inventory_id']);
                if (!$inventory) continue;

                $stockSystem = $inventory->stock;
                $stockActual = (float) $item['stock_actual'];
                $difference = $stockActual - $stockSystem;

                StockOpname::create([
                    'inventory_id' => $item['inventory_id'],
                    'stock_system' => $stockSystem,
                    'stock_actual' => $stockActual,
                    'difference' => $difference,
                    'notes' => $item['notes'] ?? null,
                    'opname_date' => $request->opname_date,
                    'checked_by' => $request->user()->id_user,
                ]);

                // Update stok di inventory sesuai stok aktual
                $inventory->update(['stock' => $stockActual]);
            }
        });

        return redirect('/stock-opname')->with('success', 'Stock opname berhasil disimpan dan stok telah diperbarui.');
    }
}
