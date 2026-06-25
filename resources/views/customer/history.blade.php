@extends('main')
@section('title', 'Riwayat Pesanan Customer')
@section('container')

    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Riwayat Pesanan</h1>
                <p class="text-sm text-gray-500 mt-1">Daftar pesanan Anda yang sudah selesai atau ditolak.</p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="text-blue-600 hover:underline text-sm font-semibold">
                ← Kembali ke Dashboard
            </a>
        </div>

        <div class="bg-white rounded-xl shadow border overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 text-left">Kode</th>
                            <th class="p-3 text-center">Tanggal</th>
                            <th class="p-3 text-right">Grand Total</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyCR as $h)
                        <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="toggleHistoryDetail({{ $h->id }})">
                            <td class="p-3 text-xs font-mono text-gray-500">
                                <div class="flex items-center gap-2">
                                    <svg id="historyIcon-{{ $h->id }}" class="w-4 h-4 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    {{ $h->request_code }}
                                </div>
                            </td>
                            <td class="p-3 text-center text-gray-500">{{ date('d-m-Y', strtotime($h->tanggal)) }}</td>
                            <td class="p-3 text-right font-semibold">Rp {{ number_format($h->grand_total, 0, ',', '.') }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    @if($h->status == 'done') bg-green-100 text-green-700
                                    @elseif($h->status == 'rejected') bg-red-100 text-red-600
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $h->status == 'done' ? 'Selesai' : 'Ditolak' }}
                                </span>
                            </td>
                            <td class="p-3 text-center" onclick="event.stopPropagation();">
                                @if($h->status == 'done')
                                <a href="/customer-request/invoice-pdf/{{ $h->id }}?download=1"
                                   class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded text-xs font-semibold hover:bg-yellow-200 inline-block">📄 Invoice</a>
                                @endif
                                @if($h->status == 'done' || $h->status == 'rejected')
                                    @if($h->status == 'done')
                                        <a href="/customer-request/spk-pdf/{{ $h->id }}?download=1" class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-semibold hover:bg-orange-200 inline-block ml-1">📋 SPK</a>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        <!-- Row Detail (Expandable) -->
                        <tr id="historyDetail-{{ $h->id }}" class="hidden">
                            <td colspan="5" class="p-0 border-t bg-gray-50/50">
                                <div class="p-4 md:p-6 text-gray-700">
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <!-- Info Kolom Kiri -->
                                        <div class="space-y-4 lg:col-span-1 border-r border-gray-200 pr-0 lg:pr-6">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Pengiriman</h4>
                                            
                                            <div class="space-y-2.5 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Nama Penerima</span>
                                                    <span class="font-semibold text-gray-800">{{ $h->customer_name }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">No HP</span>
                                                    <span class="font-semibold text-gray-800">{{ $h->phone ?? '-' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-gray-500 mb-0.5">Alamat Kirim</span>
                                                    <span class="font-semibold text-gray-800 bg-white p-2 rounded border border-gray-200 text-xs leading-relaxed">{{ $h->address }}</span>
                                                </div>
                                                @if($h->note)
                                                <div class="flex flex-col">
                                                    <span class="text-gray-500 mb-0.5">Catatan</span>
                                                    <span class="font-semibold text-gray-800 bg-white p-2 rounded border border-gray-200 text-xs leading-relaxed">{{ $h->note }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Item Detail Kolom Kanan -->
                                        <div class="lg:col-span-2 space-y-4">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Item Beton & Biaya</h4>
                                            
                                            @if($h->details->count())
                                            <div class="border rounded-lg overflow-hidden text-sm shadow-sm bg-white">
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
                                                        @foreach($h->details as $item)
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
                                                            <td class="p-2 text-right font-semibold">Rp {{ number_format($h->details->sum('total'), 0, ',', '.') }}</td>
                                                        </tr>
                                                        @if($h->discount_amount > 0)
                                                        <tr class="text-red-600">
                                                            <td colspan="4" class="p-2 text-right font-semibold">
                                                                Diskon
                                                                @if($h->discount_type == 'percentage')
                                                                    ({{ number_format($h->discount_value, 0) }}%)
                                                                @endif
                                                            </td>
                                                            <td class="p-2 text-right font-semibold">- Rp {{ number_format($h->discount_amount, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($h->delivery_fee > 0)
                                                        <tr>
                                                            <td colspan="4" class="p-2 text-right font-semibold">
                                                                Biaya Pengiriman ({{ $h->delivery_distance }} km)
                                                                @if($h->delivery_distance > 25)
                                                                    <span class="text-[10px] text-gray-500 font-normal block">
                                                                        ({{ ceil(($h->delivery_distance - 25) / 5) }} × Rp 20.000 × {{ number_format($h->details->sum('qty'), 0, ',', '.') }} m³)
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="p-2 text-right font-semibold text-orange-600">Rp {{ number_format($h->delivery_fee, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr class="border-t font-bold text-sm bg-gray-100/50">
                                                            <td colspan="4" class="p-2 text-right">Grand Total</td>
                                                            <td class="p-2 text-right text-green-700">Rp {{ number_format($h->grand_total > 0 ? $h->grand_total : $h->details->sum('total'), 0, ',', '.') }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-400">Belum ada riwayat pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t bg-gray-50">
                {{ $historyCR->links() }}
            </div>
        </div>

    </div>

    <script>
        function toggleHistoryDetail(id) {
            const tr = document.getElementById('historyDetail-' + id);
            const icon = document.getElementById('historyIcon-' + id);
            if (tr.classList.contains('hidden')) {
                tr.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                tr.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
@endsection
