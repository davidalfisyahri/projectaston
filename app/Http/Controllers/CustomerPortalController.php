<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GradeBeton;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestDetail;
use App\Models\CustomerRequestApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CustomerPortalController extends Controller
{
    /**
     * Tampilkan halaman registrasi customer.
     */
    public function showRegister()
    {
        return view('customer.register');
    }

    /**
     * Proses registrasi customer, auto-login, redirect ke dashboard.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name_user' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|max:255|unique:users,email',
            'phone'     => 'required|string|max:30',
            'address'   => 'nullable|string',
            'npwp'      => 'nullable|string|max:100',
            'password'  => 'required|string|min:5|confirmed',
        ], [
            'name_user.required' => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'phone.required'     => 'Nomor telepon wajib diisi.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 5 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name_user'     => $request->name_user,
            'username'      => $request->username,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'npwp'          => $request->npwp,
            'password'      => $request->password,
            'role'          => 'customer',
            'position'      => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/customer/dashboard');
    }

    /**
     * Dashboard utama customer.
     */
    public function dashboard()
    {
        $userId = Auth::id();

        // Statistik
        $totalOrders    = CustomerRequest::where('created_by', $userId)->count();
        $waitingApproval = CustomerRequest::where('created_by', $userId)->where('status', 'waiting_approval')->count();
        $activeOrders   = CustomerRequest::where('created_by', $userId)->whereNotIn('status', ['done', 'rejected'])->count();
        $completedOrders = CustomerRequest::where('created_by', $userId)->where('status', 'done')->count();

        // Order aktif (belum selesai/ditolak)
        $activeCR = CustomerRequest::with('details.grade')
            ->where('created_by', $userId)
            ->whereNotIn('status', ['done', 'rejected'])
            ->latest()
            ->get();

        $grades = GradeBeton::all();

        return view('customer.dashboard', compact(
            'totalOrders',
            'waitingApproval',
            'activeOrders',
            'completedOrders',
            'activeCR',
            'grades'
        ));
    }

    /**
     * Halaman riwayat order customer.
     */
    public function history()
    {
        $userId = Auth::id();

        // Riwayat (done + rejected)
        $historyCR = CustomerRequest::with('details.grade')
            ->where('created_by', $userId)
            ->whereIn('status', ['done', 'rejected'])
            ->latest()
            ->paginate(10);

        return view('customer.history', compact('historyCR'));
    }

    /**
     * Endpoint JSON untuk auto-polling status order customer.
     */
    public function getActiveStatuses()
    {
        $orders = CustomerRequest::where('created_by', Auth::id())
            ->whereNotIn('status', ['done', 'rejected'])
            ->select('id', 'request_code', 'status', 'schedule_date')
            ->get();

        return response()->json($orders);
    }

    private function uploadAndCompressFile($file, $subFolder)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = uniqid('doc_', true) . '_' . time() . '.' . $extension;
        $targetDir = 'uploads/' . $subFolder;
        $destinationPath = public_path($targetDir);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filePath = $destinationPath . '/' . $filename;
        $relativeDbPath = $targetDir . '/' . $filename;

        // Compress image files if gd extension is available
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) && extension_loaded('gd')) {
            try {
                // Read image based on format
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $image = @imagecreatefromjpeg($file->getRealPath());
                    if ($image) {
                        imagejpeg($image, $filePath, 60);
                        imagedestroy($image);
                        return $relativeDbPath;
                    }
                } elseif ($extension === 'png') {
                    $image = @imagecreatefrompng($file->getRealPath());
                    if ($image) {
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                        imagepng($image, $filePath, 6);
                        imagedestroy($image);
                        return $relativeDbPath;
                    }
                } elseif ($extension === 'webp') {
                    $image = @imagecreatefromwebp($file->getRealPath());
                    if ($image) {
                        imagewebp($image, $filePath, 60);
                        imagedestroy($image);
                        return $relativeDbPath;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Image compression failed: ' . $e->getMessage());
            }
        }

        // Default upload (e.g., for PDF or if GD fails)
        $file->move($destinationPath, $filename);
        return $relativeDbPath;
    }

    /**
     * Proses order beton baru dari customer (harga aman dari DB).
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'customer_name'      => 'required|string|max:255',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'required|string',
            'region'             => 'nullable|string|max:255',
            'customer_number'    => 'nullable|string|max:255',
            'note'               => 'nullable|string',
            
            // Profil Bisnis
            'no_identitas'       => 'nullable|string|max:100',
            'form_business'      => 'nullable|string|max:255',
            'business_ownership' => 'nullable|in:milik_sendiri,tidak_ada_cabang,sewa_kontrak,kantor_pusat,cabang,proyek',
            'section_business'   => 'nullable|string|max:255',
            'address_business'   => 'nullable|string',
            'npwp'               => 'nullable|string|max:100',
            'tax_name'           => 'nullable|string|max:255',
            'tax_address'        => 'nullable|string',
            
            // Perizinan
            'izin_tdp'           => 'nullable|string|max:255',
            'tdp_date'           => 'nullable|date',
            'izin_siup'          => 'nullable|string|max:255',
            'siup_date'          => 'nullable|date',
            'izin_sio'           => 'nullable|string|max:255',
            'sio_date'           => 'nullable|date',
            
            // Owner
            'owner_name'         => 'nullable|string|max:255',
            'owner_address'      => 'nullable|string',
            'email'              => 'nullable|email|max:255',
            
            // Project
            'office_address'     => 'nullable|string',
            'ongoing_project'    => 'nullable|string',

            'schedule_date'      => 'required|date|after_or_equal:today',
            'delivery_distance'  => 'required|numeric|min:0',
            'delivery_latitude'  => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'ktp_file'           => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:5120',
            'npwp_file'          => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:5120',

            // Detail
            'grade_id'           => 'required|array|min:1',
            'grade_id.*'         => 'required|exists:gradebeton,id_grade',
            'type'               => 'required|array|min:1',
            'qty'                => 'required|array|min:1',
            'qty.*'              => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user();

        // Upload files
        $ktpPath = null;
        if ($request->hasFile('ktp_file')) {
            $ktpPath = $this->uploadAndCompressFile($request->file('ktp_file'), 'ktp');
        }

        $npwpPath = null;
        if ($request->hasFile('npwp_file')) {
            $npwpPath = $this->uploadAndCompressFile($request->file('npwp_file'), 'npwp');
        }

        DB::transaction(function () use ($request, $user, $ktpPath, $npwpPath) {
            $req = CustomerRequest::create([
                'request_code'      => 'CUST-' . date('Ymd') . rand(100, 999),
                'created_by'        => $user->id_user,
                'tanggal'           => now(),
                
                // IDENTITAS
                'customer_name'     => $request->customer_name ?? $user->name_user,
                'phone'             => $request->phone ?? $user->phone,
                'address'           => $request->address,
                'region'            => $request->region,
                'customer_number'   => $request->customer_number,
                'note'              => $request->note,

                // PROFIL BISNIS
                'no_identitas'      => $request->no_identitas,
                'form_business'     => $request->form_business,
                'business_ownership'=> $request->business_ownership,
                'section_business'  => $request->section_business,
                'address_business'  => $request->address_business,

                // PAJAK
                'npwp'              => $request->npwp ?? $user->npwp,
                'tax_name'          => $request->tax_name,
                'tax_address'       => $request->tax_address,

                // IZIN
                'izin_tdp'          => $request->izin_tdp,
                'tdp_date'          => $request->tdp_date,
                'izin_siup'         => $request->izin_siup,
                'siup_date'         => $request->siup_date,
                'izin_sio'          => $request->izin_sio,
                'sio_date'          => $request->sio_date,

                // OWNER
                'owner_name'        => $request->owner_name ?? $user->name_user,
                'owner_address'     => $request->owner_address ?? $user->address,
                'email'             => $request->email ?? $user->email,

                // PROJECT
                'office_address'    => $request->office_address,
                'ongoing_project'   => $request->ongoing_project,

                'schedule_date'     => $request->schedule_date,
                'delivery_distance' => $request->delivery_distance,
                'delivery_fee'      => 0,
                'grand_total'       => 0,
                'delivery_latitude' => $request->delivery_latitude,
                'delivery_longitude'=> $request->delivery_longitude,
                'status'            => 'waiting_approval',

                // FILE UPLOADS
                'ktp_file'          => $ktpPath,
                'npwp_file'         => $npwpPath,
            ]);

            // DETAIL — harga aman ditarik langsung dari database
            $itemsTotal = 0;
            foreach ($request->grade_id as $i => $gradeId) {
                $grade = GradeBeton::findOrFail($gradeId);
                $price = $grade->harga;
                $qty   = $request->qty[$i];
                $lineTotal = $qty * $price;

                CustomerRequestDetail::create([
                    'customer_request_id' => $req->id,
                    'grade_id'            => $gradeId,
                    'type'                => $request->type[$i],
                    'qty'                 => $qty,
                    'price'               => $price,
                    'total'               => $lineTotal,
                ]);

                $itemsTotal += $lineTotal;
            }

            // Hitung delivery fee (0–25km gratis, >25km: Rp 20.000 per kelipatan 5km × total qty m³)
            $distance   = floatval($request->delivery_distance ?? 0);
            $totalQtyM3 = collect($request->qty)->sum(fn($q) => floatval($q));

            if ($distance <= 25) {
                $deliveryFee = 0;
            } else {
                $extraKm     = $distance - 25;
                $increments  = ceil($extraKm / 5);
                $deliveryFee = $increments * 20000 * $totalQtyM3;
            }

            $grandTotal = $itemsTotal + $deliveryFee;

            $req->update([
                'delivery_fee' => $deliveryFee,
                'grand_total'  => $grandTotal,
            ]);

            // Buat approval rows (direktur & wakil direktur)
            foreach (['wakil_direktur', 'direktur_utama'] as $role) {
                CustomerRequestApproval::create([
                    'customer_request_id' => $req->id,
                    'role'                => $role,
                    'status'              => 'pending',
                ]);
            }
        });

        return back()->with('success', 'Pesanan berhasil dibuat! Menunggu persetujuan.');
    }

    /**
     * Upload bukti transfer (payment receipt).
     */
    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'payment_receipt' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:5120',
        ]);

        $cr = CustomerRequest::where('id', $id)
            ->where('created_by', Auth::id())
            ->whereIn('status', ['approved'])
            ->firstOrFail();

        $file      = $request->file('payment_receipt');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename  = uniqid('receipt_', true) . '_' . time() . '.' . $extension;
        $targetDir = public_path('uploads/receipts');

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file->move($targetDir, $filename);

        $cr->update([
            'payment_receipt' => 'uploads/receipts/' . $filename,
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah! Menunggu verifikasi admin.');
    }
}
