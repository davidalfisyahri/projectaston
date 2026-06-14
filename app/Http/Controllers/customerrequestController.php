<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GradeBeton;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\CustomerRequestApproval;
use App\Models\DeliveryTariff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerRequestController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->position === 'kepala_plant') {
            return redirect()->route('plant_schedule');
        }

        $query = CustomerRequest::with('details.grade')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name_user', 'like', "%{$search}%");
                  });
            });
        }

        $pendingQuery = clone $query;
        $historyQuery = clone $query;

        $pendingQuery->whereIn('status', ['draft', 'waiting_approval']);
        $historyQuery->whereIn('status', ['approved', 'paid', 'confirmed_wa', 'scheduled', 'rejected', 'done']);

        $pendingCR = $pendingQuery->paginate(5, ['*'], 'pendingPage')->appends($request->query());
        $historyCR = $historyQuery->paginate(10, ['*'], 'historyPage')->appends($request->query());
        $grades = GradeBeton::all();

        // 🔥 TAMBAHAN INI
        $projects = CustomerRequest::select('ongoing_project')
            ->whereNotNull('ongoing_project')
            ->where('ongoing_project', '!=', '')
            ->distinct()
            ->get();

        $tariffs = DeliveryTariff::where('is_active', true)->orderBy('min_km')->get();

        return view('customer_req', compact('pendingCR', 'historyCR', 'grades', 'projects', 'tariffs'));
    }

    public function store(Request $request)
    {
        $req = CustomerRequest::create([
            'request_code' => 'REQ-' . date('Ymd') . rand(100, 999),
            'created_by' => Auth::id(),
            'tanggal' => now(),

            // IDENTITAS
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'region' => $request->region,
            'customer_number' => $request->customer_number,
            'note' => $request->note,

            // PROFIL BISNIS
            'no_identitas' => $request->no_identitas,
            'form_business' => $request->form_business,
            'business_ownership' => $request->business_ownership,
            'section_business' => $request->section_business,
            'address_business' => $request->address_business,

            // PAJAK
            'npwp' => $request->npwp,
            'tax_name' => $request->tax_name,
            'tax_address' => $request->tax_address,

            // IZIN
            'izin_tdp' => $request->izin_tdp,
            'tdp_date' => $request->tdp_date,
            'izin_siup' => $request->izin_siup,
            'siup_date' => $request->siup_date,
            'izin_sio' => $request->izin_sio,
            'sio_date' => $request->sio_date,

            // OWNER
            'owner_name' => $request->owner_name,
            'owner_address' => $request->owner_address,
            'email' => $request->email,

            // PROJECT
            'office_address' => $request->office_address,
            'ongoing_project' => $request->ongoing_project,

            'status' => 'waiting_approval',

            //Schedule
            'schedule_date' => $request->schedule_date,

            // DELIVERY
            'delivery_distance' => $request->delivery_distance,
            'delivery_fee' => 0,
            'grand_total' => 0,
            'delivery_latitude' => $request->delivery_latitude,
            'delivery_longitude' => $request->delivery_longitude,
        ]);

        // DETAIL
        $itemsTotal = 0;
        if ($request->grade_id) {
            foreach ($request->grade_id as $i => $g) {

                $price = str_replace('.', '', $request->price[$i]); // 🔥 FIX
                $lineTotal = $request->qty[$i] * $price;

                CustomerRequestDetail::create([
                    'customer_request_id' => $req->id,
                    'grade_id' => $g,
                    'type' => $request->type[$i],
                    'qty' => $request->qty[$i],
                    'price' => $price,
                    'total' => $lineTotal,
                ]);

                $itemsTotal += $lineTotal;
            }
        }

        // 🔥 HITUNG DELIVERY FEE
        $distance = floatval($request->delivery_distance ?? 0);
        $maxKm = DeliveryTariff::getMaxDistance();

        if ($distance > $maxKm && $request->delivery_fee_custom) {
            // Jarak > max tier → pakai input manual dari sales
            $deliveryFee = floatval(str_replace('.', '', $request->delivery_fee_custom));
        } else {
            // Dalam range tarif → hitung otomatis
            $deliveryFee = DeliveryTariff::getFeeByDistance($distance) ?? 0;
        }

        $req->update([
            'delivery_fee' => $deliveryFee,
            'grand_total' => $itemsTotal + $deliveryFee,
        ]);

        // 🔥 BUAT APPROVAL ROWS (untuk direktur & wakil direktur)
        foreach (['wakil_direktur', 'direktur_utama'] as $role) {
            CustomerRequestApproval::create([
                'customer_request_id' => $req->id,
                'role' => $role,
                'status' => 'pending',
            ]);
        }

        return back();
    }

    public function approve(Request $request, $id)
    {
        $data = CustomerRequest::findOrFail($id);

        $approval = CustomerRequestApproval::where('customer_request_id', $id)
            ->where('role', Auth::user()->role)
            ->first();

        $approval->update([
            'status' => $request->action,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        if ($request->action == 'rejected') {
            $data->update(['status' => 'rejected']);
        } else {
            $check = CustomerRequestApproval::where('customer_request_id', $id)
                ->where('status', '!=', 'approved')
                ->count();

            if ($check == 0) {
                $data->update(['status' => 'approved']);
            }
        }

        return back();
    }

    public function pay($id)
    {
        CustomerRequest::find($id)->update([
            'is_paid' => true,
            'status' => 'paid'
        ]);

        return back();
    }

    public function confirmWa($id)
    {
        CustomerRequest::find($id)->update([
            'is_wa_confirmed' => true,
            'status' => 'confirmed_wa'
        ]);

        return back();
    }

    public function pdf($id)
    {
        $data = CustomerRequest::with([
            'details.grade',
            'user',
            'approvals.user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.customer_request', compact('data'))
            ->setPaper('A4', 'portrait');

        if (request('download')) {
            return $pdf->download('customer_request_' . $data->request_code . '.pdf');
        }

        return $pdf->stream();
    }

    public function schedule(Request $request, $id)
    {
        CustomerRequest::find($id)->update([
            'schedule_date' => $request->schedule_date,
            'status' => 'scheduled'
        ]);

        return back();
    }

    public function destroy($id)
    {
        $data = CustomerRequest::findOrFail($id);
        CustomerRequestDetail::where('customer_request_id', $id)->delete();
        CustomerRequestApproval::where('customer_request_id', $id)->delete();
        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function markAsDone($id)
    {
        $cr = CustomerRequest::findOrFail($id);
        
        if (!in_array($cr->status, ['approved', 'paid', 'confirmed_wa', 'scheduled'])) {
            return redirect()->back()->with('error', 'Pesanan belum dapat ditandai selesai pada status saat ini.');
        }

        $cr->status = 'done';
        $cr->save();

        return redirect()->back()->with('success', 'Customer Request berhasil ditandai sebagai Selesai (Done).');
    }

    /**
     * Resolves Google Maps short URL and extracts coordinates.
     */
    public function resolveMapsUrl(Request $request)
    {
        $request->validate(['url' => 'required']);
        $url = $request->url;

        // Extract the URL first in case there is trailing/leading text
        if (preg_match('/(https?:\/\/[^\s]+)/', $url, $urlMatches)) {
            $url = $urlMatches[1];
        }

        // Helper logic to try and parse coordinates directly from any given URL string
        $parseCoordinates = function($targetUrl) {
            // 1. Try to find coordinates in query string q or ll
            $parsed = parse_url($targetUrl);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $queryParams);
                if (isset($queryParams['q']) && preg_match('/(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $queryParams['q'], $matches)) {
                    return [floatval($matches[1]), floatval($matches[2])];
                }
                if (isset($queryParams['ll']) && preg_match('/(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $queryParams['ll'], $matches)) {
                    return [floatval($matches[1]), floatval($matches[2])];
                }
            }

            // 2. Try to find coordinates in path (e.g. /place/lat,lng or @lat,lng)
            if (preg_match('/(?:place|search|\@|place\/|search\/)(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $targetUrl, $matches)) {
                return [floatval($matches[1]), floatval($matches[2])];
            }

            // 3. Try to find coordinates in !3d/!4d format
            if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $targetUrl, $matches)) {
                return [floatval($matches[1]), floatval($matches[2])];
            }

            return null;
        };

        // If coordinates are already present in the URL, return them directly
        $directCoords = $parseCoordinates($url);
        if ($directCoords) {
            return response()->json([
                'success' => true,
                'latitude' => $directCoords[0],
                'longitude' => $directCoords[1]
            ]);
        }

        try {
            // Guzzle / Http client automatically follows redirects
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->timeout(5)->get($url);

            $finalUrl = $response->effectiveUri()->__toString();

            // Try to parse coordinates from the final redirected URL
            $coords = $parseCoordinates($finalUrl);

            if ($coords) {
                return response()->json([
                    'success' => true,
                    'latitude' => $coords[0],
                    'longitude' => $coords[1]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Koordinat tidak ditemukan di dalam link tersebut. Pastikan link adalah Google Maps Share Location.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal membaca link Google Maps: ' . $e->getMessage()
            ]);
        }
    }

    public function spkPdf($id)
    {
        $data = CustomerRequest::with([
            'details.grade',
            'user',
            'approvals.user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.spk', compact('data'))
            ->setPaper('A4', 'portrait');

        if (request('download')) {
            return $pdf->download('SPK_' . $data->request_code . '.pdf');
        }

        return $pdf->stream();
    }

    public function plantSchedule(Request $request)
    {
        $query = CustomerRequest::with('details.grade')
            ->whereIn('status', ['approved', 'paid', 'confirmed_wa', 'scheduled', 'done']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('schedule_date', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('plant_schedule', compact('schedules'));
    }
}
