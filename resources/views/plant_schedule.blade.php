@extends('main')
@section('title', 'Jadwal Pengiriman - Kepala Plant')
@section('container')

<div class="max-w-6xl mx-auto p-4 md:p-6 space-y-6">

    <!-- HEADER & STATS -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Jadwal Pengiriman & SPK</h1>
            <p class="text-sm text-gray-500">Monitor jadwal operational mixing plant dan cetak SPK untuk pengiriman beton</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <form action="/plant-schedule" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama customer, dll..." 
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 w-full sm:w-64">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    Search
                </button>
                @if(request('search'))
                    <a href="/plant-schedule" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm border border-gray-300 font-medium transition flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- STATS BLOCKS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Total Scheduled</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalScheduled }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Paid / Confirmed</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPaid }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Selesai (Done)</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalDone }}</p>
            </div>
        </div>
    </div>

    <!-- SCHEDULE TABLE -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                    <tr>
                        <th class="p-4 text-left">Kode</th>
                        <th class="p-4 text-left">Customer</th>
                        <th class="p-4 class text-center">Jadwal Pengiriman</th>
                        <th class="p-4 text-left">Proyek</th>
                        <th class="p-4 text-left">Detail Mutu & Qty</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($schedules as $d)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-mono text-xs text-gray-500">{{ $d->request_code }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-gray-800">{{ $d->customer_name }}</div>
                                <div class="text-xs text-gray-400">{{ $d->phone ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-center font-medium text-red-600">
                                {{ $d->schedule_date ? date('d-m-Y', strtotime($d->schedule_date)) : '-' }}
                            </td>
                            <td class="p-4">
                                <div class="text-gray-800 truncate max-w-[150px]" title="{{ $d->ongoing_project }}">
                                    {{ $d->ongoing_project ?? '-' }}
                                </div>
                                @if($d->region)
                                    <div class="text-xs text-gray-400">{{ $d->region }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-xs space-y-1">
                                @forelse($d->details as $item)
                                    <div>
                                        <span class="font-bold text-gray-700">{{ $item->grade->name_grade ?? '-' }}</span> 
                                        <span class="uppercase">({{ $item->type }})</span> 
                                        - <span class="text-blue-600 font-semibold">{{ number_format($item->qty, 1) }}m³</span>
                                    </div>
                                @empty
                                    <div class="text-gray-400 italic">Tidak ada detail</div>
                                @endforelse
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 text-xs rounded-full font-semibold tracking-wide
                                    @if($d->status == 'approved') bg-blue-100 text-blue-700
                                    @elseif($d->status == 'paid') bg-yellow-100 text-yellow-700
                                    @elseif($d->status == 'confirmed_wa') bg-purple-100 text-purple-700
                                    @elseif($d->status == 'scheduled') bg-indigo-100 text-indigo-700
                                    @elseif($d->status == 'done') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $d->status)) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="openDetail({{ $d->id }})" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
                                        View
                                    </button>
                                    <a href="/customer-request/spk-pdf/{{ $d->id }}?download=1" class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-orange-600 shadow-sm flex items-center gap-1 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        SPK
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="font-medium">Tidak ada jadwal pengiriman ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($schedules->hasPages())
            <div class="p-4 border-t bg-gray-50">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================= -->
<!-- DETAIL MODALS -->
<!-- ========================= -->
@foreach($schedules as $d)
    <div id="detailModal-{{ $d->id }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg overflow-hidden max-h-[85vh] flex flex-col">

            <!-- HEADER -->
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Detail Surat Perintah Kerja (SPK)</h2>
                <button onclick="closeDetail({{ $d->id }})" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>

            <!-- BODY -->
            <div class="p-6 overflow-y-auto space-y-4 text-sm">

                <!-- IDENTITAS -->
                <h3 class="font-semibold text-gray-700 border-b pb-1">Identitas Proyek & Pengiriman</h3>
                <table class="w-full">
                    <tr><td class="py-1 text-gray-500 w-44">Kode Request</td><td class="py-1 font-mono font-medium">{{ $d->request_code }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Nama Customer</td><td class="py-1 font-semibold text-gray-800">{{ $d->customer_name }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Phone / HP</td><td class="py-1">{{ $d->phone ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Region</td><td class="py-1">{{ $d->region ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Alamat Pengiriman</td><td class="py-1">{{ $d->address ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Jadwal Pengiriman</td><td class="py-1 font-semibold text-red-600">{{ $d->schedule_date ? date('d-m-Y', strtotime($d->schedule_date)) : '-' }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Jarak / Estimasi</td><td class="py-1">{{ $d->delivery_distance ? $d->delivery_distance . ' km' : '-' }}</td></tr>
                    <tr><td class="py-1 text-gray-500">Note / Catatan</td><td class="py-1 italic text-gray-600">{{ $d->note ?? '-' }}</td></tr>
                </table>

                <!-- PETA LOKASI -->
                @if($d->delivery_latitude && $d->delivery_longitude)
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Lokasi di Peta</h3>
                    <div class="my-2 p-2 bg-gray-50 border rounded-lg flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-500 font-medium">Koordinat:</span>
                            <span class="text-xs font-mono font-semibold">{{ $d->delivery_latitude }}, {{ $d->delivery_longitude }}</span>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $d->delivery_latitude }},{{ $d->delivery_longitude }}" target="_blank" class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-100 transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Buka Google Maps
                        </a>
                    </div>
                @endif

                <!-- PROJECT -->
                <h3 class="font-semibold text-gray-700 border-b pb-1">Project</h3>
                <table class="w-full">
                    <tr><td class="py-1 text-gray-500 w-44">Nama Proyek</td><td class="py-1 font-semibold text-gray-800">{{ $d->ongoing_project ?? '-' }}</td></tr>
                </table>

                <!-- DETAIL ORDER -->
                <h3 class="font-semibold text-gray-700 border-b pb-1">Detail Order Beton</h3>
                @if($d->details->count())
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-700 font-medium border-b">
                                <tr>
                                    <th class="p-3 text-left">Grade / Mutu</th>
                                    <th class="p-3 text-center">Type Slump</th>
                                    <th class="p-3 text-center">Volume (m³)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($d->details as $item)
                                    <tr class="border-t">
                                        <td class="p-3 font-semibold text-gray-800">{{ $item->grade->name_grade }}</td>
                                        <td class="p-3 text-center uppercase">{{ $item->type }}</td>
                                        <td class="p-3 text-center font-bold text-blue-600">{{ number_format($item->qty, 1, ',', '.') }} m³</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 italic">Tidak ada detail order.</p>
                @endif

            </div>

            <!-- FOOTER -->
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-between items-center w-full">
                <a href="/customer-request/spk-pdf/{{ $d->id }}?download=1" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download SPK PDF
                </a>
                <button type="button" onclick="closeDetail({{ $d->id }})" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>
@endforeach

<script>
function openDetail(id) {
    const modal = document.getElementById('detailModal-' + id);
    if(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeDetail(id) {
    const modal = document.getElementById('detailModal-' + id);
    if(modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}
</script>

@endsection
