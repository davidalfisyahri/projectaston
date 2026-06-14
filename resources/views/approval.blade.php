@extends('main')
@section('title', 'Approval')
@section('container')

    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Approval Management</h1>

            <form action="/approval" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data approval..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-900 w-full sm:w-64">
                <button type="submit"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm border border-gray-300 font-medium transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="/approval"
                        class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm border border-red-200 font-medium transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Tidak Mencukupi!',
                        text: {!! json_encode(session('error')) !!},
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Tutup'
                    });
                });
            </script>
        @endif

        {{-- TAB NAVIGATION --}}
        <div class="flex gap-2 mb-6">
            <button onclick="switchTab('cr')" id="tab-cr"
                class="tab-btn px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 bg-red-900 text-white shadow-md">
                Customer Request
                <span class="ml-1 bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">{{ $crPending->total() }}</span>
            </button>
            <button onclick="switchTab('po')" id="tab-po"
                class="tab-btn px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 bg-gray-200 text-gray-600 hover:bg-gray-300">
                Procurement
                <span
                    class="ml-1 bg-gray-300 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $poPending->total() }}</span>
            </button>
        </div>

        {{-- ============================================= --}}
        {{-- TAB 1: CUSTOMER REQUEST --}}
        {{-- ============================================= --}}
        <div id="panel-cr" class="tab-panel">

            {{-- TABEL PENDING --}}
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> Menunggu Approval
            </h2>

            @if($crPending->isEmpty())
                <div class="bg-white rounded-2xl shadow p-8 text-center mb-8">
                    <p class="text-gray-400">Tidak ada Customer Request yang menunggu approval.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Kode</th>
                                    <th class="px-4 py-3 text-left">Customer</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($crPending as $cr)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cr->request_code }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cr->customer_name }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cr->tanggal }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cr->user->name_user ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Waiting</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @php $myApproval = $cr->approvals->where('role', auth()->user()->position)->first(); @endphp
                                                @if($myApproval && $myApproval->status == 'pending')
                                                    <form action="{{ route('approval.customer_request', $cr->id) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approved">
                                                        <button type="submit" onclick="return confirm('Approve?')"
                                                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('approval.customer_request', $cr->id) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="rejected">
                                                        <button type="submit" onclick="return confirm('Reject?')"
                                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400">—</span>
                                                @endif
                                                <button type="button" onclick="openDetail({{ $cr->id }})"
                                                    class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg text-xs font-semibold transition">
                                                    View
                                                </button>
                                                <a href="/customer-request/pdf/{{ $cr->id }}" target="_blank"
                                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                        <span>{{ $crPending->firstItem() ?? 0 }}-{{ $crPending->lastItem() ?? 0 }} of
                            {{ $crPending->total() }}</span>
                        <div class="flex items-center gap-1 ml-4">
                            @if($crPending->onFirstPage())
                                <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                            @else
                                <a href="{{ $crPending->previousPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                            @endif
                            <span class="px-2 py-1 font-semibold text-gray-800">{{ $crPending->currentPage() }}</span>
                            @if($crPending->hasMorePages())
                                <a href="{{ $crPending->nextPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                            @else
                                <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- TABEL HISTORY --}}
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-gray-400 rounded-full"></span> History Approval
            </h2>

            @if($crHistory->isEmpty())
                <div class="bg-white rounded-2xl shadow p-8 text-center">
                    <p class="text-gray-400">Belum ada history approval.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Kode</th>
                                    <th class="px-4 py-3 text-left">Customer</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($crHistory as $cr)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cr->request_code }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cr->customer_name }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cr->tanggal }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cr->user->name_user ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($cr->status == 'approved')
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>
                                            @else
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" onclick="openDetail({{ $cr->id }})"
                                                    class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg text-xs font-semibold transition">
                                                    View
                                                </button>
                                                <a href="/customer-request/pdf/{{ $cr->id }}" target="_blank"
                                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                        <span>{{ $crHistory->firstItem() ?? 0 }}-{{ $crHistory->lastItem() ?? 0 }} of
                            {{ $crHistory->total() }}</span>
                        <div class="flex items-center gap-1 ml-4">
                            @if($crHistory->onFirstPage())
                                <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                            @else
                                <a href="{{ $crHistory->previousPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                            @endif
                            <span class="px-2 py-1 font-semibold text-gray-800">{{ $crHistory->currentPage() }}</span>
                            @if($crHistory->hasMorePages())
                                <a href="{{ $crHistory->nextPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                            @else
                                <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ============================================= --}}
        {{-- TAB 2: PROCUREMENT --}}
        {{-- ============================================= --}}
        <div id="panel-po" class="tab-panel hidden">

            {{-- TABEL PENDING --}}
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> Menunggu Approval
            </h2>

            @if($poPending->isEmpty())
                <div class="bg-white rounded-2xl shadow p-8 text-center mb-8">
                    <p class="text-gray-400">Tidak ada Procurement yang menunggu approval.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">No PO</th>
                                    <th class="px-4 py-3 text-left">Supplier</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($poPending as $po)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $po->no_po }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $po->supplier->name_pt ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $po->tanggal }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-green-600">Rp
                                            {{ number_format($po->total, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('approval.procurement', $po->id_po) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approved">
                                                    <button type="submit" onclick="return confirm('Approve?')"
                                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('approval.procurement', $po->id_po) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="rejected">
                                                    <button type="submit" onclick="return confirm('Reject?')"
                                                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition">
                                                        Reject
                                                    </button>
                                                </form>
                                                <a href="/procurement/pdf/{{ $po->id_po }}" target="_blank"
                                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                        <span>{{ $poPending->firstItem() ?? 0 }}-{{ $poPending->lastItem() ?? 0 }} of
                            {{ $poPending->total() }}</span>
                        <div class="flex items-center gap-1 ml-4">
                            @if($poPending->onFirstPage())
                                <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                            @else
                                <a href="{{ $poPending->previousPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                            @endif
                            <span class="px-2 py-1 font-semibold text-gray-800">{{ $poPending->currentPage() }}</span>
                            @if($poPending->hasMorePages())
                                <a href="{{ $poPending->nextPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                            @else
                                <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- TABEL HISTORY --}}
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-gray-400 rounded-full"></span> History Approval
            </h2>

            @if($poHistory->isEmpty())
                <div class="bg-white rounded-2xl shadow p-8 text-center">
                    <p class="text-gray-400">Belum ada history approval.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">No PO</th>
                                    <th class="px-4 py-3 text-left">Supplier</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($poHistory as $po)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $po->no_po }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $po->supplier->name_pt ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $po->tanggal }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-green-600">Rp
                                            {{ number_format($po->total, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($po->status == 'approved')
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>
                                            @else
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="/procurement/pdf/{{ $po->id_po }}" target="_blank"
                                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                                PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                        <span>{{ $poHistory->firstItem() ?? 0 }}-{{ $poHistory->lastItem() ?? 0 }} of
                            {{ $poHistory->total() }}</span>
                        <div class="flex items-center gap-1 ml-4">
                            @if($poHistory->onFirstPage())
                                <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                            @else
                                <a href="{{ $poHistory->previousPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                            @endif
                            <span class="px-2 py-1 font-semibold text-gray-800">{{ $poHistory->currentPage() }}</span>
                            @if($poHistory->hasMorePages())
                                <a href="{{ $poHistory->nextPageUrl() }}"
                                    class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                            @else
                                <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- ========================= -->
    <!-- DETAIL MODALS CR -->
    <!-- ========================= -->
    @php
        $allCr = collect($crPending->items())->merge($crHistory->items())->unique('id');
    @endphp
    @foreach($allCr as $d)
        <div id="detailModal-{{ $d->id }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg overflow-hidden max-h-[85vh] flex flex-col">

                <!-- HEADER -->
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Detail Customer Request</h2>
                </div>

                <!-- BODY -->
                <div class="p-6 overflow-y-auto space-y-4 text-sm text-left">

                    <!-- IDENTITAS -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Identitas Customer</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">Kode Request</td>
                            <td class="py-1 font-medium">{{ $d->request_code }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Nama Customer</td>
                            <td class="py-1">{{ $d->customer_name }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Phone</td>
                            <td class="py-1">{{ $d->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Region</td>
                            <td class="py-1">{{ $d->region ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Customer Number</td>
                            <td class="py-1">{{ $d->customer_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Alamat Pengiriman</td>
                            <td class="py-1">{{ $d->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Jarak Pengiriman</td>
                            <td class="py-1 font-semibold text-blue-600">{{ $d->delivery_distance ? $d->delivery_distance . ' km' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Note</td>
                            <td class="py-1">{{ $d->note ?? '-' }}</td>
                        </tr>
                    </table>

                    <!-- Jarak & Peta Lokasi Pengiriman -->
                    @if($d->delivery_latitude && $d->delivery_longitude)
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Lokasi Pengiriman di Peta</h3>
                    <div id="detail-map-{{ $d->id }}" style="height: 200px; z-index: 10;" class="rounded-lg border shadow-sm my-2 detail-map" data-plat="-6.476278" data-plng="106.733417" data-dlat="{{ $d->delivery_latitude }}" data-dlng="{{ $d->delivery_longitude }}" data-code="{{ $d->request_code }}"></div>
                    @endif

                    <!-- PROFIL BISNIS -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Profil Bisnis</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">No Identitas (NIK)</td>
                            <td class="py-1">{{ $d->no_identitas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Bentuk Usaha</td>
                            <td class="py-1">{{ $d->form_business ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Kepemilikan</td>
                            <td class="py-1">{{ $d->business_ownership ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Bidang Usaha</td>
                            <td class="py-1">{{ $d->section_business ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Alamat Usaha</td>
                            <td class="py-1">{{ $d->address_business ?? '-' }}</td>
                        </tr>
                    </table>

                    <!-- PAJAK -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Pajak</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">NPWP</td>
                            <td class="py-1">{{ $d->npwp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Nama Pajak</td>
                            <td class="py-1">{{ $d->tax_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Alamat Pajak</td>
                            <td class="py-1">{{ $d->tax_address ?? '-' }}</td>
                        </tr>
                    </table>

                    <!-- IZIN -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Perizinan</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">TDP</td>
                            <td class="py-1">{{ $d->izin_tdp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Berlaku TDP</td>
                            <td class="py-1">{{ $d->tdp_date ? date('d-m-Y', strtotime($d->tdp_date)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">SIUP</td>
                            <td class="py-1">{{ $d->izin_siup ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Berlaku SIUP</td>
                            <td class="py-1">{{ $d->siup_date ? date('d-m-Y', strtotime($d->siup_date)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">SIO</td>
                            <td class="py-1">{{ $d->izin_sio ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Berlaku SIO</td>
                            <td class="py-1">{{ $d->sio_date ? date('d-m-Y', strtotime($d->sio_date)) : '-' }}</td>
                        </tr>
                    </table>

                    <!-- OWNER -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Owner</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">Nama Pemilik</td>
                            <td class="py-1">{{ $d->owner_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Alamat Pemilik</td>
                            <td class="py-1">{{ $d->owner_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Email</td>
                            <td class="py-1">{{ $d->email ?? '-' }}</td>
                        </tr>
                    </table>

                    <!-- PROJECT -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Project</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">Alamat Kantor Induk</td>
                            <td class="py-1">{{ $d->office_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">Ongoing Project</td>
                            <td class="py-1">{{ $d->ongoing_project ?? '-' }}</td>
                        </tr>
                    </table>

                    <!-- JADWAL -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Jadwal</h3>
                    <table class="w-full">
                        <tr>
                            <td class="py-1 text-gray-500 w-44">Jadwal Pengiriman</td>
                            <td class="py-1">{{ $d->schedule_date ? date('d-m-Y', strtotime($d->schedule_date)) : '-' }}</td>
                        </tr>
                    </table>

                    <!-- DETAIL ORDER -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Detail Order</h3>
                    @if($d->details->count())
                        <div class="border rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 text-gray-600">
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
                                            <tr class="border-t">
                                                <td class="p-2">{{ $item->grade->name_grade }}</td>
                                                <td class="p-2 text-center uppercase">{{ $item->type }}</td>
                                                <td class="p-2 text-center">{{ number_format($item->qty, 2, ',', '.') }}</td>
                                                <td class="p-2 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                <td class="p-2 text-right font-semibold text-green-600">Rp
                                                    {{ number_format($item->total, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-100 text-gray-800 text-xs">
                                        <tr>
                                            <td colspan="4" class="p-2 text-right font-semibold">Subtotal</td>
                                            <td class="p-2 text-right font-semibold">Rp {{ number_format($d->details->sum('total'), 0, ',', '.') }}</td>
                                        </tr>
                                        @if($d->delivery_distance > 0)
                                        <tr>
                                            <td colspan="4" class="p-2 text-right font-semibold">Biaya Pengiriman ({{ $d->delivery_distance }} km)</td>
                                            <td class="p-2 text-right font-semibold text-orange-600">Rp {{ number_format($d->delivery_fee, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                        <tr class="border-t font-bold">
                                            <td colspan="4" class="p-2 text-right text-sm">Grand Total</td>
                                            <td class="p-2 text-right text-sm text-green-700">Rp {{ number_format($d->grand_total > 0 ? $d->grand_total : $d->details->sum('total'), 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 italic">Tidak ada detail order.</p>
                    @endif

                </div>

                <!-- FOOTER -->
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-center">
                    <button type="button" onclick="closeDetail({{ $d->id }})"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endforeach

    <script>
        // TAB SWITCHING
        function switchTab(tab) {
            // Hide all panels
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            // Reset all tab buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-red-900', 'text-white', 'shadow-md');
                b.classList.add('bg-gray-200', 'text-gray-600');
                // Reset badge
                const badge = b.querySelector('span');
                if (badge) {
                    badge.classList.remove('bg-white/20', 'text-white');
                    badge.classList.add('bg-gray-300', 'text-gray-600');
                }
            });

            // Show selected panel
            document.getElementById('panel-' + tab).classList.remove('hidden');

            // Activate selected tab
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.classList.remove('bg-gray-200', 'text-gray-600');
            activeBtn.classList.add('bg-red-900', 'text-white', 'shadow-md');
            const activeBadge = activeBtn.querySelector('span');
            if (activeBadge) {
                activeBadge.classList.remove('bg-gray-300', 'text-gray-600');
                activeBadge.classList.add('bg-white/20', 'text-white');
            }
        }

        const detailMapInstances = {};

        function openDetail(id) {
            document.getElementById('detailModal-' + id).classList.remove('hidden');
            document.getElementById('detailModal-' + id).classList.add('flex');

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

        function closeDetail(id) {
            document.getElementById('detailModal-' + id).classList.add('hidden');
            document.getElementById('detailModal-' + id).classList.remove('flex');
        }
    </script>

@endsection