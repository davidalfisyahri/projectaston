@extends('main')
@section('title', 'Customer Dashboard')
@section('container')

    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="max-w-6xl mx-auto">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ auth()->user()->name_user }}!</h1>
                <p class="text-sm text-gray-500 mt-1">Portal Pemesanan Beton — PT Istimewa Aston Indonesia</p>
            </div>
            <button onclick="openOrderModal()" class="bg-[#E53E3E] text-white px-5 py-2.5 rounded-lg hover:bg-red-700 font-semibold text-sm transition whitespace-nowrap shadow-sm">
                + Buat Pesanan Baru
            </button>
        </div>

        <!-- STATISTIK -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow border p-5">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalOrders }}</p>
            </div>
            <div class="bg-white rounded-xl shadow border p-5">
                <p class="text-xs text-gray-500 uppercase font-semibold">Menunggu Approval</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $waitingApproval }}</p>
            </div>
            <div class="bg-white rounded-xl shadow border p-5">
                <p class="text-xs text-gray-500 uppercase font-semibold">Aktif</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $activeOrders }}</p>
            </div>
            <div class="bg-white rounded-xl shadow border p-5">
                <p class="text-xs text-gray-500 uppercase font-semibold">Selesai</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $completedOrders }}</p>
            </div>
        </div>

        {{-- SUCCESS / ERROR --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-xl mb-6 text-sm font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ========================= -->
        <!-- PESANAN AKTIF -->
        <!-- ========================= -->
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-blue-500 rounded-full"></span> Pesanan Aktif
        </h2>

        <div class="bg-white rounded-xl shadow border overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 text-left">Kode</th>
                            <th class="p-3 text-left">Tanggal Order</th>
                            <th class="p-3 text-left">Tanggal Kirim</th>
                            <th class="p-3 text-right">Grand Total</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeCR as $d)
                        <!-- Main row -->
                        <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="toggleActiveDetail({{ $d->id }})">
                            <td class="p-3 text-xs font-mono text-gray-500">
                                <div class="flex items-center gap-1.5">
                                    <svg id="chevron-{{ $d->id }}" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ $d->request_code }}
                                </div>
                            </td>
                            <td class="p-3 text-gray-700 font-medium">{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td class="p-3 text-gray-700 font-medium">{{ $d->schedule_date ? date('d-m-Y', strtotime($d->schedule_date)) : '-' }}</td>
                            <td class="p-3 text-right font-semibold text-gray-800">Rp {{ number_format($d->grand_total > 0 ? $d->grand_total : $d->details->sum('total'), 0, ',', '.') }}</td>
                            <td class="p-3 text-center">
                                <span id="status-badge-{{ $d->id }}" class="px-2.5 py-1 text-xs rounded-full font-semibold
                                    @if($d->status == 'waiting_approval') bg-yellow-100 text-yellow-700
                                    @elseif($d->status == 'approved') bg-green-100 text-green-700
                                    @elseif($d->status == 'paid') bg-emerald-100 text-emerald-700
                                    @elseif($d->status == 'confirmed_wa') bg-purple-100 text-purple-700
                                    @elseif($d->status == 'scheduled') bg-indigo-100 text-indigo-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ str_replace('_', ' ', ucfirst($d->status)) }}
                                </span>
                            </td>
                            <td class="p-3 text-center" onclick="event.stopPropagation()">
                                <button onclick="toggleActiveDetail({{ $d->id }})" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Detail / Dropdown row -->
                        <tr id="active-detail-{{ $d->id }}" class="hidden bg-gray-50/50 border-t">
                            <td colspan="6" class="p-5">
                                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-6">
                                    <!-- Stepper Progress Bar -->
                                    @if($d->status !== 'rejected')
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Status Progress</h4>
                                        @php
                                            $statusMap = ['waiting_approval' => 1, 'draft' => 1, 'approved' => 2, 'paid' => 3, 'confirmed_wa' => 3, 'scheduled' => 4, 'done' => 5];
                                            $currentStep = $statusMap[$d->status] ?? 1;
                                            $steps = [
                                                ['label' => 'Menunggu Persetujuan', 'step' => 1],
                                                ['label' => 'Disetujui', 'step' => 2],
                                                ['label' => 'Pembayaran', 'step' => 3],
                                                ['label' => 'Dijadwalkan', 'step' => 4],
                                                ['label' => 'Selesai', 'step' => 5],
                                            ];
                                        @endphp
                                        <div class="px-5 py-5" id="stepper-{{ $d->id }}" data-current-step="{{ $currentStep }}">
                                            <div class="flex items-center justify-between relative">
                                                {{-- Background line --}}
                                                <div class="absolute top-4 left-0 right-0 h-1 bg-gray-200 rounded-full z-0"></div>
                                                {{-- Filled line --}}
                                                <div class="absolute top-4 left-0 h-1 bg-gradient-to-r from-red-700 to-red-500 rounded-full z-0 transition-all duration-700 ease-in-out stepper-fill"
                                                     style="width: {{ 10 + ($currentStep - 1) * 20 }}%"></div>

                                                @foreach($steps as $s)
                                                <div class="flex flex-col items-center z-10 relative" style="flex: 1;">
                                                    {{-- Node --}}
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500
                                                        @if($s['step'] < $currentStep)
                                                            bg-green-500 text-white shadow-md
                                                        @elseif($s['step'] == $currentStep)
                                                            bg-red-700 text-white ring-4 ring-red-200 shadow-lg scale-110
                                                        @else
                                                            bg-gray-300 text-gray-500
                                                        @endif
                                                    ">
                                                        @if($s['step'] < $currentStep)
                                                            ✓
                                                        @elseif($s['step'] == $currentStep)
                                                            ★
                                                        @else
                                                            {{ $s['step'] }}
                                                        @endif
                                                    </div>
                                                    {{-- Label --}}
                                                    <span class="mt-2 text-[10px] text-center leading-tight font-medium
                                                        @if($s['step'] == $currentStep) text-red-700 font-bold
                                                        @elseif($s['step'] < $currentStep) text-green-600
                                                        @else text-gray-400
                                                        @endif
                                                    ">{{ $s['label'] }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="bg-red-100 border border-red-300 text-red-700 rounded-lg px-4 py-3 text-sm font-bold text-center">
                                        ❌ Pesanan Ditolak
                                    </div>
                                    @endif

                                    <!-- Grid Info Pesanan & Detail Table -->
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <!-- Info Kolom Kiri -->
                                        <div class="space-y-4 lg:col-span-1 border-r border-gray-100 pr-0 lg:pr-6">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Pengiriman</h4>
                                            
                                            <div class="space-y-2.5 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Nama Penerima</span>
                                                    <span class="font-semibold text-gray-800">{{ $d->customer_name }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">No HP</span>
                                                    <span class="font-semibold text-gray-800">{{ $d->phone ?? '-' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-gray-500 mb-0.5">Alamat Kirim</span>
                                                    <span class="font-semibold text-gray-800 bg-gray-50 p-2 rounded border text-xs leading-relaxed">{{ $d->address }}</span>
                                                </div>
                                                @if($d->note)
                                                <div class="flex flex-col">
                                                    <span class="text-gray-500 mb-0.5">Catatan</span>
                                                    <span class="font-semibold text-gray-800 bg-gray-50 p-2 rounded border text-xs leading-relaxed">{{ $d->note }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Item Detail & Dokumen Kolom Kanan -->
                                        <div class="lg:col-span-2 space-y-4">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Item Beton & Biaya</h4>
                                            
                                            @if($d->details->count())
                                            <div class="border rounded-lg overflow-hidden text-sm">
                                                <table class="w-full">
                                                    <thead class="bg-gray-50 text-gray-600 border-b">
                                                        <tr>
                                                            <th class="p-2 text-left">Grade</th>
                                                            <th class="p-2 text-center">Type</th>
                                                            <th class="p-2 text-center">Qty</th>
                                                            <th class="p-2 text-right">Harga</th>
                                                            <th class="p-2 text-right">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($d->details as $item)
                                                        <tr class="border-t bg-white">
                                                            <td class="p-2 font-medium text-gray-800">{{ $item->grade->name_grade }}</td>
                                                            <td class="p-2 text-center uppercase text-gray-600">{{ $item->type }}</td>
                                                            <td class="p-2 text-center text-gray-700">{{ number_format($item->qty, 2, ',', '.') }} m³</td>
                                                            <td class="p-2 text-right text-gray-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                            <td class="p-2 text-right font-semibold text-green-600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-gray-50 text-gray-800 text-xs border-t">
                                                        <tr>
                                                            <td colspan="4" class="p-2 text-right font-semibold">Subtotal Beton</td>
                                                            <td class="p-2 text-right font-semibold">Rp {{ number_format($d->details->sum('total'), 0, ',', '.') }}</td>
                                                        </tr>
                                                        @if($d->discount_amount > 0)
                                                        <tr class="text-red-600">
                                                            <td colspan="4" class="p-2 text-right font-semibold">
                                                                Diskon
                                                                @if($d->discount_type == 'percentage')
                                                                    ({{ number_format($d->discount_value, 0) }}%)
                                                                @endif
                                                            </td>
                                                            <td class="p-2 text-right font-semibold">- Rp {{ number_format($d->discount_amount, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($d->delivery_fee > 0)
                                                        <tr>
                                                            <td colspan="4" class="p-2 text-right font-semibold">
                                                                Biaya Pengiriman ({{ $d->delivery_distance }} km)
                                                                @if($d->delivery_distance > 25)
                                                                    <span class="text-[10px] text-gray-500 font-normal block">
                                                                        ({{ ceil(($d->delivery_distance - 25) / 5) }} × Rp 20.000 × {{ number_format($d->details->sum('qty'), 0, ',', '.') }} m³)
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="p-2 text-right font-semibold text-orange-600">Rp {{ number_format($d->delivery_fee, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr class="border-t font-bold text-sm bg-gray-100/50">
                                                            <td colspan="4" class="p-2 text-right">Grand Total</td>
                                                            <td class="p-2 text-right text-green-700">Rp {{ number_format($d->grand_total > 0 ? $d->grand_total : $d->details->sum('total'), 0, ',', '.') }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            @endif

                                            <!-- Action Buttons & Payment info -->
                                            <div class="flex flex-wrap items-center gap-2 pt-2">
                                                @if(in_array($d->status, ['approved', 'paid', 'confirmed_wa', 'scheduled', 'done']))
                                                <a href="/customer-request/invoice-pdf/{{ $d->id }}?download=1"
                                                   class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-yellow-200 transition">
                                                    📄 Download Invoice
                                                </a>
                                                @endif

                                                @if(in_array($d->status, ['confirmed_wa', 'scheduled', 'done']))
                                                <a href="/customer-request/spk-pdf/{{ $d->id }}?download=1"
                                                   class="bg-orange-100 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-orange-200 transition">
                                                    📋 Download SPK
                                                </a>
                                                @endif

                                                @if($d->status === 'approved' && !$d->payment_receipt)
                                                <button type="button" onclick="openPayModal({{ $d->id }})"
                                                    class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-700 transition shadow-sm">
                                                    💳 Bayar Sekarang
                                                </button>
                                                @elseif($d->status === 'approved' && $d->payment_receipt)
                                                <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                                    ⏳ Bukti Transfer Dikirim — Menunggu Verifikasi Admin
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Upload Bukti Transfer --}}
                        @if($d->status === 'approved')
                        <div id="payModal-{{ $d->id }}"
                             onclick="if(event.target===this) closePayModal({{ $d->id }})"
                             class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
                            <div class="bg-white w-full max-w-sm rounded-xl shadow-xl overflow-hidden">

                                <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                                    <h2 class="text-base font-bold text-gray-800">💳 Pembayaran</h2>
                                    <button type="button" onclick="closePayModal({{ $d->id }})" class="text-gray-400 hover:text-gray-600 font-bold text-xl leading-none">✕</button>
                                </div>

                                <div class="p-6 space-y-4">
                                    {{-- Order Summary --}}
                                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Kode</span>
                                            <span class="font-mono font-semibold text-gray-800">{{ $d->request_code }}</span>
                                        </div>
                                        <div class="flex justify-between border-t border-blue-200 pt-2">
                                            <span class="font-semibold text-gray-700">Grand Total</span>
                                            <span class="font-bold text-green-700 text-base">
                                                Rp {{ number_format($d->grand_total > 0 ? $d->grand_total : $d->details->sum('total'), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Bank Info --}}
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm">
                                        <p class="font-semibold text-amber-800 mb-2">📋 Transfer ke Rekening Berikut</p>
                                        <div class="space-y-3 text-gray-700">
                                            <div class="border-b border-amber-200/60 pb-2">
                                                <div class="flex justify-between font-semibold text-gray-800"><span>Bank BCA</span></div>
                                                <div class="flex justify-between text-xs mt-1">
                                                    <span class="text-gray-500">No. Rekening</span>
                                                    <span class="font-bold font-mono">8721510107</span>
                                                </div>
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-gray-500">Atas Nama</span>
                                                    <span class="font-medium">NENENG AJENG YUNIAR</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex justify-between font-semibold text-gray-800"><span>Bank BRI</span></div>
                                                <div class="flex justify-between text-xs mt-1">
                                                    <span class="text-gray-500">No. Rekening</span>
                                                    <span class="font-bold font-mono">0387 01 0022 123 04</span>
                                                </div>
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-gray-500">Atas Nama</span>
                                                    <span class="font-medium">PT ISTIMEWA ASTON INDONESIA</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Upload Form --}}
                                    <form action="{{ route('customer.order.pay', $d->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                                        <input type="file" name="payment_receipt" accept="image/*,application/pdf" required
                                            class="w-full border p-2 rounded-lg text-sm bg-white mb-3">
                                        <button type="submit" onclick="return confirm('Kirim bukti transfer ini?')"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                                            📤 Kirim Bukti Transfer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                Belum ada pesanan aktif. Klik tombol <strong>"+ Buat Pesanan Baru"</strong> untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



    <!-- ========================= -->
    <!-- MODAL BUAT PESANAN BARU -->
    <!-- ========================= -->
    <div id="orderModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-6xl rounded-xl shadow-lg overflow-hidden max-h-[90vh] flex flex-col">

            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Form Customer Request</h2>
                <button onclick="closeOrderModal()" class="text-gray-500 hover:text-red-500 text-xl font-bold">✕</button>
            </div>

            <div class="p-6 overflow-y-auto">
                <form action="{{ route('customer.order.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- ===================== -->
                    <!-- IDENTITAS -->
                    <!-- ===================== -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input name="customer_name" placeholder="Nama Customer" class="border p-2 rounded" value="{{ auth()->user()->name_user }}" required>
                        <input name="phone" placeholder="No HP" class="border p-2 rounded" value="{{ auth()->user()->phone }}">

                        <input name="region" placeholder="Region" class="border p-2 rounded">
                        <input name="customer_number" placeholder="Customer Number" class="border p-2 rounded">

                        <textarea name="note" placeholder="Note" class="col-span-2 border p-2 rounded"></textarea>
                    </div>

                    <!-- ===================== -->
                    <!-- PROFILE BISNIS -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Profil Bisnis</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">NIK</label>
                            <input name="no_identitas" placeholder="NIK" class="border p-2 rounded w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Upload KTP (Opsional)</label>
                            <input type="file" name="ktp_file" accept="image/*,application/pdf" class="border p-1.5 rounded w-full text-sm">
                        </div>

                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <input name="form_business" placeholder="Bentuk Usaha" class="border p-2 rounded w-full">
                            <select name="business_ownership" class="border p-2 rounded w-full">
                                <option value="">-- Pilih Kepemilikan --</option>
                                <option value="milik_sendiri">Milik Sendiri</option>
                                <option value="tidak_ada_cabang">Tidak Ada Cabang</option>
                                <option value="sewa_kontrak">Sewa / Kontrak</option>
                                <option value="kantor_pusat">Kantor Pusat / Induk</option>
                                <option value="cabang">Cabang</option>
                                <option value="proyek">Proyek</option>
                            </select>
                        </div>

                        <input name="section_business" placeholder="Bidang Usaha" class="border p-2 rounded">
                        <textarea name="address_business" placeholder="Alamat Usaha" class="border p-2 rounded"></textarea>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">NPWP</label>
                            <input name="npwp" placeholder="NPWP" class="border p-2 rounded w-full" value="{{ auth()->user()->npwp }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Upload NPWP (Opsional)</label>
                            <input type="file" name="npwp_file" accept="image/*,application/pdf" class="border p-1.5 rounded w-full text-sm">
                        </div>

                        <input name="tax_name" placeholder="Nama Pajak" class="col-span-2 border p-2 rounded">
                        <textarea name="address" id="cust_address_input" placeholder="Alamat pengiriman"
                            class="col-span-2 border p-2 rounded">{{ auth()->user()->address }}</textarea>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Lewat Link Share Loc Google Maps / Koordinat (Opsional)</label>
                            <input type="text" id="cust_maps_link" placeholder="Tempel link https://maps.app.goo.gl/... atau koordinat lat,lng" class="w-full border p-2 rounded bg-white focus:outline-none focus:border-blue-500">
                            <p class="text-[10px] text-gray-500 mt-1">Tempel tautan lokasi Google Maps yang dishare untuk auto-pointing instan.</p>
                        </div>

                        <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border border-blue-100 p-4 rounded-xl bg-blue-50/30">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jarak Pengantaran (km)</label>
                                <input type="number" step="0.1" name="delivery_distance" id="cust_delivery_distance" placeholder="Jarak dalam km, contoh: 12.5" class="w-full border p-2 rounded bg-white focus:outline-none focus:border-blue-500" required>

                                <!-- Map Picker -->
                                <div class="mt-3">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tandai Lokasi Tujuan di Peta</label>
                                    <div id="custMap" style="height: 250px; z-index: 10;" class="rounded-lg border shadow-sm"></div>
                                    <p class="text-[10px] text-gray-500 mt-1">Klik pada peta untuk menaruh pin lokasi tujuan pengiriman. Pin merah adalah lokasi Plant.</p>
                                </div>

                                <input type="hidden" name="delivery_latitude" id="cust_delivery_latitude">
                                <input type="hidden" name="delivery_longitude" id="cust_delivery_longitude">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Biaya Pengiriman (Rp)</label>
                                <div id="cust_delivery_fee_display" class="w-full p-2 border rounded bg-gray-100 font-semibold text-gray-800">Rp 0</div>
                                <p class="text-[10px] text-gray-500 mt-1">0–25 km gratis. Lebih dari 25 km: Rp 20.000 per kelipatan 5 km × qty (m³)</p>
                            </div>
                        </div>

                        <textarea name="tax_address" placeholder="Alamat Pajak"
                            class="col-span-2 border p-2 rounded"></textarea>
                    </div>

                    <!-- ===================== -->
                    <!-- JADWAL -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Jadwal</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Pengiriman</label>
                            <input type="date" name="schedule_date" required class="border p-2 rounded w-full" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- ===================== -->
                    <!-- PROJECT -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Project</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <textarea name="office_address" placeholder="Jika kantor cabang, Alamat Kantor induk"
                            class="border p-2 rounded"></textarea>

                        <div>
                            <label class="text-xs text-gray-600">Project yang sedang berjalan</label>
                            <input name="ongoing_project" placeholder="Nama Project (Opsional)" class="border p-2 rounded w-full">
                        </div>
                    </div>

                    <!-- ===================== -->
                    <!-- IZIN -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Perizinan</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- TDP -->
                        <div>
                            <label class="text-xs text-gray-600">TDP</label>
                            <input name="izin_tdp" class="border p-2 rounded w-full">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Tanggal Berlaku TDP</label>
                            <input type="date" name="tdp_date" class="border p-2 rounded w-full">
                        </div>

                        <!-- SIUP -->
                        <div>
                            <label class="text-xs text-gray-600">SIUP</label>
                            <input name="izin_siup" class="border p-2 rounded w-full">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Tanggal Berlaku SIUP</label>
                            <input type="date" name="siup_date" class="border p-2 rounded w-full">
                        </div>

                        <!-- SIO -->
                        <div>
                            <label class="text-xs text-gray-600">SIO</label>
                            <input name="izin_sio" class="border p-2 rounded w-full">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Tanggal Berlaku SIO</label>
                            <input type="date" name="sio_date" class="border p-2 rounded w-full">
                        </div>
                    </div>

                    <!-- ===================== -->
                    <!-- OWNER -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Owner</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input name="owner_name" placeholder="Nama Pemilik" class="border p-2 rounded" value="{{ auth()->user()->name_user }}">
                        <input name="email" type="email" placeholder="Email pemilik" class="border p-2 rounded" value="{{ auth()->user()->email }}">
                        <textarea name="owner_address" placeholder="Alamat pemilik"
                            class="col-span-2 border p-2 rounded">{{ auth()->user()->address }}</textarea>
                    </div>

                    <!--=====================-->
                    <!-- DETAIL -->
                    <!-- ===================== -->
                    <h3 class="mb-3 font-semibold text-gray-800 border-b pb-2">
                        Detail Order
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="p-2 text-left">Grade</th>
                                    <th class="p-2 text-center">Type</th>
                                    <th class="p-2 text-center">Qty</th>
                                    <th class="p-2 text-right">Harga</th>
                                    <th class="p-2 text-right">Total</th>
                                    <th class="p-2 text-center w-10">#</th>
                                </tr>
                            </thead>

                            <tbody id="custDetailTable">
                                <tr class="border-t">
                                    <td class="p-2">
                                        <select name="grade_id[]" class="border rounded px-2 py-1 w-full custGradeSelect">
                                            @foreach($grades as $g)
                                                <option value="{{ $g->id_grade }}" data-harga="{{ $g->harga }}">
                                                    {{ $g->name_grade }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="p-2">
                                        <select name="type[]" class="border rounded px-2 py-1 w-full custTypeSelect">
                                            <option value="fa">FA</option>
                                            <option value="nfa">NFA</option>
                                        </select>
                                    </td>

                                    <td class="p-2">
                                        <input type="number" name="qty[]" class="border rounded px-2 py-1 w-full custQtyInput">
                                    </td>

                                    <td class="p-2">
                                        <input type="text" name="price[]"
                                            class="border rounded px-2 py-1 w-full custPriceDisplay bg-gray-50" readonly>
                                    </td>

                                    <td class="p-2">
                                        <input type="text" class="border rounded px-2 py-1 w-full custTotalDisplay bg-gray-100"
                                            readonly data-raw="0">
                                    </td>

                                    <td class="p-2 text-center">
                                        <button type="button" class="custBtnRemove text-red-500 text-lg">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-100 text-gray-800">
                                <tr>
                                    <td colspan="4" class="p-2 text-right font-semibold">Subtotal Item (Beton)</td>
                                    <td class="p-2 text-right font-semibold text-gray-700">
                                        Rp <span id="custSubtotalDisplay">0</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr id="custDeliveryFeeDisplayRow" class="hidden">
                                    <td colspan="4" class="p-2 text-right font-semibold">
                                        Biaya Pengiriman <span id="custDeliveryDistanceDisplay" class="font-normal text-xs text-gray-500"></span>
                                        <span id="custDeliveryFeeBreakdown" class="text-[10px] text-gray-500 font-normal block"></span>
                                    </td>
                                    <td class="p-2 text-right font-semibold text-orange-600">
                                        Rp <span id="custDeliveryFeeAmountDisplay">0</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="p-2 text-right font-bold">Grand Total</td>
                                    <td class="p-2 text-right font-bold text-green-700">
                                        Rp <span id="custGrandTotalDisplay">0</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-3 gap-3">
                        <button type="button" onclick="custAddRow()" class="text-blue-600 text-sm font-semibold hover:underline">
                            + Tambah Item
                        </button>
                    </div>

                    <!-- FOOTER -->
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" onclick="closeOrderModal()"
                            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">
                            Batal
                        </button>

                        <button class="bg-blue-600 text-white px-4 py-2 rounded">
                            Submit
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // =====================
        // MODAL CONTROL
        // =====================
        window.openOrderModal = function () {
            document.getElementById('orderModal').classList.remove('hidden');
            document.getElementById('orderModal').classList.add('flex');
            setTimeout(initCustMap, 200);
        }
        window.closeOrderModal = function () {
            document.getElementById('orderModal').classList.add('hidden');
            document.getElementById('orderModal').classList.remove('flex');
        }
        window.openPayModal = function (id) {
            const m = document.getElementById('payModal-' + id);
            if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        }
        window.closePayModal = function (id) {
            const m = document.getElementById('payModal-' + id);
            if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
        }

        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target.id === 'orderModal') window.closeOrderModal();
        });

        // =====================
        // MAP PICKER
        // =====================
        let custMap = null, custMarker = null;
        const plantCoords = [-6.476278, 106.733417];

        function initCustMap() {
            if (custMap) { custMap.invalidateSize(); return; }
            custMap = L.map('custMap').setView(plantCoords, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(custMap);
            const redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });
            L.marker(plantCoords, {icon: redIcon}).addTo(custMap).bindPopup("Plant (Mulai)").openPopup();
            custMap.on('click', function(e) { setCustDest(e.latlng.lat, e.latlng.lng); });
            custMap.invalidateSize();
        }

        function setCustDest(lat, lng) {
            document.getElementById('cust_delivery_latitude').value = lat;
            document.getElementById('cust_delivery_longitude').value = lng;
            if (!custMarker) {
                custMarker = L.marker([lat, lng], {draggable: true}).addTo(custMap);
                custMarker.on('dragend', function(e) { const p = e.target.getLatLng(); setCustDest(p.lat, p.lng); });
            } else { custMarker.setLatLng([lat, lng]); }
            calcCustRoute(plantCoords[0], plantCoords[1], lat, lng);
        }

        function calcCustRoute(lat1, lng1, lat2, lng2) {
            const url = `https://router.project-osrm.org/route/v1/driving/${lng1},${lat1};${lng2},${lat2}?overview=false`;
            document.getElementById('cust_delivery_distance').value = '';
            document.getElementById('cust_delivery_distance').placeholder = 'Menghitung rute...';
            fetch(url).then(r => r.json()).then(d => {
                if (d.code === 'Ok' && d.routes && d.routes.length > 0) {
                    document.getElementById('cust_delivery_distance').value = (d.routes[0].distance / 1000).toFixed(1);
                    updateCustTotals();
                } else { throw new Error('route fail'); }
            }).catch(() => {
                const km = haversineDist(lat1, lng1, lat2, lng2).toFixed(1);
                document.getElementById('cust_delivery_distance').value = km;
                updateCustTotals();
            });
        }

        function haversineDist(lat1, lon1, lat2, lon2) {
            const R = 6371, dLat = (lat2-lat1)*Math.PI/180, dLon = (lon2-lon1)*Math.PI/180;
            const a = Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)*Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        // Google Maps link resolver
        const custMapsInput = document.getElementById('cust_maps_link');
        if (custMapsInput) {
            custMapsInput.addEventListener('change', handleCustMapsLink);
            custMapsInput.addEventListener('paste', () => setTimeout(handleCustMapsLink, 100));
        }
        function handleCustMapsLink() {
            const val = custMapsInput.value.trim();
            if (!val) return;
            const urlMatch = val.match(/(https?:\/\/[^\s]+)/);
            const url = urlMatch ? urlMatch[0] : null;
            if (url) {
                // Try direct coord extraction
                const qM = url.match(/[?&]q=([+-]?\d+\.\d+)[\s+,]*([+-]?\d+\.\d+)/);
                if (qM) { finishCustGeo(parseFloat(qM[1]), parseFloat(qM[2])); return; }
                const pM = url.match(/\/(?:place|search)\/([+-]?\d+\.\d+)[\s+,]*([+-]?\d+\.\d+)/);
                if (pM) { finishCustGeo(parseFloat(pM[1]), parseFloat(pM[2])); return; }
                const aM = url.match(/@([+-]?\d+\.\d+)[\s+,]*([+-]?\d+\.\d+)/);
                if (aM) { finishCustGeo(parseFloat(aM[1]), parseFloat(aM[2])); return; }
                if (url.includes('goo.gl') || url.includes('maps.app') || url.includes('maps.google')) {
                    fetch('/api/resolve-maps-url', {
                        method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: JSON.stringify({ url })
                    }).then(r => r.json()).then(d => { if (d.success) finishCustGeo(d.latitude, d.longitude); else alert(d.error || 'Gagal.'); }).catch(() => alert('Gagal membaca link.'));
                    return;
                }
            }
            const cM = val.match(/([+-]?\d+\.\d+)[\s+,]*([+-]?\d+\.\d+)/);
            if (cM) { finishCustGeo(parseFloat(cM[1]), parseFloat(cM[2])); return; }
            alert('Format tidak dikenali.');
        }
        function finishCustGeo(lat, lng) { if (custMap) { custMap.setView([lat, lng], 14); setCustDest(lat, lng); } }

        // =====================
        // ORDER FORM CALC
        // =====================
        function fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }

        function updateCustTotals() {
            let itemsTotal = 0;
            let totalQty = 0;
            document.querySelectorAll('#custDetailTable tr').forEach(row => {
                const t = row.querySelector('.custTotalDisplay');
                if (t) itemsTotal += parseFloat(t.dataset.raw) || 0;
                const q = row.querySelector('.custQtyInput');
                if (q) totalQty += parseFloat(q.value) || 0;
            });

            const subtotalDisplayEl = document.getElementById('custSubtotalDisplay');
            if (subtotalDisplayEl) {
                subtotalDisplayEl.innerText = fmt(itemsTotal);
            }

            const dist = parseFloat(document.getElementById('cust_delivery_distance').value) || 0;
            let fee = 0;
            let increments = 0;
            
            const feeEl = document.getElementById('cust_delivery_fee_display');
            const deliveryFeeDisplayRow = document.getElementById('custDeliveryFeeDisplayRow');
            const deliveryDistanceDisplay = document.getElementById('custDeliveryDistanceDisplay');
            const deliveryFeeBreakdown = document.getElementById('custDeliveryFeeBreakdown');
            const deliveryFeeAmountDisplay = document.getElementById('custDeliveryFeeAmountDisplay');

            if (dist <= 0) {
                feeEl.innerText = 'Rp 0';
                if (deliveryFeeDisplayRow) deliveryFeeDisplayRow.classList.add('hidden');
            } else if (dist <= 25) {
                feeEl.innerText = 'GRATIS (≤ 25 km)';
                if (deliveryFeeDisplayRow) {
                    deliveryFeeDisplayRow.classList.remove('hidden');
                    deliveryDistanceDisplay.innerText = `(${dist} km)`;
                    deliveryFeeBreakdown.innerText = '';
                    deliveryFeeAmountDisplay.innerText = '0 (Gratis)';
                }
            } else {
                increments = Math.ceil((dist - 25) / 5);
                fee = increments * 20000 * totalQty;
                const breakdownText = `(${increments} × Rp 20.000 × ${fmt(totalQty)} m³)`;
                
                feeEl.innerText = 'Rp ' + fmt(fee) + ' ' + breakdownText;
                
                if (deliveryFeeDisplayRow) {
                    deliveryFeeDisplayRow.classList.remove('hidden');
                    deliveryDistanceDisplay.innerText = `(${dist} km)`;
                    deliveryFeeBreakdown.innerText = breakdownText;
                    deliveryFeeAmountDisplay.innerText = fmt(fee);
                }
            }

            document.getElementById('custGrandTotalDisplay').innerText = fmt(itemsTotal + fee);
        }

        document.getElementById('custDetailTable').addEventListener('input', function(e) {
            const row = e.target.closest('tr');
            if (!row) return;
            const grade = row.querySelector('.custGradeSelect');
            const qty = row.querySelector('.custQtyInput');
            const price = row.querySelector('.custPriceDisplay');
            const total = row.querySelector('.custTotalDisplay');
            if (!grade) return;
            const sel = grade.options[grade.selectedIndex];
            const harga = parseFloat(sel.dataset.harga) || 0;
            const qtyVal = parseFloat(qty.value) || 0;
            price.value = fmt(harga);
            const tot = qtyVal * harga;
            total.value = fmt(tot);
            total.dataset.raw = tot;
            updateCustTotals();
        });

        document.getElementById('cust_delivery_distance').addEventListener('input', updateCustTotals);

        document.getElementById('custDetailTable').addEventListener('click', function(e) {
            if (e.target.classList.contains('custBtnRemove')) {
                if (document.querySelectorAll('#custDetailTable tr').length > 1) {
                    e.target.closest('tr').remove();
                    updateCustTotals();
                }
            }
        });

        window.custAddRow = function() {
            const table = document.getElementById('custDetailTable');
            const row = table.querySelector('tr').cloneNode(true);
            row.querySelectorAll('input').forEach(i => { i.value = ''; if (i.dataset.raw) i.dataset.raw = '0'; });
            row.querySelector('select').selectedIndex = 0;
            table.appendChild(row);
        }

        // =====================
        // AUTO-POLLING (setiap 10 detik)
        // =====================
        setInterval(function() {
            fetch('{{ route("customer.orders.status") }}')
                .then(r => r.json())
                .then(orders => {
                    const statusMap = {waiting_approval: 1, draft: 1, approved: 2, paid: 3, confirmed_wa: 3, scheduled: 4, done: 5};
                    const badgeColors = {
                        waiting_approval: 'bg-yellow-100 text-yellow-700',
                        approved: 'bg-green-100 text-green-700',
                        paid: 'bg-emerald-100 text-emerald-700',
                        confirmed_wa: 'bg-purple-100 text-purple-700',
                        scheduled: 'bg-indigo-100 text-indigo-700',
                        done: 'bg-green-100 text-green-700',
                    };

                    orders.forEach(o => {
                        const stepperEl = document.getElementById('stepper-' + o.id);
                        if (!stepperEl) return;
                        const oldStep = parseInt(stepperEl.dataset.currentStep);
                        const newStep = statusMap[o.status] || 1;

                        if (oldStep !== newStep) {
                            stepperEl.dataset.currentStep = newStep;

                            // Update fill line
                            const fill = stepperEl.querySelector('.stepper-fill');
                            if (fill) fill.style.width = (10 + (newStep - 1) * 20) + '%';

                            // Update nodes
                            const nodes = stepperEl.querySelectorAll('.flex.flex-col.items-center');
                            nodes.forEach((node, i) => {
                                const step = i + 1;
                                const circle = node.querySelector('div');
                                const label = node.querySelector('span');

                                circle.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500';
                                if (step < newStep) {
                                    circle.classList.add('bg-green-500', 'text-white', 'shadow-md');
                                    circle.innerHTML = '✓';
                                    label.className = 'mt-2 text-[10px] text-center leading-tight font-medium text-green-600';
                                } else if (step === newStep) {
                                    circle.classList.add('bg-red-700', 'text-white', 'ring-4', 'ring-red-200', 'shadow-lg', 'scale-110');
                                    circle.innerHTML = '★';
                                    label.className = 'mt-2 text-[10px] text-center leading-tight font-medium text-red-700 font-bold';
                                } else {
                                    circle.classList.add('bg-gray-300', 'text-gray-500');
                                    circle.innerHTML = step;
                                    label.className = 'mt-2 text-[10px] text-center leading-tight font-medium text-gray-400';
                                }
                            });

                            // Update badge
                            const badge = document.getElementById('status-badge-' + o.id);
                            if (badge) {
                                badge.className = 'px-3 py-1 text-xs rounded-full font-semibold ' + (badgeColors[o.status] || 'bg-gray-100 text-gray-600');
                                badge.innerText = o.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                            }
                        }
                    });
                })
                .catch(() => {}); // silent fail
        }, 10000);

        window.toggleActiveDetail = function(id) {
            const detailRow = document.getElementById('active-detail-' + id);
            const chevron = document.getElementById('chevron-' + id);
            if (detailRow.classList.contains('hidden')) {
                detailRow.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-90');
            } else {
                detailRow.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-90');
            }
        }

    });
    </script>

@endsection
