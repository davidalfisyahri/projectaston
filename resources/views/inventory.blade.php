@extends('main')
@section('title','Inventory')

@section('container')

@php
    $lowStockItems = $inventoryList->filter(function($item) {
        return $item->stock <= 1000;
    });
@endphp

@if($lowStockItems->count() > 0)
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <h3 class="font-bold">Peringatan: Stok Material Menipis!</h3>
            <p class="text-sm mt-1">Beberapa material berikut memiliki stok 1.000 kg atau kurang:</p>
            <ul class="list-disc list-inside mt-2 text-sm font-medium">
                @foreach($lowStockItems as $item)
                    <li>{{ $item->name_material }} <span class="text-red-500">(Tersisa: {{ number_format($item->stock, 0, ',', '.') }} kg)</span></li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

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
                <button onclick="openModal('addInventory')" 
                    class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium text-sm transition">
                    + Add Material
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-medium">Material Name</th>
                            <th class="px-6 py-4 font-medium">Type</th>
                            <th class="px-6 py-4 font-medium text-right">Stock</th>
                            <th class="px-6 py-4 font-medium text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($inventories as $inv)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $inv->name_material }}</td>
                            <td class="px-6 py-4">{{ $inv->type }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold {{ $inv->stock == 0 ? 'text-red-500' : 'text-green-600' }}">
                                    {{ number_format($inv->stock, 0, ',', '.') }} Kg
                                </span>
                            </td>
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
                <button onclick="openModal('addGrade')" 
                    class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium text-sm transition border-0">
                    + Add Grade
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-medium">Nama Grade</th>
                            <th class="px-6 py-4 font-medium">MPA</th>
                            <th class="px-6 py-4 font-medium text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($grade as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $item->name_grade }}</td>
                            <td class="px-6 py-4">{{ $item->mpa }}</td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <button onclick="openModal('detail{{ $item->id_grade }}')" 
                                    class="text-blue-500 hover:text-blue-600 font-medium transition cursor-pointer">
                                    Detail
                                </button>
                                <button onclick="openModal('editGrade{{ $item->id_grade }}')" 
                                    class="text-yellow-500 hover:text-yellow-600 font-medium transition cursor-pointer">
                                    Edit
                                </button>
                                <a href="/grade/delete/{{ $item->id_grade }}"
                                    onclick="return confirmDelete()"
                                    class="text-red-500 hover:text-red-600 font-medium transition cursor-pointer">
                                    Delete
                                 </a>
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
                        <h4 class="font-semibold mb-2 text-gray-800">Composition Material</h4>
            
                        <div class="border rounded-lg bg-gray-50 overflow-hidden">
                            @foreach($item->composition as $c)
                            <div class="flex justify-between border-b last:border-0 px-4 py-2 text-sm text-gray-700">
                                <span>{{ $c->inventory->name_material }}</span>
                                <span class="font-medium">
                                    {{ rtrim(rtrim(number_format($c->qty, 2, ',', '.'), '0'), ',') }} Kg
                                </span>
                            </div>
                            @endforeach
                        </div>
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
                <div>
                    <label class="text-sm">Harga FA</label>
                    <input type="text" name="harga_fa" class="input number-format">
                </div>

                <div>
                    <label class="text-sm">Harga NFA</label>
                    <input type="text" name="harga_nfa" class="input number-format">
                </div>
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
                            <input type="number" step="0.01" name="qty[]" class="input">
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

@endsection