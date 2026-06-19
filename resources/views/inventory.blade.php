@extends('main')
@section('title','Inventory')

@section('container')

<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <h1 class="text-2xl font-bold text-gray-800">Inventory</h1>

    <form action="/inventory" method="GET" class="flex gap-2 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari material atau grade..." 
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-900 w-full sm:w-64">
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm border border-gray-300 font-medium transition">
            Search
        </button>
        @if(request('search'))
            <a href="/inventory" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm border border-red-200 font-medium transition">
                Clear
            </a>
        @endif
    </form>
</div>

<div class="p-6">
    <div class="flex flex-col gap-8">

        {{-- ================= INVENTORY ================= --}}
        <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-white">
                <h2 class="text-xl font-semibold text-gray-800">Inventory Material</h2>
                @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                <button onclick="openModal('addInventory')" 
                    class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium text-sm transition">
                    + Add Material
                </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-medium">Material Name</th>
                            <th class="px-6 py-4 font-medium">Type</th>
                            <th class="px-6 py-4 font-medium text-right">Stock</th>
                            @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                            <th class="px-6 py-4 font-medium text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($inventories as $inv)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $inv->name_material }}</td>
                            <td class="px-6 py-4">{{ $inv->type }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold {{ $inv->stock == 0 ? 'text-red-500' : 'text-green-600' }}">
                                    {{ number_format($inv->stock, 0, ',', '.') }} {{ $inv->unit }}
                                </span>
                            </td>
                            @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                            <td class="px-6 py-4 text-center space-x-3">
                                <button onclick="openModal('editInventory{{ $inv->id_inventory }}')" 
                                    class="text-yellow-500 hover:text-yellow-600 font-medium transition cursor-pointer">
                                    Edit
                                </button>
                                <a href="/inventory/delete/{{ $inv->id_inventory }}"
                                    onclick="return confirmDelete()"
                                    class="text-red-500 hover:text-red-600 font-medium transition cursor-pointer">
                                    Delete
                                 </a>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Inventory -->
            <div class="flex items-center justify-end px-6 py-3 border-t border-gray-200 bg-gray-50 text-sm text-gray-600 rounded-b-2xl">
                <div class="flex items-center gap-4">
                    <a href="{{ $inventories->appends(['grade_page' => request('grade_page')])->previousPageUrl() ?? '#' }}" 
                       class="{{ $inventories->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    
                    <span class="font-medium text-gray-800">{{ $inventories->currentPage() }}</span>
                    
                    <span>{{ $inventories->firstItem() ?? 0 }}-{{ $inventories->lastItem() ?? 0 }} of {{ $inventories->total() }}</span>
                    
                    <a href="{{ $inventories->appends(['grade_page' => request('grade_page')])->nextPageUrl() ?? '#' }}" 
                       class="{{ !$inventories->hasMorePages() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
            
            @foreach($inventories as $inv)
            {{-- MODAL EDIT INVENTORY --}}
            <div id="editInventory{{ $inv->id_inventory }}" class="modal hidden">
                <div class="modal-box">
                    <h3 class="font-bold mb-3 text-lg text-left">Edit Material</h3>

                    <form action="/inventory/update/{{ $inv->id_inventory }}" method="POST" class="text-left">
                        @csrf

                        <input type="text" name="name_material" value="{{ $inv->name_material }}" class="input">

                        <select name="type" class="input mt-2">
                            <option {{ $inv->type=='cement'?'selected':'' }}>cement</option>
                            <option {{ $inv->type=='FA'?'selected':'' }}>FA</option>
                            <option {{ $inv->type=='Sand'?'selected':'' }}>Sand</option>
                            <option {{ $inv->type=='Aggregate'?'selected':'' }}>Aggregate</option>
                            <option {{ $inv->type=='Admixture'?'selected':'' }}>Admixture</option>
                        </select>
                        
                        <input type="text" name="stock" value="0" class="input mt-2 number-format" placeholder="Stock">

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" onclick="closeModal('editInventory{{ $inv->id_inventory }}')" class="btn-cancel">Cancel</button>
                            <button class="btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach

        </div>

        {{-- ================= GRADE ================= --}}
        <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden mb-6">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-white">
                <h2 class="text-xl font-semibold text-gray-800">Grade Beton</h2>
                @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                <div class="flex gap-2">
                    <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition flex items-center gap-1.5 shadow-sm" title="Import file Excel berisi resep bahan (FA / NFA) atau harga FA / NFA">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        <span>Import Excel</span>
                        <input type="file" id="excel_recipe" accept=".xlsx,.xls,.csv" class="hidden" onchange="importRecipeExcel(this)">
                    </label>
                    <button onclick="openModal('addGrade')" 
                        class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium text-sm transition border-0">
                        + Add Grade
                    </button>
                </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-medium text-center" colspan="2">GRADE</th>
                            <th class="px-6 py-4 font-medium text-right">FA</th>
                            <th class="px-6 py-4 font-medium text-right">NFA</th>
                            @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                            <th class="px-6 py-4 font-medium text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($grade as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900 text-center w-24">
                                {{ $item->name_grade }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900 text-center w-24">
                                {{ $item->mpa }}
                            </td>
                            <td class="px-6 py-4 text-right w-32">
                                {{ number_format($item->harga_fa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right w-32">
                                {{ number_format($item->harga_nfa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <button onclick="openModal('detail{{ $item->id_grade }}')" 
                                    class="text-blue-500 hover:text-blue-600 font-medium transition cursor-pointer">
                                    Detail
                                </button>
                                @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                                <button onclick="openModal('editGrade{{ $item->id_grade }}')" 
                                    class="text-yellow-500 hover:text-yellow-600 font-medium transition cursor-pointer">
                                    Edit
                                </button>
                                <a href="/grade/delete/{{ $item->id_grade }}"
                                    onclick="return confirmDelete()"
                                    class="text-red-500 hover:text-red-600 font-medium transition cursor-pointer">
                                    Delete
                                 </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Grade -->
            <div class="flex items-center justify-end px-6 py-3 border-t border-gray-200 bg-gray-50 text-sm text-gray-600 rounded-b-2xl">
                <div class="flex items-center gap-4">
                    <a href="{{ $grade->appends(['inventory_page' => request('inventory_page')])->previousPageUrl() ?? '#' }}" 
                       class="{{ $grade->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    
                    <span class="font-medium text-gray-800">{{ $grade->currentPage() }}</span>
                    
                    <span>{{ $grade->firstItem() ?? 0 }}-{{ $grade->lastItem() ?? 0 }} of {{ $grade->total() }}</span>
                    
                    <a href="{{ $grade->appends(['inventory_page' => request('inventory_page')])->nextPageUrl() ?? '#' }}" 
                       class="{{ !$grade->hasMorePages() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            @foreach($grade as $item)
            {{-- MODAL DETAIL --}}
            <div id="detail{{ $item->id_grade }}" class="modal hidden">
                <div class="modal-box w-11/12 max-w-[500px] text-left whitespace-normal">
            
                    <h3 class="text-lg font-bold mb-4">Detail Grade Beton</h3>
            
                    <!-- INFO GRADE -->
                    <div class="mb-4 space-y-2">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Nama Grade</span>
                            <span class="font-semibold text-gray-900">{{ $item->name_grade }}</span>
                        </div>
            
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">MPA</span>
                            <span class="font-semibold text-gray-900">{{ $item->mpa }}</span>
                        </div>
            
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">FA 15%</span>
                            <span class="font-semibold text-gray-900">
                                Rp {{ number_format($item->harga_fa, 0, ',', '.') }}
                            </span>
                        </div>
            
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">NFA</span>
                            <span class="font-semibold text-gray-900">
                                Rp {{ number_format($item->harga_nfa, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
            
                    <!-- COMPOSITION -->
                    <div class="mt-4">
                        <h4 class="font-semibold mb-2 text-gray-800">Resep Komposisi</h4>

                        @php
                            $faComps    = $item->composition->where('recipe_type', 'FA');
                            $nfaComps   = $item->composition->where('recipe_type', 'NFA');
                            $otherComps = $item->composition->filter(fn($c) => !in_array($c->recipe_type, ['FA','NFA']));
                        @endphp

                        {{-- FA --}}
                        @if($faComps->count() > 0)
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-emerald-700 mb-1 uppercase tracking-wide">🟢 FA (Fly Ash)</p>
                            <div class="border border-emerald-100 rounded-lg bg-emerald-50 overflow-hidden">
                                @foreach($faComps as $c)
                                <div class="flex justify-between border-b border-emerald-100 last:border-0 px-4 py-2 text-sm text-gray-700">
                                    <span>{{ $c->inventory->name_material }}</span>
                                    <span class="font-medium">{{ rtrim(rtrim(number_format($c->qty, 2, ',', '.'), '0'), ',') }} {{ $c->inventory->unit }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- NFA --}}
                        @if($nfaComps->count() > 0)
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-blue-700 mb-1 uppercase tracking-wide">🔵 NFA (Non Fly Ash)</p>
                            <div class="border border-blue-100 rounded-lg bg-blue-50 overflow-hidden">
                                @foreach($nfaComps as $c)
                                <div class="flex justify-between border-b border-blue-100 last:border-0 px-4 py-2 text-sm text-gray-700">
                                    <span>{{ $c->inventory->name_material }}</span>
                                    <span class="font-medium">{{ rtrim(rtrim(number_format($c->qty, 2, ',', '.'), '0'), ',') }} {{ $c->inventory->unit }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Others (legacy / no type) --}}
                        @if($otherComps->count() > 0)
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Material Lainnya</p>
                            <div class="border rounded-lg bg-gray-50 overflow-hidden">
                                @foreach($otherComps as $c)
                                <div class="flex justify-between border-b last:border-0 px-4 py-2 text-sm text-gray-700">
                                    <span>{{ $c->inventory->name_material }}</span>
                                    <span class="font-medium">{{ rtrim(rtrim(number_format($c->qty, 2, ',', '.'), '0'), ',') }} {{ $c->inventory->unit }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($item->composition->count() === 0)
                        <p class="text-sm text-gray-400 italic text-center py-3">Belum ada komposisi material.</p>
                        @endif
                    </div>
            
                    <!-- ACTION -->
                    <div class="mt-6 text-right">
                        <button onclick="closeModal('detail{{ $item->id_grade }}')" class="btn-cancel">
                            Close
                        </button>
                    </div>
            
                </div>
            </div>

            {{-- MODAL EDIT GRADE --}}
            <div id="editGrade{{ $item->id_grade }}" class="modal hidden">
                <div class="modal-box w-11/12 max-w-[550px] text-left whitespace-normal">

                    <h3 class="text-lg font-semibold mb-4">Edit Grade Beton</h3>

                    <form action="/grade/update/{{ $item->id_grade }}" method="POST">
                        @csrf

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="text-sm">Nama Grade</label>
                            <input type="text" name="name_grade" 
                                value="{{ $item->name_grade }}" 
                                class="input mt-1" required>
                        </div>

                        <!-- MPA -->
                        <div class="mb-3">
                            <label class="text-sm">MPA</label>
                            <input type="text" name="mpa" 
                                value="{{ $item->mpa }}" 
                                class="input mt-1" required>
                        </div>

                        <!-- HARGA -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="text-sm">Harga FA</label>
                                <input type="text" name="harga_fa" 
                                    value="{{ number_format($item->harga_fa, 0, ',', '.') }}"
                                    class="input number-format">
                            </div>

                            <div>
                                <label class="text-sm">Harga NFA</label>
                                <input type="text" name="harga_nfa" 
                                    value="{{ number_format($item->harga_nfa, 0, ',', '.') }}"
                                    class="input number-format">
                            </div>
                        </div>

                        <!-- COMPOSITION -->
                        <div class="mb-2">
                            <label class="text-sm font-medium">Composition Material</label>
                        </div>

                        <div class="max-h-60 overflow-y-auto border rounded bg-white">
                            <table class="w-full text-sm" id="editTable{{ $item->id_grade }}">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="p-2 text-left">Material</th>
                                        <th class="p-2 text-left">Qty</th>
                                        <th class="p-2 text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y">
                                    @foreach($item->composition as $c)
                                    <tr>
                                        <td class="p-2">
                                            <select name="inventory_id[]" class="input py-1 px-2 h-auto text-sm">
                                                @foreach($inventoryList as $inv)
                                                <option value="{{ $inv->id_inventory }}"
                                                    {{ $inv->id_inventory == $c->inventory_id ? 'selected' : '' }}>
                                                    {{ $inv->name_material }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="p-2">
                                            <input type="number" step="0.01" name="qty[]" 
                                                value="{{ $c->qty }}" 
                                                class="input py-1 px-2 h-auto text-sm">
                                        </td>

                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:bg-red-50 rounded p-1">
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ADD ROW -->
                        <button type="button" 
                            onclick="addRowEdit({{ $item->id_grade }})"
                            class="text-blue-600 text-sm mt-2 font-medium">
                            + Tambah Material
                        </button>

                        <!-- ACTION -->
                        <div class="mt-5 flex justify-end gap-2 border-t pt-4">
                            <button type="button" 
                                onclick="closeModal('editGrade{{ $item->id_grade }}')" 
                                class="btn-cancel">
                                Cancel
                            </button>

                            <button class="btn-primary">
                                Update
                            </button>
                        </div>

                    </form>
                </div>
            </div>  
            @endforeach

        </div>

    </div>
</div>

{{-- ================= MODAL ADD INVENTORY ================= --}}
<div id="addInventory" class="modal hidden">
    <div class="modal-box">
        <h3 class="font-bold mb-3">Add Material</h3>

        <form action="/inventory/store" method="POST">
            @csrf

            <input type="text" name="name_material" placeholder="Material Name" class="input">
            
            <select name="type" class="input mt-2">
                <option>cement</option>
                <option>FA</option>
                <option>Sand</option>
                <option>Aggregate</option>
                <option>Admixture</option>
            </select>

            {{-- <input type="number" name="stock" placeholder="Stock" class="input mt-2"> --}}

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addInventory')" class="btn-cancel">Cancel</button>
                <button class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL ADD GRADE ================= --}}
<div id="addGrade" class="modal hidden">
    <div class="modal-box w-11/12 max-w-[500px]">

        <h3 class="text-lg font-semibold mb-4">Add Grade Beton</h3>

        <form action="/grade/store" method="POST">
            @csrf

            <!-- NAME GRADE -->
            <div class="mb-3">
                <label class="text-sm">Nama Grade</label>
                <input type="text" name="name_grade" placeholder="Contoh: K-250"
                    class="input mt-1" required>
            </div>

            <!-- MPA -->
            <div class="mb-3">
                <label class="text-sm">MPA</label>
                <input type="text" name="mpa" placeholder="Contoh: 20 / FC20"
                    class="input mt-1" required>
            </div>

            <!-- HARGA -->
            <div class="grid grid-cols-2 gap-3 mb-3">
                <label>Harga Beton</label>
            <input type="text"
                name="harga"
                class="input number-format">
            </div>

            <!-- COMPOSITION -->
            <div class="mb-2">
                <label class="text-sm font-medium">Composition Material</label>
            </div>

            <table class="w-full text-sm border rounded" id="compositionTable">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Material</th>
                        <th class="p-2 text-left">Qty</th>
                        <th class="p-2 text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="p-2">
                            <select name="inventory_id[]" class="input" >
                                <option value="">-- pilih material --</option>
                                @foreach($inventoryList as $inv)
                                <option value="{{ $inv->id_inventory }}">
                                    {{ $inv->name_material }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="p-2">
                        <input type="number"
                            step="0.001"
                            min="0"
                            name="qty[]">
                        </td>

                        <td class="p-2 text-center">
                            <button type="button" onclick="removeRow(this)" class="text-red-500">
                                ✕
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- BUTTON TAMBAH -->
            <button type="button" onclick="addRow()" 
                class="mt-2 text-blue-600 text-sm">
                + Tambah Material
            </button>

            <!-- ACTION -->
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addGrade')" 
                    class="btn-cancel">
                    Cancel
                </button>

                <button class="btn-primary">
                    Save
                </button>
            </div>

        </form>
    </div>
</div>



{{-- ================= STYLE + SCRIPT ================= --}}
<style>
.modal { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; }
.modal-box { background:white; padding:20px; border-radius:10px; width:400px; }
.input { width:100%; border:1px solid #ddd; padding:8px; border-radius:6px; }
.btn-primary { background:#2563eb; color:white; padding:6px 12px; border-radius:6px; }
.btn-cancel { background:#ccc; padding:6px 12px; border-radius:6px; }
.hidden { display:none; }
</style>

<script>
function openModal(id){ document.getElementById(id).classList.remove('hidden'); }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); }

function confirmDelete() {
    return confirm("Apakah kamu yakin ingin menghapus data ini?");
}

document.querySelectorAll('.number-format').forEach(input => {

input.addEventListener('input', function(e) {
    let value = this.value.replace(/[^0-9]/g, '');

    if (value === '') {
        this.value = '';
        return;
    }

    this.value = new Intl.NumberFormat('id-ID').format(value);
});

});

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {

        this.querySelectorAll('.number-format').forEach(input => {
            input.value = input.value.replace(/\./g, '');
        });

    });
});

function addRow(){
    let table = document.getElementById('compositionTable');

    let row = `
    <tr>
        <td>
            <select name="inventory_id[]" class="input">
                @foreach($inventoryList as $inv)
                <option value="{{ $inv->id_inventory }}">{{ $inv->name_material }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="qty[]" class="input"></td>
    </tr>`;

    table.innerHTML += row;
}

function addRow() {
    let table = document.querySelector("#compositionTable tbody");

    let row = `
    <tr>
        <td class="p-2">
            <select name="inventory_id[]" class="input" required>
                <option value="">-- pilih material --</option>
                @foreach($inventoryList as $inv)
                <option value="{{ $inv->id_inventory }}">
                    {{ $inv->name_material }}
                </option>
                @endforeach
            </select>
        </td>

        <td class="p-2">
            <input type="number" step="0.01" name="qty[]" class="input">
        </td>

        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="text-red-500">
                ✕
            </button>
        </td>
    </tr>
    `;

    table.insertAdjacentHTML('beforeend', row);
}

function removeRow(btn) {
    btn.closest('tr').remove();
}

// EDIT GRADE

function addRowEdit(id) {
    let table = document.querySelector(`#editTable${id} tbody`);

    let row = `
    <tr>
        <td class="p-2">
            <select name="inventory_id[]" class="input">
                @foreach($inventoryList as $inv)
                <option value="{{ $inv->id_inventory }}">
                    {{ $inv->name_material }}
                </option>
                @endforeach
            </select>
        </td>

        <td class="p-2">
            <input type="number" name="qty[]" class="input">
        </td>

        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="text-red-500">
                ✕
            </button>
        </td>
    </tr>
    `;

    table.insertAdjacentHTML('beforeend', row);
}

</script>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
function importRecipeExcel(input) {
    const file = input.files[0];
    if (!file) return;

    const alertId = 'upload-alert-' + Date.now();
    const alertHtml = `
    <div id="${alertId}" class="fixed top-4 right-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3">
        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <span id="alert-msg-${alertId}">Sedang membaca file Excel...</span>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', alertHtml);

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data     = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const ws       = workbook.Sheets[workbook.SheetNames[0]];
            const rows     = XLSX.utils.sheet_to_json(ws, { header: 1 });

            if (rows.length < 2) {
                alert('File Excel kosong atau tidak memiliki baris data.');
                document.getElementById(alertId).remove();
                input.value = '';
                return;
            }

            const headerRow = rows[0].map(cell => String(cell || '').trim());

            // ── Detect column indices ──────────────────────────────────────────
            let hargaFAIndex  = -1;
            let hargaNFAIndex = -1;
            let typeColIndex  = -1;
            for (let i = 0; i < headerRow.length; i++) {
                const h = headerRow[i].toUpperCase();
                if (h === 'FA'   || h === 'HARGA FA')   hargaFAIndex  = i;
                if (h === 'NFA'  || h === 'HARGA NFA')  hargaNFAIndex = i;
                if (h === 'TYPE' || h === 'TIPE' || h === 'RECIPE TYPE') typeColIndex = i;
            }

            // Price Excel = has dedicated FA / NFA price columns
            const isPriceExcel = (hargaFAIndex !== -1 || hargaNFAIndex !== -1);

            const IGNORE_COLS = ['slump', 'w/c', 'keterangan', 'total', 'volume'];

            let parsedGrades = [];

            for (let r = 1; r < rows.length; r++) {
                const row  = rows[r];
                if (!row || row.length === 0) continue;

                const col0 = String(row[0] || '').trim();
                const col1 = String(row[1] || '').trim();
                if (!col0 && !col1) continue;

                let nameGrade  = '';
                let mpa        = '-';
                let harga_fa   = 0;
                let harga_nfa  = 0;
                let recipe_type = '';
                let compositions = {};

                // ── MODE 1 : new format (col0 = "K 100", col1 = "Fc' 8" or MPA) ──
                const isMode1 = col1.toLowerCase().includes('fc')
                    || col0.toUpperCase().startsWith('K ')
                    || col0.toUpperCase().startsWith('FC');

                if (isMode1) {
                    nameGrade = col0;
                    mpa = (!isNaN(col1) && col1 !== '') ? "Fc' " + col1 : col1;

                    if (isPriceExcel) {
                        // ── Price Excel: grab harga only, send empty compositions ──
                        if (hargaFAIndex !== -1) {
                            harga_fa  = parseInt(String(row[hargaFAIndex]  || '0').replace(/[^0-9]/g, '')) || 0;
                        }
                        if (hargaNFAIndex !== -1) {
                            harga_nfa = parseInt(String(row[hargaNFAIndex] || '0').replace(/[^0-9]/g, '')) || 0;
                        }
                        // compositions stays {}, recipe_type stays ''
                    } else {
                        // ── Recipe Excel: grab compositions & recipe_type ──
                        if (typeColIndex !== -1) {
                            recipe_type = String(row[typeColIndex] || '').trim().toUpperCase();
                        }

                        for (let i = 2; i < headerRow.length; i++) {
                            if (i === typeColIndex) continue;
                            const matName = headerRow[i];
                            if (!matName) continue;
                            if (IGNORE_COLS.some(c => matName.toLowerCase().includes(c))) continue;
                            const qty = parseFloat(row[i]);
                            if (!isNaN(qty) && qty > 0) compositions[matName] = qty;
                        }

                        // Auto-detect recipe_type from materials if not explicit
                        if (!recipe_type) {
                            const hasFlyAsh = Object.keys(compositions).some(m => {
                                const ml = m.toLowerCase();
                                return ml.includes('fly ash') || ml === 'fa';
                            });
                            recipe_type = hasFlyAsh ? 'FA' : 'NFA';
                        }
                    }

                } else {
                    // ── MODE 2 : old format (col0=K, col1=125, col2=FA/NFA) ──
                    const col2 = String(row[2] || '').trim().toUpperCase();
                    if (!col1) continue;

                    // Build name WITHOUT the FA/NFA suffix
                    const prefix = col0.toUpperCase() === 'K' ? 'K ' : (col0 ? col0.toUpperCase() + ' ' : 'K ');
                    nameGrade   = prefix + col1;            // e.g. "K 125"
                    recipe_type = (col2 === 'FA' || col2 === 'NFA') ? col2 : '';

                    for (let i = 3; i < headerRow.length; i++) {
                        const matName = headerRow[i];
                        if (!matName) continue;
                        if (IGNORE_COLS.some(c => matName.toLowerCase().includes(c))) continue;
                        const qty = parseFloat(row[i]);
                        if (!isNaN(qty) && qty > 0) compositions[matName] = qty;
                    }

                    // Auto-detect if not set
                    if (!recipe_type) {
                        const hasFlyAsh = Object.keys(compositions).some(m => {
                            const ml = m.toLowerCase();
                            return ml.includes('fly ash') || ml === 'fa';
                        });
                        recipe_type = hasFlyAsh ? 'FA' : 'NFA';
                    }
                }

                parsedGrades.push({ name_grade: nameGrade, mpa, harga_fa, harga_nfa, recipe_type, compositions });
            }

            if (parsedGrades.length > 0) {
                const mode = isPriceExcel ? 'harga' : 'resep';
                document.getElementById(`alert-msg-${alertId}`).innerText =
                    `Menyimpan ${parsedGrades.length} data ${mode} ke database...`;

                fetch('/grade/bulk-store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ grades: parsedGrades })
                })
                .then(r => r.json())
                .then(res => {
                    document.getElementById(alertId).remove();
                    if (res.success) {
                        alert(res.message);
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + res.message);
                    }
                })
                .catch(err => {
                    document.getElementById(alertId).remove();
                    console.error(err);
                    alert('Terjadi kesalahan saat mengirim data ke server.');
                });
            } else {
                document.getElementById(alertId).remove();
                alert('Tidak ada data valid ditemukan di file Excel.');
            }

        } catch (error) {
            console.error(error);
            document.getElementById(alertId).remove();
            alert('Gagal membaca file Excel. Pastikan format file benar.');
        }

        input.value = '';
    };
    reader.readAsArrayBuffer(file);
}
</script>

@endsection