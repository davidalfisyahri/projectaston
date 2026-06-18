<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryTariff;

class DeliveryTariffController extends Controller
{
    /**
     * API: Return semua tarif aktif (JSON) untuk JavaScript di form.
     */
    public function getTariffs()
    {
        $tariffs = DeliveryTariff::where('is_active', true)
            ->orderBy('min_km')
            ->get();

        return response()->json($tariffs);
    }

    /**
     * Bulk update semua tarif dari halaman setting.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengubah tarif pengiriman.');
        }

        $request->validate([
            'tariffs' => 'required|array',
            'tariffs.*.id' => 'required|exists:delivery_tariffs,id',
            'tariffs.*.min_km' => 'required|numeric|min:0',
            'tariffs.*.max_km' => 'required|numeric|min:0',
            'tariffs.*.fee' => 'required|numeric|min:0',
        ]);

        foreach ($request->tariffs as $data) {
            DeliveryTariff::where('id', $data['id'])->update([
                'min_km' => $data['min_km'],
                'max_km' => $data['max_km'],
                'fee' => $data['fee'],
                'label' => $data['min_km'] . ' - ' . $data['max_km'] . ' km' . ($data['fee'] == 0 ? ' (Gratis)' : ''),
            ]);
        }

        return back()->with('success', 'Tarif pengiriman berhasil diperbarui!');
    }

    /**
     * Tambah tier baru.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menambah tarif pengiriman.');
        }

        $request->validate([
            'min_km' => 'required|numeric|min:0',
            'max_km' => 'required|numeric|gt:min_km',
            'fee' => 'required|numeric|min:0',
        ]);

        DeliveryTariff::create([
            'min_km' => $request->min_km,
            'max_km' => $request->max_km,
            'fee' => $request->fee,
            'label' => $request->min_km . ' - ' . $request->max_km . ' km' . ($request->fee == 0 ? ' (Gratis)' : ''),
            'is_active' => true,
        ]);

        return back()->with('success', 'Tarif baru berhasil ditambahkan!');
    }

    /**
     * Hapus tier.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menghapus tarif pengiriman.');
        }

        DeliveryTariff::findOrFail($id)->delete();
        return back()->with('success', 'Tarif berhasil dihapus!');
    }
}
