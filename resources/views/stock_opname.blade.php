@extends('main')
@section('title', 'Stock Opname')
@section('container')

<div class="p-6 bg-gray-50/50 min-h-screen">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Stock Opname</h1>
        <p class="text-sm text-gray-500">Pengecekan dan penyesuaian stok material</p>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
    <div id="successAlert" class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('successAlert').remove()" class="ml-auto text-green-400 hover:text-green-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div id="errorAlert" class="mb-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <span class="font-semibold">Gagal menyimpan:</span>
            <ul class="mt-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- IMPORT EXCEL ALERT --}}
    <div id="importAlert" class="hidden mb-4 flex items-start gap-3 px-5 py-3.5 rounded-xl text-sm shadow-sm transition duration-300">
    </div>

    @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
    {{-- FORM OPNAME --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"></path></svg>
                <h2 class="text-xl font-bold text-gray-800">Input Opname Baru</h2>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="downloadOpnameTemplate()" 
                    class="border border-emerald-600 text-emerald-600 hover:bg-emerald-50 font-semibold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Template</span>
                </button>
                <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <span>Unggah Excel</span>
                    <input type="file" id="excel_file" accept=".xlsx,.xls,.csv" class="hidden" onchange="importExcel(this)">
                </label>
            </div>
        </div>

        <form action="/stock-opname" method="POST" class="p-6">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Opname</label>
                <input type="date" name="opname_date" value="{{ date('Y-m-d') }}"
                    class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition w-full md:w-64" required>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 font-medium">Material</th>
                            <th class="px-4 py-3 font-medium">Tipe</th>
                            <th class="px-4 py-3 font-medium text-center">Stok Sistem</th>
                            <th class="px-4 py-3 font-medium text-center">Stok Aktual</th>
                            <th class="px-4 py-3 font-medium text-center">Selisih</th>
                            <th class="px-4 py-3 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($inventories as $inv)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900">
                                {{ $inv->name_material }}
                                <input type="hidden" name="items[{{ $inv->id_inventory }}][inventory_id]" value="{{ $inv->id_inventory }}">
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wide bg-gray-100 text-gray-600">
                                    {{ $inv->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-gray-700" id="system_{{ $inv->id_inventory }}">
                                {{ $inv->stock }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="text" name="items[{{ $inv->id_inventory }}][stock_actual]"
                                    class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition decimal-format"
                                    oninput="calcDiff({{ $inv->id_inventory }}, {{ $inv->stock }}, this.value)"
                                    required>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span id="diff_{{ $inv->id_inventory }}" class="font-semibold text-gray-400">—</span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="items[{{ $inv->id_inventory }}][notes]" placeholder="Opsional"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
                    Simpan Opname
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- RIWAYAT OPNAME --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-xl font-bold text-gray-800">Riwayat Opname</h2>
            </div>

            {{-- FILTER RIWAYAT --}}
            <form method="GET" action="/stock-opname" class="flex flex-wrap items-center gap-3">
                <input type="date" name="date" value="{{ request('date') }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <select name="material" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white">
                    <option value="">Semua Material</option>
                    @foreach($inventories as $inv)
                        <option value="{{ $inv->id_inventory }}" {{ request('material') == $inv->id_inventory ? 'selected' : '' }}>{{ $inv->name_material }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-50 transition">
                    Filter
                </button>
                @if(request('date') || request('material'))
                    <a href="/stock-opname" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-white text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Material</th>
                        <th class="px-6 py-4 font-medium text-center">Stok Sistem</th>
                        <th class="px-6 py-4 font-medium text-center">Stok Aktual</th>
                        <th class="px-6 py-4 font-medium text-center">Selisih</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium">Diperiksa</th>
                        <th class="px-6 py-4 font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($history as $opname)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium">{{ $opname->opname_date->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ $opname->inventory->name_material ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">{{ rtrim(rtrim(number_format($opname->stock_system, 3, ',', '.'), '0'), ',') }}</td>
                        <td class="px-6 py-4 text-center">{{ rtrim(rtrim(number_format($opname->stock_actual, 3, ',', '.'), '0'), ',') }}</td>
                        <td class="px-6 py-4 text-center font-semibold
                            {{ $opname->difference == 0 ? 'text-green-600' : ($opname->difference > 0 ? 'text-blue-600' : 'text-red-600') }}">
                            {{ $opname->difference > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($opname->difference, 3, ',', '.'), '0'), ',') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $absDiff = abs($opname->difference);
                                $pct = $opname->stock_system > 0 ? ($absDiff / $opname->stock_system) * 100 : 0;
                            @endphp
                            @if($opname->difference == 0)
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Cocok</span>
                            @elseif($pct <= 5)
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-yellow-100 text-yellow-700">Selisih Kecil</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-red-100 text-red-700">Selisih Besar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $opname->checker->name_user ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $opname->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-gray-500 font-medium">Belum ada riwayat opname</p>
                                <p class="text-gray-400 text-xs">Input opname di atas untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($history->hasPages())
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-100 bg-white text-sm text-gray-600">
            <div class="flex items-center gap-4">
                <a href="{{ $history->previousPageUrl() ?? '#' }}"
                   class="{{ $history->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <span class="font-medium text-gray-800">{{ $history->currentPage() }}</span>
                <span>{{ $history->firstItem() ?? 0 }}-{{ $history->lastItem() ?? 0 }} of {{ $history->total() }}</span>
                <a href="{{ $history->nextPageUrl() ?? '#' }}"
                   class="{{ !$history->hasMorePages() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function calcDiff(id, systemStock, actualValue) {
    const diffEl = document.getElementById('diff_' + id);
    if (actualValue === '' || isNaN(parseFloat(actualValue.replace(',', '.')))) {
        diffEl.innerText = '—';
        diffEl.className = 'font-semibold text-gray-400';
        return;
    }

    const parsedActual = parseFloat(actualValue.replace(',', '.'));
    const diff = parsedActual - systemStock;
    const absDiff = Math.abs(diff);
    const pct = systemStock > 0 ? (absDiff / systemStock) * 100 : 0;

    let formattedDiff = diff.toFixed(3).replace(/\.?0+$/, '');
    diffEl.innerText = (diff > 0 ? '+' : '') + formattedDiff;

    if (diff === 0) {
        diffEl.className = 'font-semibold text-green-600';
    } else if (pct <= 5) {
        diffEl.className = 'font-semibold text-yellow-600';
    } else {
        diffEl.className = 'font-semibold text-red-600';
    }
}

// Auto-hide success alert
const sa = document.getElementById('successAlert');
if (sa) {
    setTimeout(() => {
        sa.style.transition = 'opacity 0.5s';
        sa.style.opacity = '0';
        setTimeout(() => sa.remove(), 500);
    }, 4000);
}

// Format decimal inputs (comma handling)
document.body.addEventListener('input', function(e) {
    if (e.target.classList.contains('decimal-format')) {
        let val = e.target.value.replace(/[^0-9,.]/g, '');
        val = val.replace(/\./g, ',');
        let parts = val.split(',');
        if (parts.length > 2) {
            val = parts[0] + ',' + parts.slice(1).join('').replace(/,/g, '');
        }
        e.target.value = val;
    }
});

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        this.querySelectorAll('.decimal-format').forEach(input => {
            input.value = input.value.replace(/,/g, '.');
        });
    });
});
</script>

<!-- SheetJS Library -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// Download Dynamic Template
function downloadOpnameTemplate() {
    const formRows = document.querySelectorAll('form tbody tr');
    const templateData = [];
    
    // Header template
    templateData.push(['Nama Material', 'Tipe', 'Stok Sistem', 'Stok Aktual (Wajib diisi)', 'Catatan (Opsional)']);

    formRows.forEach(tr => {
        const nameEl = tr.cells[0];
        if (!nameEl) return;
        
        const name = nameEl.innerText.trim();
        const typeEl = tr.cells[1] ? tr.cells[1].querySelector('span') : null;
        const type = typeEl ? typeEl.innerText.trim() : '';
        const systemStockText = tr.querySelector('td[id^="system_"]');
        const systemStock = systemStockText ? parseFloat(systemStockText.innerText.trim()) : 0;

        templateData.push([name, type, systemStock, '', '']);
    });

    // Buat worksheet dan workbook
    const ws = XLSX.utils.aoa_to_sheet(templateData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Template Stock Opname");

    // Format lebar kolom agar rapi
    const wscols = [
        {wch: 25}, // Nama Material
        {wch: 15}, // Tipe
        {wch: 15}, // Stok Sistem
        {wch: 25}, // Stok Aktual
        {wch: 30}  // Catatan
    ];
    ws['!cols'] = wscols;

    // Unduh file
    XLSX.writeFile(wb, "Template_Stock_Opname.xlsx");
}

// Import Excel File
function importExcel(input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            
            // Ambil sheet pertama
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            
            // Ubah sheet menjadi array of arrays (baris & kolom)
            const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
            
            if (rows.length < 2) {
                showImportAlert('File Excel kosong atau tidak memiliki baris data.', 'error');
                input.value = '';
                return;
            }

            const headerRow = rows[0].map(cell => String(cell || '').trim().toLowerCase());
            
            // Kata kunci pencarian kolom
            const materialKeywords = ['material', 'nama material', 'nama', 'bahan', 'nama barang', 'item'];
            const stockKeywords = ['stok aktual', 'aktual', 'stok fisik', 'fisik', 'stok nyata', 'qty', 'jumlah', 'actual'];
            const notesKeywords = ['catatan', 'notes', 'keterangan', 'ket'];

            let materialColIndex = -1;
            let stockColIndex = -1;
            let notesColIndex = -1;

            // Cari index kolom berdasarkan header
            for (let i = 0; i < headerRow.length; i++) {
                const headerVal = headerRow[i];
                if (materialColIndex === -1 && materialKeywords.some(kw => headerVal.includes(kw))) {
                    materialColIndex = i;
                } else if (stockColIndex === -1 && stockKeywords.some(kw => headerVal.includes(kw))) {
                    stockColIndex = i;
                } else if (notesColIndex === -1 && notesKeywords.some(kw => headerVal.includes(kw))) {
                    notesColIndex = i;
                }
            }

            // Validasi keberadaan kolom wajib
            if (materialColIndex === -1) {
                showImportAlert('Kolom nama material tidak terdeteksi. Pastikan file Excel memiliki header seperti "Nama Material" atau "Material".', 'error');
                input.value = '';
                return;
            }
            if (stockColIndex === -1) {
                showImportAlert('Kolom stok aktual tidak terdeteksi. Pastikan file Excel memiliki header seperti "Stok Aktual" atau "Qty".', 'error');
                input.value = '';
                return;
            }

            // Ambil semua input material dari form HTML untuk dipetakan
            const formRows = document.querySelectorAll('form tbody tr');
            const materialInputs = {};
            
            formRows.forEach(tr => {
                const nameEl = tr.cells[0];
                if (!nameEl) return;
                
                const name = nameEl.innerText.trim();
                const actualInput = tr.querySelector('input[name*="[stock_actual]"]');
                const notesInput = tr.querySelector('input[name*="[notes]"]');
                const systemStockText = tr.querySelector('td[id^="system_"]');
                
                if (actualInput) {
                    const id = actualInput.name.match(/items\[(\d+)\]/)[1];
                    const systemStock = parseFloat(systemStockText ? systemStockText.innerText.trim() : 0);
                    materialInputs[name.toLowerCase()] = {
                        id: id,
                        actualInput: actualInput,
                        notesInput: notesInput,
                        systemStock: systemStock
                    };
                }
            });

            let matchedCount = 0;
            let unmatchedMaterials = [];
            let invalidValuesCount = 0;

            // Proses baris-baris data (mulai dari baris ke-2)
            for (let r = 1; r < rows.length; r++) {
                const row = rows[r];
                if (!row || row.length === 0) continue;

                const materialNameRaw = row[materialColIndex];
                if (!materialNameRaw) continue;

                const materialName = String(materialNameRaw).trim().toLowerCase();
                const stockValRaw = row[stockColIndex];
                const notesVal = notesColIndex !== -1 ? String(row[notesColIndex] || '').trim() : '';

                // Cari kecocokan material
                const targetMaterial = materialInputs[materialName];
                if (targetMaterial) {
                    const stockValFloat = parseFloat(stockValRaw);
                    if (isNaN(stockValFloat) || stockValFloat < 0) {
                        invalidValuesCount++;
                        continue;
                    }
                    
                    // Isi form input
                    targetMaterial.actualInput.value = stockValFloat;
                    if (targetMaterial.notesInput && notesVal) {
                        targetMaterial.notesInput.value = notesVal;
                    }

                    // Picu fungsi calcDiff bawaan
                    calcDiff(targetMaterial.id, targetMaterial.systemStock, stockValFloat);
                    matchedCount++;
                } else {
                    unmatchedMaterials.push(String(materialNameRaw).trim());
                }
            }

            // Tampilkan laporan rangkuman
            if (matchedCount > 0) {
                let msg = `<strong>Berhasil memproses Excel!</strong> ${matchedCount} material telah terisi secara otomatis.`;
                if (invalidValuesCount > 0) {
                    msg += `<br>• ${invalidValuesCount} baris memiliki nilai stok tidak valid (diabaikan).`;
                }
                if (unmatchedMaterials.length > 0) {
                    msg += `<br>• ${unmatchedMaterials.length} material tidak cocok dengan sistem: <em>${unmatchedMaterials.join(', ')}</em>`;
                }
                showImportAlert(msg, unmatchedMaterials.length > 0 ? 'warning' : 'success');
            } else {
                showImportAlert('Tidak ada nama material di Excel yang cocok dengan database.', 'warning');
            }

        } catch (error) {
            console.error(error);
            showImportAlert('Gagal membaca file Excel. Pastikan format file benar.', 'error');
        }
        
        // Reset file input
        input.value = '';
    };
    reader.readAsArrayBuffer(file);
}

function showImportAlert(message, type) {
    const alertEl = document.getElementById('importAlert');
    if (!alertEl) return;

    // Bersihkan classes sebelumnya
    alertEl.className = 'mb-4 flex items-start gap-3 px-5 py-3.5 rounded-xl text-sm shadow-sm transition duration-300';
    alertEl.innerHTML = '';

    // Icon SVG berdasarkan tipe
    let svgIcon = '';
    if (type === 'success') {
        alertEl.classList.add('bg-green-50', 'border', 'border-green-200', 'text-green-700');
        svgIcon = `<svg class="w-5 h-5 flex-shrink-0 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
    } else if (type === 'warning') {
        alertEl.classList.add('bg-yellow-50', 'border', 'border-yellow-200', 'text-yellow-700');
        svgIcon = `<svg class="w-5 h-5 flex-shrink-0 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
    } else {
        alertEl.classList.add('bg-red-50', 'border', 'border-red-200', 'text-red-700');
        svgIcon = `<svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
    }

    const contentDiv = document.createElement('div');
    contentDiv.className = 'flex-1';
    contentDiv.innerHTML = message;

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'ml-auto text-gray-400 hover:text-gray-600 transition flex-shrink-0';
    closeBtn.onclick = () => alertEl.classList.add('hidden');
    closeBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;

    alertEl.innerHTML = svgIcon;
    alertEl.appendChild(contentDiv);
    alertEl.appendChild(closeBtn);

    alertEl.classList.remove('hidden');
}
</script>

@endsection
