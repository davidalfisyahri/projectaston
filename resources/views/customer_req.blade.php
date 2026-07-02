@extends('main')
@section('title', 'customer_req')
@section('container')

    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="max-w-6xl mx-auto">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                Customer Request
            </h1>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <form action="/customer-request" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, no hp, dll..." 
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 w-full sm:w-64">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm border border-gray-300 font-medium transition">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="/customer-request" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm border border-red-200 font-medium transition">
                            Clear
                        </a>
                    @endif
                </form>

                @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                <button onclick="openModal('addRequest')" class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium text-sm transition w-full sm:w-auto whitespace-nowrap">
                    + Buat Request
                </button>
                @endif
            </div>
        </div>

        <!-- ========================= -->
        <!-- PENDING TABLE -->
        <!-- ========================= -->
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 mt-6">
            <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> Menunggu Approval
        </h2>

        <div class="bg-white rounded-xl shadow border overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Customer</th>
                        <th class="p-3 text-center">Phone</th>
                        <th class="p-3 text-center">Region</th>
                        <th class="p-3 text-center">Dibuat Oleh</th>
                        <th class="p-3 text-center">Tanggal</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingCR as $d)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3 text-xs text-gray-500">{{ $d->request_code }}</td>
                            <td class="p-3 font-medium">{{ $d->customer_name }}</td>
                            <td class="p-3 text-center">{{ $d->phone }}</td>
                            <td class="p-3 text-center">{{ $d->region }}</td>
                            <td class="p-3 text-center text-gray-600">{{ $d->user->name_user ?? '-' }}</td>
                            <td class="p-3 text-center text-gray-500">{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                    {{ $d->status }}
                                </span>
                            </td>
                            <td class="p-3 text-center flex justify-center gap-2">
                                <button type="button" onclick="openDetail({{ $d->id }})" class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-xs hover:bg-blue-200">View</button>
                                <a href="/customer-request/pdf/{{ $d->id }}?download=1" class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs hover:bg-green-200">Download</a>
                                @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                                <form action="/customer-request/delete/{{ $d->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs mt-1">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-6 text-center text-gray-400">Tidak ada request pending.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="p-4 border-t bg-gray-50">
                {{ $pendingCR->links() }}
            </div>
        </div>

        <!-- ========================= -->
        <!-- HISTORY TABLE -->
        <!-- ========================= -->
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-gray-400 rounded-full"></span> Riwayat Request
        </h2>

        <div class="bg-white rounded-xl shadow border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Customer</th>
                        <th class="p-3 text-center">Phone</th>
                        <th class="p-3 text-center">Region</th>
                        <th class="p-3 text-center">Dibuat Oleh</th>
                        <th class="p-3 text-center">Tanggal</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyCR as $d)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3 text-xs text-gray-500">{{ $d->request_code }}</td>
                            <td class="p-3 font-medium">{{ $d->customer_name }}</td>
                            <td class="p-3 text-center">{{ $d->phone }}</td>
                            <td class="p-3 text-center">{{ $d->region }}</td>
                            <td class="p-3 text-center text-gray-600">{{ $d->user->name_user ?? '-' }}</td>
                            <td class="p-3 text-center text-gray-500">{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($d->status == 'approved') bg-green-100 text-green-700
                                    @elseif($d->status == 'rejected') bg-red-100 text-red-600
                                    @elseif($d->status == 'done') bg-blue-100 text-blue-700 font-bold
                                    @elseif($d->status == 'paid') bg-emerald-100 text-emerald-700 font-semibold
                                    @elseif($d->status == 'confirmed_wa') bg-purple-100 text-purple-700
                                    @elseif($d->status == 'scheduled') bg-indigo-100 text-indigo-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $d->status }}
                                </span>
                            </td>
                            <td class="p-3 text-left flex flex-wrap justify-start gap-2">
                                <button type="button" onclick="openDetail({{ $d->id }})" class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-xs hover:bg-blue-200">View</button>

                                <a href="/customer-request/pdf/{{ $d->id }}?download=1" class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs hover:bg-green-200"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                  </svg>
                                </a>

                                @if(in_array($d->status, ['approved', 'paid', 'confirmed_wa', 'scheduled', 'done']))
                                <a href="/customer-request/invoice-pdf/{{ $d->id }}?download=1"
                                    class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded text-xs hover:bg-yellow-200">
                                     Invoice
                                 </a>
                                @endif

                                @if(in_array($d->status, ['confirmed_wa', 'scheduled', 'done']))
                                <a href="/customer-request/spk-pdf/{{ $d->id }}?download=1" class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs hover:bg-orange-200 font-semibold" title="Download SPK Kepala Plant">SPK</a>
                                @endif

                                @if($d->status == 'approved' && (auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama'))
                                <button type="button" onclick="openPayModal({{ $d->id }})" class="bg-emerald-600 text-white px-2 py-1 rounded text-xs hover:bg-emerald-700 shadow-sm font-bold">💳 Info Pembayaran</button>
                                @endif


                                @if(in_array($d->status, ['paid', 'confirmed_wa', 'scheduled']) && (auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama'))
                                <form action="/customer-request/done/{{ $d->id }}" method="POST" onsubmit="return confirm('Tandai request ini sebagai Selesai (Done) di lapangan?')">
                                    @csrf
                                    <button class="bg-emerald-500 text-white px-2 py-1 rounded text-xs hover:bg-emerald-600 shadow-sm font-bold">Done ✓</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-6 text-center text-gray-400">Tidak ada riwayat request.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="p-4 border-t bg-gray-50">
                {{ $historyCR->links() }}
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- DETAIL MODALS -->
    <!-- ========================= -->
    @php
        $allCr = collect($pendingCR->items())->merge($historyCR->items())->unique('id');
    @endphp
    @foreach($allCr as $d)
        <div id="detailModal-{{ $d->id }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg overflow-hidden max-h-[85vh] flex flex-col">

                <!-- HEADER -->
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Detail Customer Request</h2>
                </div>

                <!-- BODY -->
                <div class="p-6 overflow-y-auto space-y-4 text-sm">

                    <!-- IDENTITAS -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Identitas Customer</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Kode Request</td><td class="py-1 font-medium">{{ $d->request_code }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Dibuat Oleh</td><td class="py-1 font-medium text-gray-700">{{ $d->user->name_user ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Nama Customer</td><td class="py-1">{{ $d->customer_name }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Phone</td><td class="py-1">{{ $d->phone ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Region</td><td class="py-1">{{ $d->region ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Customer Number</td><td class="py-1">{{ $d->customer_number ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Pengiriman</td><td class="py-1">{{ $d->address ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Jarak Pengiriman</td><td class="py-1 font-semibold text-blue-600">{{ $d->delivery_distance ? $d->delivery_distance . ' km' : '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Note</td><td class="py-1">{{ $d->note ?? '-' }}</td></tr>
                    </table>

                    <!-- Jarak & Peta Lokasi Pengiriman -->
                    @if($d->delivery_latitude && $d->delivery_longitude)
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Lokasi Pengiriman di Peta</h3>
                    <div id="detail-map-{{ $d->id }}" style="height: 200px; z-index: 10;" class="rounded-lg border shadow-sm my-2 detail-map" data-plat="-6.476278" data-plng="106.733417" data-dlat="{{ $d->delivery_latitude }}" data-dlng="{{ $d->delivery_longitude }}" data-code="{{ $d->request_code }}"></div>
                    @endif

                    <!-- PROFIL BISNIS -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Profil Bisnis</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">No Identitas (NIK)</td><td class="py-1">{{ $d->no_identitas ?? '-' }}</td></tr>
                        <tr>
                            <td class="py-1 text-gray-500 w-44">File KTP</td>
                            <td class="py-1">
                                @if($d->ktp_file)
                                    @if(in_array(strtolower(pathinfo($d->ktp_file, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']))
                                        <a href="{{ asset($d->ktp_file) }}" target="_blank"><img src="{{ asset($d->ktp_file) }}" class="h-16 rounded border hover:opacity-80 transition" alt="KTP"></a>
                                    @else
                                        <a href="{{ asset($d->ktp_file) }}" target="_blank" class="text-blue-600 hover:underline text-xs">📄 Lihat PDF KTP</a>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum diupload</span>
                                @endif
                            </td>
                        </tr>
                        <tr><td class="py-1 text-gray-500">Bentuk Usaha</td><td class="py-1">{{ $d->form_business ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Kepemilikan</td><td class="py-1">{{ $d->business_ownership ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Bidang Usaha</td><td class="py-1">{{ $d->section_business ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Usaha</td><td class="py-1">{{ $d->address_business ?? '-' }}</td></tr>
                    </table>

                    <!-- PAJAK -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Pajak</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">NPWP</td><td class="py-1">{{ $d->npwp ?? '-' }}</td></tr>
                        <tr>
                            <td class="py-1 text-gray-500 w-44">File NPWP</td>
                            <td class="py-1">
                                @if($d->npwp_file)
                                    @if(in_array(strtolower(pathinfo($d->npwp_file, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']))
                                        <a href="{{ asset($d->npwp_file) }}" target="_blank"><img src="{{ asset($d->npwp_file) }}" class="h-16 rounded border hover:opacity-80 transition" alt="NPWP"></a>
                                    @else
                                        <a href="{{ asset($d->npwp_file) }}" target="_blank" class="text-blue-600 hover:underline text-xs">📄 Lihat PDF NPWP</a>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum diupload</span>
                                @endif
                            </td>
                        </tr>
                        <tr><td class="py-1 text-gray-500">Nama Pajak</td><td class="py-1">{{ $d->tax_name ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Pajak</td><td class="py-1">{{ $d->tax_address ?? '-' }}</td></tr>
                    </table>

                    <!-- IZIN -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Perizinan</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">TDP</td><td class="py-1">{{ $d->izin_tdp ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Berlaku TDP</td><td class="py-1">{{ $d->tdp_date ? date('d-m-Y', strtotime($d->tdp_date)) : '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">SIUP</td><td class="py-1">{{ $d->izin_siup ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Berlaku SIUP</td><td class="py-1">{{ $d->siup_date ? date('d-m-Y', strtotime($d->siup_date)) : '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">SIO</td><td class="py-1">{{ $d->izin_sio ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Berlaku SIO</td><td class="py-1">{{ $d->sio_date ? date('d-m-Y', strtotime($d->sio_date)) : '-' }}</td></tr>
                    </table>

                    <!-- OWNER -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Owner</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Nama Pemilik</td><td class="py-1">{{ $d->owner_name ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Pemilik</td><td class="py-1">{{ $d->owner_address ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Email</td><td class="py-1">{{ $d->email ?? '-' }}</td></tr>
                    </table>

                    <!-- PROJECT -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Project</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Alamat Kantor Induk</td><td class="py-1">{{ $d->office_address ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Ongoing Project</td><td class="py-1">{{ $d->ongoing_project ?? '-' }}</td></tr>
                    </table>

                    <!-- JADWAL -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Jadwal</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Jadwal Pengiriman</td><td class="py-1">{{ $d->schedule_date ? date('d-m-Y', strtotime($d->schedule_date)) : '-' }}</td></tr>
                    </table>

                    <!-- DETAIL ORDER -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Detail Order</h3>
                    @if($d->details->count())
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 text-gray-600">
                                    <tr>
                                        <th class="p-2 text-left">Grade</th>
                                        <th class="p-2 text-center">Qty</th>
                                        <th class="p-2 text-right">Harga</th>
                                        <th class="p-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($d->details as $item)
                                        <tr class="border-t">
                                            <td class="p-2">{{ $item->grade->name_grade }}</td>
                                            <td class="p-2 text-center">{{ number_format($item->qty, 2, ',', '.') }} m³</td>
                                            <td class="p-2 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="p-2 text-right font-semibold text-green-600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-100 text-gray-800 text-xs">
                                    <tr>
                                        <td colspan="3" class="p-2 text-right font-semibold">Subtotal Beton</td>
                                        <td class="p-2 text-right font-semibold">Rp {{ number_format($d->details->sum('total'), 0, ',', '.') }}</td>
                                    </tr>
                                    @if($d->discount_amount > 0)
                                    <tr class="text-red-600">
                                        <td colspan="3" class="p-2 text-right font-semibold">
                                            Diskon
                                            @if($d->discount_type == 'percentage')
                                                ({{ number_format($d->discount_value, 0) }}%)
                                            @endif
                                        </td>
                                        <td class="p-2 text-right font-semibold">- Rp {{ number_format($d->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($d->delivery_distance > 0)
                                    <tr>
                                        <td colspan="3" class="p-2 text-right font-semibold">
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
                                    <tr class="border-t font-bold">
                                        <td colspan="3" class="p-2 text-right text-sm">Grand Total</td>
                                        <td class="p-2 text-right text-sm text-green-700">Rp {{ number_format($d->grand_total > 0 ? $d->grand_total : $d->details->sum('total'), 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 italic">Tidak ada detail order.</p>
                    @endif

                </div>

                <!-- FOOTER -->
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-between items-center w-full">
                    @if(in_array($d->status, ['confirmed_wa', 'scheduled', 'done']))
                    <a href="/customer-request/spk-pdf/{{ $d->id }}?download=1" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-1.5 transition">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download SPK
                    </a>
                    @else
                    <div></div>
                    @endif
                    <button type="button" onclick="closeDetail({{ $d->id }})"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium">
                        Close
                    </button>
                </div>

            </div>
        </div>



        {{-- ── MODAL KONFIRMASI PEMBAYARAN MANUAL ────────────────────── --}}
        @if($d->status == 'approved')
        <div id="payModal-{{ $d->id }}"
             onclick="if(event.target===this) closePayModal({{ $d->id }})"
             class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-white w-full max-w-sm rounded-xl shadow-xl overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                    <h2 class="text-base font-bold text-gray-800">💳 Konfirmasi Pembayaran</h2>
                    <button type="button" onclick="closePayModal({{ $d->id }})" class="text-gray-400 hover:text-gray-600 font-bold text-xl leading-none">✕</button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">

                    {{-- Order Summary --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kode</span>
                            <span class="font-mono font-semibold text-gray-800">{{ $d->request_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Customer</span>
                            <span class="font-semibold text-gray-800">{{ $d->customer_name }}</span>
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
                        <p class="font-semibold text-amber-800 mb-2">📋 Info Rekening Tujuan Transfer</p>
                        <div class="space-y-3 text-gray-700">
                            <!-- Bank BCA -->
                            <div class="border-b border-amber-200/60 pb-2">
                                <div class="flex justify-between font-semibold text-gray-800">
                                    <span>Bank BCA</span>
                                </div>
                                <div class="flex justify-between text-xs mt-1">
                                    <span class="text-gray-500">No. Rekening</span>
                                    <span class="font-bold font-mono">8721510107</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Atas Nama</span>
                                    <span class="font-medium">NENENG AJENG YUNIAR</span>
                                </div>
                            </div>
                            
                            <!-- Bank BRI -->
                            <div>
                                <div class="flex justify-between font-semibold text-gray-800">
                                    <span>Bank BRI</span>
                                </div>
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

                    {{-- Bukti Transfer dari Customer --}}
                    @if($d->payment_receipt)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                        <p class="font-semibold text-green-800 mb-2 text-sm">📎 Bukti Transfer dari Customer</p>
                        @php
                            $ext = strtolower(pathinfo($d->payment_receipt, PATHINFO_EXTENSION));
                        @endphp
                        @if(in_array($ext, ['jpg','jpeg','png','webp']))
                            <a href="{{ asset($d->payment_receipt) }}" target="_blank">
                                <img src="{{ asset($d->payment_receipt) }}" alt="Bukti Transfer" class="w-full max-w-xs rounded-lg border shadow-sm hover:opacity-90 transition">
                            </a>
                        @elseif($ext === 'pdf')
                            <a href="{{ asset($d->payment_receipt) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border px-3 py-2 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-50 transition">
                                📄 Lihat PDF Bukti Transfer
                            </a>
                        @endif
                        <p class="text-xs text-green-600 mt-2">✅ Customer sudah mengirim bukti transfer</p>
                    </div>
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-4">
                        <p class="text-xs text-yellow-700">⏳ Customer belum mengunggah bukti transfer.</p>
                    </div>
                    @endif

                    <p class="text-xs text-gray-400 italic mt-3">Klik <strong>Tandai Lunas</strong> setelah transfer masuk dikonfirmasi.</p>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closePayModal({{ $d->id }})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Batal</button>
                    <form action="/customer-request/pay/{{ $d->id }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Yakin pembayaran sudah diterima? Status akan berubah ke PAID.')"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                            ✓ Tandai Lunas
                        </button>
                    </form>
                </div>

            </div>
        </div>
        @endif

    @endforeach

    <!-- ========================= -->
    <!-- MODAL -->
    <!-- ========================= -->
    <div id="modalForm" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">

        <div class="bg-white w-full max-w-6xl rounded-xl shadow-lg overflow-hidden max-h-[90vh] flex flex-col">

            <!-- HEADER -->
            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    Form Customer Request
                </h2>

                <!-- BUTTON X -->
                <button onclick="closeModal()" class="text-gray-500 hover:text-red-500 text-xl font-bold">
                    ✕
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 overflow-y-auto">

                <form action="/customer-request/store" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- ===================== -->
                    <!-- IDENTITAS -->
                    <!-- ===================== -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input name="customer_name" placeholder="Nama Customer" class="border p-2 rounded" required>
                        <input name="phone" placeholder="No HP" class="border p-2 rounded">

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
                            <input name="npwp" placeholder="NPWP" class="border p-2 rounded w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Upload NPWP (Opsional)</label>
                            <input type="file" name="npwp_file" accept="image/*,application/pdf" class="border p-1.5 rounded w-full text-sm">
                        </div>

                        <input name="tax_name" placeholder="Nama Pajak" class="col-span-2 border p-2 rounded">
                        <textarea name="address" id="address_input" placeholder="Alamat pengiriman"
                            class="col-span-2 border p-2 rounded"></textarea>


                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Lewat Link Share Loc Google Maps / Koordinat (Opsional)</label>
                            <input type="text" id="maps_link_input" placeholder="Tempel link https://maps.app.goo.gl/... atau koordinat lat,lng" class="w-full border p-2 rounded bg-white focus:outline-none focus:border-blue-500">
                            <p class="text-[10px] text-gray-500 mt-1">Tempel tautan lokasi Google Maps yang dishare customer dari WhatsApp untuk auto-pointing instan.</p>
                        </div>
                        
                        <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border border-blue-100 p-4 rounded-xl bg-blue-50/30">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jarak Pengantaran (km)</label>
                                <input type="number" step="0.1" name="delivery_distance" id="delivery_distance" placeholder="Jarak dalam km, contoh: 12.5" class="w-full border p-2 rounded bg-white focus:outline-none focus:border-blue-500" required>
                                
                                <!-- Map Picker -->
                                <div class="mt-3">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tandai Lokasi Tujuan di Peta</label>
                                    <div id="map" style="height: 250px; z-index: 10;" class="rounded-lg border shadow-sm"></div>
                                    <p class="text-[10px] text-gray-500 mt-1">Klik pada peta untuk menaruh pin lokasi tujuan pengiriman. Pin merah adalah lokasi Plant.</p>
                                </div>
                                
                                <input type="hidden" name="delivery_latitude" id="delivery_latitude">
                                <input type="hidden" name="delivery_longitude" id="delivery_longitude">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Biaya Pengiriman (Rp)</label>
                                <div id="delivery_fee_auto_display" class="w-full p-2 border rounded bg-gray-100 font-semibold text-gray-800">Rp 0</div>
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
                            <input type="date" name="schedule_date" required class="border p-2 rounded w-full">
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

                            <select name="ongoing_project" id="ongoing_project" class="border p-2 rounded w-full">

                                <option value="">-- Pilih Project (Opsional) --</option>

                                @foreach($projects as $p)
                                    <option value="{{ $p->ongoing_project }}">
                                        {{ $p->ongoing_project }}
                                    </option>
                                @endforeach

                            </select>
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
                        <input name="owner_name" placeholder="Nama Pemilik" class="border p-2 rounded">

                        <input name="email" type="email" placeholder="Email pemilik" class="border p-2 rounded">

                        <textarea name="owner_address" placeholder="Alamat pemilik"
                            class="col-span-2 border p-2 rounded"></textarea>
                    </div>

                    {{-- <h3 class="mb-3 font-semibold text-gray-800 border-b pb-2">
                        Jadwal
                    </h3>
                    <div>
                        <label class="text-xs text-gray-600">Jadwal Pengiriman</label>
                        <input type="date" name="schedule_date" class="border p-2 rounded w-full">
                    </div>
                    <br> --}}
                    
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
                                    <th class="p-2 text-center">Qty</th>
                                    <th class="p-2 text-right">Harga</th>
                                    <th class="p-2 text-right">Total</th>
                                    <th class="p-2 text-center w-10">#</th>
                                </tr>
                            </thead>

                            <tbody id="detailTable">
                                <tr class="border-t">
                                    <td class="p-2">
                                        <select name="grade_id[]" class="border rounded px-2 py-1 w-full gradeSelect">
                                            @foreach($grades as $g)
                                                <option value="{{ $g->id_grade }}" data-harga="{{ $g->harga }}">
                                                    {{ $g->name_grade }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="type[]" value="fa">
                                    </td>

                                    <td class="p-2">
                                        <input type="number" name="qty[]" class="border rounded px-2 py-1 w-full qtyInput">
                                    </td>

                                    <td class="p-2">
                                        <input type="text" name="price[]"
                                            class="border rounded px-2 py-1 w-full priceInput bg-gray-50" readonly>
                                    </td>

                                    <td class="p-2">
                                        <input type="text" class="border rounded px-2 py-1 w-full totalInput bg-gray-100"
                                            readonly>
                                    </td>

                                    <td class="p-2 text-center">
                                        <button type="button" class="btn-remove text-red-500 text-lg">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-100 text-gray-800">
                                <tr>
                                    <td colspan="3" class="p-2 text-right font-semibold">Subtotal Item (Beton)</td>
                                    <td class="p-2 text-right font-semibold text-gray-700">
                                        Rp <span id="subtotalDisplay">0</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr id="discountDisplayRow" class="hidden text-red-600 font-semibold">
                                    <td colspan="3" class="p-2 text-right">
                                        Potongan Diskon <span id="discountLabelDetail" class="text-xs font-normal"></span>
                                    </td>
                                    <td class="p-2 text-right" id="discountAmountDisplay">
                                        - Rp 0
                                    </td>
                                    <td></td>
                                </tr>
                                <tr id="discountedPriceRow" class="hidden">
                                    <td colspan="3" class="p-2 text-right font-semibold text-green-700">Harga Setelah Diskon</td>
                                    <td class="p-2 text-right font-semibold text-green-700" id="discountedPriceDisplay">
                                        Rp 0
                                    </td>
                                    <td></td>
                                </tr>
                                <tr id="deliveryFeeDisplayRow" class="hidden">
                                    <td colspan="3" class="p-2 text-right font-semibold">
                                        Biaya Pengiriman <span id="deliveryDistanceDisplay" class="font-normal text-xs text-gray-500"></span>
                                        <span id="deliveryFeeBreakdown" class="text-[10px] text-gray-500 font-normal block"></span>
                                    </td>
                                    <td class="p-2 text-right font-semibold text-orange-600">
                                        Rp <span id="deliveryFeeAmountDisplay">0</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="p-2 text-right font-bold">Grand Total</td>
                                    <td class="p-2 text-right font-bold text-green-700">
                                        Rp <span id="grandTotalDisplay">0</span>
                                        <input type="hidden" name="grand_total" id="grandTotalInput" value="0">
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-3 gap-3">
                        <button type="button" onclick="addRow()" class="text-blue-600 text-sm font-semibold hover:underline">
                            + Tambah Item
                        </button>

                        <div class="flex gap-2 items-center bg-gray-50 border p-3 rounded-xl shadow-sm">
                            <div class="flex flex-col gap-0.5">
                                <label class="text-[10px] font-bold text-gray-500 uppercase">Tipe Diskon</label>
                                <select name="discount_type" id="discount_type" class="border px-2 py-1 rounded bg-white text-xs focus:outline-none focus:border-blue-500 w-36">
                                    <option value="">Tanpa Diskon</option>
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Nominal (Rupiah)</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <label class="text-[10px] font-bold text-gray-500 uppercase">Nilai Diskon</label>
                                <input type="number" step="any" name="discount_value" id="discount_value" placeholder="0" class="border px-2 py-1 rounded bg-white text-xs focus:outline-none focus:border-blue-500 w-28" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" onclick="closeModal()"
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

            // Inisialisasi Select2 untuk Project
            if (typeof $ !== 'undefined') {
                $(document).ready(function () {
                    $('#ongoing_project').select2({
                        placeholder: "Cari atau pilih project...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#modalForm')
                    });
                });
            }

            // =====================
            // FORMAT & HITUNG
            // =====================
            // =====================
            // FORMAT & HITUNG
            // =====================
            const distanceInput = document.getElementById('delivery_distance');
            const feeAutoDisplay = document.getElementById('delivery_fee_auto_display');
            const discountTypeInput = document.getElementById('discount_type');
            const discountValueInput = document.getElementById('discount_value');

            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num)
            }

            function formatRupiah(number) {
                if (!number) return '';
                return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(number);
            }

            /**
             * Hitung biaya pengiriman:
             * - 0–25 km  : GRATIS
             * - >25 km   : Rp 20.000 per kelipatan 5 km × qty m³
             */
            function getDeliveryFee(distance, totalQtyM3) {
                if (isNaN(distance) || distance <= 0) return 0;
                if (distance <= 25) return 0; // gratis

                const extraKm = distance - 25;
                const increments = Math.ceil(extraKm / 5);
                return increments * 20000 * totalQtyM3;
            }

            function updateAllTotals() {
                // 1. Hitung total item order
                let totalInputs = document.querySelectorAll('.totalInput')
                let itemsTotal = 0
                totalInputs.forEach(input => {
                    let rawVal = parseFloat(input.dataset.raw) || 0;
                    itemsTotal += rawVal;
                });

                const subtotalDisplayEl = document.getElementById('subtotalDisplay');
                if (subtotalDisplayEl) {
                    subtotalDisplayEl.innerText = formatNumber(itemsTotal);
                }

                // Hitung total qty m³
                let totalQtyM3 = 0;
                document.querySelectorAll('#detailTable tr').forEach(row => {
                    let qtyInput = row.querySelector('input[name="qty[]"]');
                    if (qtyInput) {
                        totalQtyM3 += parseFloat(qtyInput.value) || 0;
                    }
                });

                // 2. Hitung delivery fee (0-25km gratis, >25km Rp 20.000 per 5km × qty m³)
                let distance = parseFloat(distanceInput.value) || 0;
                let fee = getDeliveryFee(distance, totalQtyM3);
                
                const deliveryFeeDisplayRow = document.getElementById('deliveryFeeDisplayRow');
                const deliveryDistanceDisplay = document.getElementById('deliveryDistanceDisplay');
                const deliveryFeeBreakdown = document.getElementById('deliveryFeeBreakdown');
                const deliveryFeeAmountDisplay = document.getElementById('deliveryFeeAmountDisplay');

                if (distance <= 0) {
                    feeAutoDisplay.innerText = 'Rp 0';
                    if (deliveryFeeDisplayRow) deliveryFeeDisplayRow.classList.add('hidden');
                } else if (distance <= 25) {
                    feeAutoDisplay.innerText = 'GRATIS (≤ 25 km)';
                    if (deliveryFeeDisplayRow) {
                        deliveryFeeDisplayRow.classList.remove('hidden');
                        deliveryDistanceDisplay.innerText = `(${distance} km)`;
                        deliveryFeeBreakdown.innerText = '';
                        deliveryFeeAmountDisplay.innerText = '0 (Gratis)';
                    }
                } else {
                    const extraKm = distance - 25;
                    const increments = Math.ceil(extraKm / 5);
                    const breakdownText = `(${increments} × Rp 20.000 × ${formatNumber(totalQtyM3)} m³)`;
                    
                    feeAutoDisplay.innerText = 'Rp ' + formatNumber(fee) + ' ' + breakdownText;
                    
                    if (deliveryFeeDisplayRow) {
                        deliveryFeeDisplayRow.classList.remove('hidden');
                        deliveryDistanceDisplay.innerText = `(${distance} km)`;
                        deliveryFeeBreakdown.innerText = breakdownText;
                        deliveryFeeAmountDisplay.innerText = formatNumber(fee);
                    }
                }

                // 3. Hitung diskon
                let discountType = discountTypeInput ? discountTypeInput.value : '';
                let discountValue = discountValueInput ? parseFloat(discountValueInput.value) || 0 : 0;
                let discountAmount = 0;

                if (discountType === 'percentage') {
                    discountAmount = itemsTotal * (discountValue / 100);
                } else if (discountType === 'fixed') {
                    discountAmount = discountValue;
                }

                if (discountAmount > itemsTotal) {
                    discountAmount = itemsTotal;
                }

                // Tampilkan diskon jika ada
                const discountDisplayEl = document.getElementById('discountDisplayRow');
                const discountedPriceRow = document.getElementById('discountedPriceRow');
                const discountLabelDetail = document.getElementById('discountLabelDetail');
                if (discountDisplayEl) {
                    if (discountAmount > 0) {
                        discountDisplayEl.classList.remove('hidden');
                        document.getElementById('discountAmountDisplay').innerText = '- Rp ' + formatNumber(discountAmount);

                        // Tampilkan label detail diskon
                        if (discountLabelDetail) {
                            if (discountType === 'percentage') {
                                discountLabelDetail.innerText = '(' + discountValue + '%)';
                            } else if (discountType === 'fixed') {
                                discountLabelDetail.innerText = '(Rp ' + formatNumber(discountValue) + ')';
                            } else {
                                discountLabelDetail.innerText = '';
                            }
                        }

                        // Tampilkan harga setelah diskon
                        if (discountedPriceRow) {
                            discountedPriceRow.classList.remove('hidden');
                            let afterDiscount = itemsTotal - discountAmount;
                            document.getElementById('discountedPriceDisplay').innerText = 'Rp ' + formatNumber(afterDiscount);
                        }
                    } else {
                        discountDisplayEl.classList.add('hidden');
                        if (discountedPriceRow) discountedPriceRow.classList.add('hidden');
                        if (discountLabelDetail) discountLabelDetail.innerText = '';
                    }
                }

                // 4. Tampilkan Grand Total
                let grandTotal = (itemsTotal - discountAmount) + fee;
                document.getElementById('grandTotalDisplay').innerText = formatNumber(grandTotal);
                document.getElementById('grandTotalInput').value = grandTotal;
            }

            if (distanceInput) {
                distanceInput.addEventListener('input', updateAllTotals);
            }
            if (discountTypeInput) {
                discountTypeInput.addEventListener('change', updateAllTotals);
            }
            if (discountValueInput) {
                discountValueInput.addEventListener('input', updateAllTotals);
            }

            // =====================
            // EVENT LISTENER FORM ITEM
            // =====================
            document.getElementById('detailTable').addEventListener('input', function (e) {
                let row = e.target.closest('tr')
                if (!row) return

                let grade = row.querySelector('.gradeSelect')
                let qty = row.querySelector('.qtyInput')
                let price = row.querySelector('.priceInput')
                let total = row.querySelector('.totalInput')

                if (!grade) return

                let selected = grade.options[grade.selectedIndex]
                let harga = selected.dataset.harga

                harga = parseFloat(harga) || 0
                let qtyVal = parseFloat(qty.value) || 0

                // Tampilkan harga dengan format
                price.value = formatNumber(harga)

                // Hitung total dan simpan raw-nya
                let totalVal = qtyVal * harga
                total.value = formatNumber(totalVal)
                total.dataset.raw = totalVal

                updateAllTotals()
            })

            // =====================
            // TAMBAH ROW
            // =====================
            window.addRow = function () {
                let table = document.getElementById('detailTable')
                let row = table.querySelector('tr').cloneNode(true)

                // reset input value & dataset
                row.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    if(input.dataset.raw) input.dataset.raw = '0';
                })

                table.appendChild(row)
            }

            // =====================
            // HAPUS ROW
            // =====================
            document.getElementById('detailTable').addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-remove')) {
                    let rows = document.querySelectorAll('#detailTable tr')
                    if (rows.length > 1) {
                        e.target.closest('tr').remove()
                        updateAllTotals()
                    }
                }
            })

            // =====================
            // MAP PICKER & ROUTING LOGIC
            // =====================
            let pickerMap = null;
            let pickerMarker = null;
            const plantCoords = [-6.476278, 106.733417];

            function setPickerDestination(lat, lng) {
                document.getElementById('delivery_latitude').value = lat;
                document.getElementById('delivery_longitude').value = lng;

                if (!pickerMarker) {
                    pickerMarker = L.marker([lat, lng], {draggable: true}).addTo(pickerMap);
                    pickerMarker.on('dragend', function(e) {
                        const marker = e.target;
                        const position = marker.getLatLng();
                        setPickerDestination(position.lat, position.lng);
                    });
                } else {
                    pickerMarker.setLatLng([lat, lng]);
                }

                // Kalkulasi jarak jalan mobil dari Plant
                calculateRouteDistance(plantCoords[0], plantCoords[1], lat, lng);
            }

            function calculateRouteDistance(lat1, lng1, lat2, lng2) {
                const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${lng1},${lat1};${lng2},${lat2}?overview=false`;
                
                document.getElementById('delivery_distance').value = '';
                document.getElementById('delivery_distance').placeholder = 'Menghitung rute...';
                
                fetch(osrmUrl)
                    .then(res => res.json())
                    .then(data => {
                        if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                            const distanceInMeters = data.routes[0].distance;
                            const distanceInKm = (distanceInMeters / 1000).toFixed(1);
                            document.getElementById('delivery_distance').value = distanceInKm;
                            updateAllTotals();
                        } else {
                            throw new Error('OSRM route failed');
                        }
                    })
                    .catch(err => {
                        console.warn('OSRM failed, falling back to Haversine straight-line distance:', err);
                        // Fallback Haversine
                        const distanceInKm = haversineDistance(lat1, lng1, lat2, lng2).toFixed(1);
                        document.getElementById('delivery_distance').value = distanceInKm;
                        updateAllTotals();
                    });
            }

            function haversineDistance(lat1, lon1, lat2, lon2) {
                const R = 6371; // km
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = 
                    Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            // =====================
            // GEOCONDING & GMAPS LINK RESOLVER LOGIC
            // =====================
            const mapsLinkInput = document.getElementById('maps_link_input');

            function finishGeocode(lat, lon) {
                if (pickerMap) {
                    pickerMap.setView([lat, lon], 14);
                    setPickerDestination(lat, lon);
                }
            }

            // Google Maps Link Parser / Resolver
            function handleMapsLinkInput() {
                const inputVal = mapsLinkInput.value.trim();
                if (!inputVal) return;

                // Extract URL if present anywhere in the text
                const urlRegex = /(https?:\/\/[^\s]+)/;
                const urlMatch = inputVal.match(urlRegex);
                const targetUrl = urlMatch ? urlMatch[0] : null;

                if (targetUrl) {
                    // 1. Cek jika URL adalah Google Maps Long URL dengan q=lat,lng
                    const qMatch = targetUrl.match(/[?&]q=([+-]?\d+\.\d+)(?:\s*|\+*),(?:\s*|\+*)([+-]?\d+\.\d+)/);
                    if (qMatch) {
                        finishGeocode(parseFloat(qMatch[1]), parseFloat(qMatch[2]));
                        mapsLinkInput.className = "w-full border border-green-400 p-2 rounded bg-green-50/20 focus:outline-none";
                        return;
                    }
                    
                    // 2. Cek jika URL mengandung /place/lat,lng atau /search/lat,lng
                    const placeMatch = targetUrl.match(/\/(?:place|search)\/([+-]?\d+\.\d+)(?:\s*|\+*),(?:\s*|\+*)([+-]?\d+\.\d+)/);
                    if (placeMatch) {
                        finishGeocode(parseFloat(placeMatch[1]), parseFloat(placeMatch[2]));
                        mapsLinkInput.className = "w-full border border-green-400 p-2 rounded bg-green-50/20 focus:outline-none";
                        return;
                    }

                    // 3. Cek jika URL mengandung @lat,lng
                    const atMatch = targetUrl.match(/@([+-]?\d+\.\d+)(?:\s*|\+*),(?:\s*|\+*)([+-]?\d+\.\d+)/);
                    if (atMatch) {
                        finishGeocode(parseFloat(atMatch[1]), parseFloat(atMatch[2]));
                        mapsLinkInput.className = "w-full border border-green-400 p-2 rounded bg-green-50/20 focus:outline-none";
                        return;
                    }

                    // 4. Jika itu short URL (misal maps.app.goo.gl, goo.gl, atau maps.google.com)
                    if (targetUrl.includes('goo.gl') || targetUrl.includes('maps.app') || targetUrl.includes('maps.google')) {
                        mapsLinkInput.placeholder = "Membaca link Google Maps...";
                        mapsLinkInput.disabled = true;

                        fetch('/api/resolve-maps-url', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ url: targetUrl })
                        })
                        .then(res => res.json())
                        .then(data => {
                            mapsLinkInput.disabled = false;
                            mapsLinkInput.placeholder = "Tempel link https://maps.app.goo.gl/... atau koordinat lat,lng";
                            if (data.success) {
                                finishGeocode(data.latitude, data.longitude);
                                mapsLinkInput.className = "w-full border border-green-400 p-2 rounded bg-green-50/20 focus:outline-none";
                            } else {
                                mapsLinkInput.className = "w-full border border-red-400 p-2 rounded bg-red-50/20 focus:outline-none";
                                alert(data.error || 'Gagal membaca koordinat dari link tersebut.');
                            }
                        })
                        .catch(err => {
                            mapsLinkInput.disabled = false;
                            mapsLinkInput.placeholder = "Tempel link https://maps.app.goo.gl/... atau koordinat lat,lng";
                            mapsLinkInput.className = "w-full border border-red-400 p-2 rounded bg-red-50/20 focus:outline-none";
                            console.error('Resolve short url failed:', err);
                            alert('Terjadi kesalahan koneksi saat membaca link Google Maps.');
                        });
                        return;
                    }
                }

                // 5. Cek jika di dalam teks ada koordinat langsung (lat, lng) di mana saja
                const coordRegex = /([+-]?\d+\.\d+)(?:\s*|\+*),(?:\s*|\+*)([+-]?\d+\.\d+)/;
                const coordMatch = inputVal.match(coordRegex);
                if (coordMatch) {
                    const lat = parseFloat(coordMatch[1]);
                    const lon = parseFloat(coordMatch[2]);
                    finishGeocode(lat, lon);
                    mapsLinkInput.className = "w-full border border-green-400 p-2 rounded bg-green-50/20 focus:outline-none";
                    return;
                }

                mapsLinkInput.className = "w-full border border-red-400 p-2 rounded bg-red-50/20 focus:outline-none";
                alert('Format link atau teks tidak dikenali. Gunakan link share location WhatsApp/Google Maps resmi atau koordinat lat,lng.');
            }


            if (mapsLinkInput) {
                mapsLinkInput.addEventListener('change', handleMapsLinkInput);
                mapsLinkInput.addEventListener('paste', () => {
                    setTimeout(handleMapsLinkInput, 100);
                });
            }

            // =====================
            // MODAL CONTROL & MAP INITIALIZATION
            // =====================
            const detailMapInstances = {};

            window.openModal = function () {
                document.getElementById('modalForm').classList.remove('hidden')
                document.getElementById('modalForm').classList.add('flex')
                
                if (!pickerMap) {
                    setTimeout(() => {
                        pickerMap = L.map('map').setView(plantCoords, 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(pickerMap);

                        // Icon merah untuk Plant
                        const redIcon = new L.Icon({
                          iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                          iconSize: [25, 41],
                          iconAnchor: [12, 41],
                          popupAnchor: [1, -34],
                          shadowSize: [41, 41]
                        });
                        L.marker(plantCoords, {icon: redIcon}).addTo(pickerMap).bindPopup("Plant (Mulai)").openPopup();

                        // Klik peta untuk taruh pin tujuan
                        pickerMap.on('click', function(e) {
                            setPickerDestination(e.latlng.lat, e.latlng.lng);
                        });

                        pickerMap.invalidateSize();
                    }, 200);
                } else {
                    setTimeout(() => {
                        pickerMap.invalidateSize();
                    }, 200);
                }
            }

            window.closeModal = function () {
                document.getElementById('modalForm').classList.add('hidden')
                document.getElementById('modalForm').classList.remove('flex')
            }

            window.openDetail = function (id) {
                document.getElementById('detailModal-' + id).classList.remove('hidden')
                document.getElementById('detailModal-' + id).classList.add('flex')
                
                // Inisialisasi peta detail setelah modal tampil
                const mapEl = document.getElementById('detail-map-' + id);
                if (mapEl && !detailMapInstances[id]) {
                    const plat = parseFloat(mapEl.dataset.plat);
                    const plng = parseFloat(mapEl.dataset.plng);
                    const dlat = parseFloat(mapEl.dataset.dlat);
                    const dlng = parseFloat(mapEl.dataset.dlng);
                    const code = mapEl.dataset.code;

                    setTimeout(() => {
                        const dMap = L.map('detail-map-' + id).setView([dlat, dlng], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(dMap);

                        const redIcon = new L.Icon({
                          iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                          iconSize: [25, 41],
                          iconAnchor: [12, 41],
                          popupAnchor: [1, -34],
                          shadowSize: [41, 41]
                        });

                        L.marker([plat, plng], {icon: redIcon}).addTo(dMap).bindPopup("Plant (Mulai)").openPopup();
                        L.marker([dlat, dlng]).addTo(dMap).bindPopup(`Tujuan (${code})`);

                        // Draw dashed line
                        L.polyline([[plat, plng], [dlat, dlng]], {color: 'blue', weight: 3, dashArray: '5, 10'}).addTo(dMap);

                        detailMapInstances[id] = dMap;
                        dMap.invalidateSize();
                    }, 200);
                } else if (detailMapInstances[id]) {
                    setTimeout(() => {
                        detailMapInstances[id].invalidateSize();
                    }, 200);
                }
            }

            window.closeDetail = function (id) {
                document.getElementById('detailModal-' + id).classList.add('hidden')
                document.getElementById('detailModal-' + id).classList.remove('flex')
            }



            // Klik background overlay = close modal
            document.getElementById('modalForm').addEventListener('click', function (e) {
                if (e.target.id === 'modalForm') {
                    window.closeModal()
                }
            })

            // =====================
            // KONFIRMASI PEMBAYARAN MANUAL
            // =====================
            window.openPayModal = function (id) {
                const modal = document.getElementById('payModal-' + id);
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }

            window.closePayModal = function (id) {
                const modal = document.getElementById('payModal-' + id);
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

        })
    </script>

@endsection