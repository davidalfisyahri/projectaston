@extends('main')
@section('title', 'procurement')
@section('container')
<h1 class="text-2xl font-bold mb-6">Procurement</h1>


    @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
    <!-- FORM -->
    <div class="bg-white rounded-2xl shadow p-6">
        <form action="/procurement/store" method="POST">
            @csrf

            <!-- HEADER FORM -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">

                <input type="text" name="no_po" class="input" required placeholder="No PO" >

                <input type="date" name="tanggal" class="input" required>

                <input type="text" name="name_pt" class="input" required placeholder="Nama PT Supplier">

                <input type="text" name="supplier_name" class="input" required placeholder="Nama PIC">

                <input type="text" name="supplier_address" class="input md:col-span-2" required placeholder="Alamat">

                <input type="text" name="created_by"
                    value="{{ auth()->user()->name_user ?? 'Manual User' }}"
                    class="input md:col-span-2 bg-gray-100" readonly>
            </div>

            <!-- TABLE ITEM -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden min-w-[600px]">
    
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3 text-left w-[40%]">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Item</span>
            
                                <a href="{{ url('/inventory') }}" 
                                   class="text-xs bg-blue-500 text-white px-2 py-1 rounded-md hover:bg-blue-600 transition">
                                    + Add new inventory
                                </a>
                            </div>
                        </th>
            
                        <th class="p-3 text-center w-[10%]">Unit</th>
                        <th class="p-3 text-center w-[20%]">Qty</th>
                        <th class="p-3 text-right w-[25%]">Harga</th>
                        <th class="p-3 text-center w-[5%]"></th>
                    </tr>
                </thead>
            
                <tbody id="table" class="divide-y">
                    <tr class="hover:bg-gray-50 transition">
                        
                        <!-- ITEM -->
                        <td class="p-2">
                            <select name="inventory_id[]" 
                                class="input select2 text-sm bg-gray-50 border border-gray-200 rounded-lg h-9 w-full max-w-xs" 
                                required>
                                
                                <option value="">Pilih Material</option>
            
                                @foreach($inventories as $inv)
                                    <option value="{{ $inv->id_inventory }}">
                                        {{ $inv->name_material }} - {{ $inv->type }}
                                    </option>
                                @endforeach
            
                            </select>
                        </td>
            
                        <!-- UNIT -->
                        <td class="p-2">
                            <select name="unit[]" 
                                class="input text-center text-sm bg-gray-50 border border-gray-200 rounded-lg h-9 w-full">
                                <option value="kg">Kg</option>
                                <option value="ton">Ton</option>
                                <option value="L">Liter (L)</option>
                            </select>
                        </td>
            
                        <!-- QTY -->
                        <td class="p-2">
                            <input type="number" name="qty[]" 
                                class="input text-center text-sm bg-gray-50 border border-gray-200 rounded-lg h-9 w-full"
                                step="1" min="1" placeholder="0">
                        </td>
            
                        <!-- HARGA -->
                        <td class="p-2">
                            <input type="text" name="price[]" 
                                class="input text-right text-sm rupiah bg-gray-50 border border-gray-200 rounded-lg h-9 w-full"
                                placeholder="Rp 0">
                        </td>
            
                        <!-- DELETE -->
                        <td class="p-2 text-center align-middle">
                            <button type="button" onclick="removeRow(this)" 
                                class="w-8 h-8 flex items-center justify-center rounded-full 
                                       hover:bg-red-100 text-red-400 hover:text-red-600 transition">
                                ✕
                            </button>
                        </td>
            
                    </tr>
                </tbody>
            
                </table>

            <div class="flex justify-between mt-4">
                <button type="button" onclick="addRow()" class="text-blue-600">
                    + Tambah Item
                </button>

                <button class="bg-green-600 text-white px-6 py-2 rounded-xl">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- TABLE DATA -->
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden border border-gray-100">
        <div class="flex justify-between items-center p-6">
            <h2 class="text-xl font-bold text-gray-800">Data Procurement</h2>

        </div>
    
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4 font-medium">No PO</th>
                    <th class="px-6 py-4 font-medium">Tanggal</th>
                    <th class="px-6 py-4 font-medium">Created By</th>
                    <th class="px-6 py-4 font-medium text-right">Total</th>
                    <th class="px-6 py-4 font-medium text-center">Action</th>
                </tr>
            </thead>
    
            <tbody class="text-gray-600 text-sm">
                @foreach($po as $p)
                
                <!-- ROW UTAMA -->
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $p->no_po }}</td>
                    <td class="px-6 py-4">{{ $p->tanggal }}</td>
                    <td class="px-6 py-4">{{ $p->created_by }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">
                        Rp {{ number_format($p->total, 0, ',', '.') }}
                    </td>
                
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center space-x-3">
                
                            <!-- VIEW PDF -->
                            <a href="/procurement/pdf/{{ $p->id_po }}" target="_blank"
                                class="text-blue-500 hover:text-blue-600 font-semibold">
                                View
                            </a>

                            <!-- DOWNLOAD PDF -->
                            <a href="/procurement/pdf/{{ $p->id_po }}?download=1"
                                class="text-green-500 hover:text-green-600 font-semibold">
                                Download
                            </a>
                
                            @if(auth()->user()->role === 'superadmin' || auth()->user()->position !== 'direktur_utama')
                            <!-- DELETE -->
                            <form action="/procurement/delete/{{ $p->id_po }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')"
                                    class="text-red-500 hover:text-red-600 font-semibold">
                                    Delete
                                </button>
                            </form>
                            @endif

                             <!-- TOGGLE -->
                             <button onclick="toggleDetail({{ $p->id_po }}, this)"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition duration-200">
                                
                                <span class="text-lg font-bold transition-all duration-300">
                                    +
                                </span>
                            
                            </button>
                
                        </div>
                    </td>
                </tr>
                
                <!-- ROW DETAIL (HIDDEN) -->
                <tr id="detail-{{ $p->id_po }}" class="hidden bg-gray-50">
                    <td colspan="5" class="px-6 py-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border min-w-[400px]">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-2 text-left">Item</th>
                                    <th class="p-2 text-center">Qty</th>
                                    <th class="p-2 text-right">Harga</th>
                                    <th class="p-2 text-right">Total</th>
                                </tr>
                            </thead>
                
                            <tbody>
                                @foreach($p->details as $d)
                                <tr class="border-t">
                                    <td class="p-2">{{ $d->inventory->name_material ?? '-' }}</td>
                                
                                    <td class="p-2 text-center">
                                        @if(strtolower($d->unit) === 'kg' && $d->qty >= 1000)
                                            {{ rtrim(rtrim(number_format($d->qty / 1000, 2, ',', '.'), '0'), ',') }} Ton
                                            <div class="text-xs text-gray-400">
                                                ({{ number_format($d->qty, 0, ',', '.') }} Kg)
                                            </div>
                                        @else
                                            {{ number_format($d->qty, 0, ',', '.') }} {{ ucfirst($d->unit ?? 'Kg') }}
                                        @endif
                                    </td>
                                
                                    <td class="p-2 text-right">
                                        Rp {{ number_format($d->price,0,',','.') }}
                                    </td>
                                
                                    <td class="p-2 text-right">
                                        Rp {{ number_format($d->total,0,',','.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 text-gray-800">
                                <tr>
                                    <td colspan="3" class="p-2 text-right font-bold">Grand Total</td>
                                    <td class="p-2 text-right font-bold text-green-600">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                
                    </td>
                </tr>
                
                @endforeach
                </tbody>
            </table>
        </div>
    
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <div class="flex items-center space-x-2 text-gray-400 text-sm">
                <span>&lt;</span>
                <span class="text-gray-800 font-bold">1</span>
                <span>1-{{ count($po) }} of {{ count($po) }}</span>
                <span>&gt;</span>
            </div>
        </div>
    </div>

<!-- MODAL -->
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    
    <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg p-6 relative">

        <!-- CLOSE -->
        <button onclick="closeModal()" 
            class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl">
            ✕
        </button>

        <h2 class="text-lg font-semibold mb-4">Detail Purchase Order</h2>

        <!-- INFO -->
        <div class="mb-4">
            <p><strong>No PO:</strong> <span id="d_no_po"></span></p>
            <p><strong>Tanggal:</strong> <span id="d_tanggal"></span></p>
            <p><strong>Supplier:</strong> <span id="d_supplier"></span></p>
            <p><strong>Alamat:</strong> <span id="d_address"></span></p>
            <p><strong>Dibuat Oleh:</strong> <span id="d_created"></span></p>
        </div>

        <!-- TABLE ITEM -->
        <div class="overflow-x-auto">
            <table class="w-full border text-sm min-w-[500px]">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Item</th>
                    <th class="p-2">Unit</th>
                    <th class="p-2">Qty</th>
                    <th class="p-2">Harga</th>
                    <th class="p-2">Total</th>
                </tr>
            </thead>
            <tbody id="d_items"></tbody>
            <tfoot class="bg-gray-50 text-gray-800">
                <tr>
                    <td colspan="4" class="p-2 text-right font-bold">Grand Total</td>
                    <td class="p-2 text-right font-bold text-green-600">Rp <span id="d_total"></span></td>
                </tr>
            </tfoot>
        </table>
        </div>

    </div>
</div>

<style>
.input {
    width: 100%;
    border: 1px solid #ddd;
    padding: 8px;
    border-radius: 8px;
}
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
const inventoriesStr = `{!! addslashes(json_encode($inventories)) !!}`;
const inventoriesArray = JSON.parse(inventoriesStr);
let optionsHTML = '<option value="">Pilih Material</option>';
inventoriesArray.forEach(inv => {
    optionsHTML += `<option value="${inv.id_inventory}">${inv.name_material} - ${inv.type}</option>`;
});

$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Cari Material...",
        allowClear: true
    });
});

document.addEventListener('input', function(e){

if(e.target.name === 'qty[]'){
    let value = e.target.value;

    // cegah koma & karakter aneh
    e.target.value = value.replace(/[^0-9]/g, '');
}

});

function addRow(){
    let row = `
<tr class="hover:bg-gray-50 transition">
                        
                        <!-- ITEM -->
                        <td class="p-2">
                            <select name="inventory_id[]" 
                                class="input select2 text-sm bg-gray-50 border border-gray-200 rounded-lg h-9 w-full max-w-xs" 
                                required>
                                
                                <option value="">Pilih Material</option>
            
                                @foreach($inventories as $inv)
                                    <option value="{{ $inv->id_inventory }}">
                                        {{ $inv->name_material }} - {{ $inv->type }}
                                    </option>
                                @endforeach
            
                            </select>
                        </td>
            
                        <!-- UNIT -->
                        <td class="p-2">
                            <select name="unit[]" 
                                class="input text-center text-sm bg-gray-50 border border-gray-200 rounded-lg h-9 w-full">
                                <option value="kg">Kg</option>
                                <option value="ton">Ton</option>
                                <option value="L">Liter (L)</option>
                            </select>
                        </td>
            
                        <!-- QTY -->
                        <td class="p-2">
                            <input type="number" name="qty[]" 
                                class="input text-center text-sm bg-gray-50 border border-gray-200 rounded-lg h-9 w-full"
                                step="1" min="1" placeholder="0">
                        </td>
            
                        <!-- HARGA -->
                        <td class="p-2">
                            <input type="text" name="price[]" 
                                class="input text-right text-sm rupiah bg-gray-50 border border-gray-200 rounded-lg h-9 w-full"
                                placeholder="Rp 0">
                        </td>
            
                        <!-- DELETE -->
                        <td class="p-2 text-center align-middle">
                            <button type="button" onclick="removeRow(this)" 
                                class="w-8 h-8 flex items-center justify-center rounded-full 
                                       hover:bg-red-100 text-red-400 hover:text-red-600 transition">
                                ✕
                            </button>
                        </td>
            
                    </tr>`;

    document.querySelector('#table').insertAdjacentHTML('beforeend', row);

    $('.select2').select2({
        placeholder: "Cari Material...",
        allowClear: true
    });
}

function removeRow(btn){
    btn.closest('tr').remove();
}

// format rupiah
function formatRupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka);
}

function toggleDetail(id){
    let row = document.getElementById('detail-' + id);

    if(row.classList.contains('hidden')){
        row.classList.remove('hidden');
    } else {
        row.classList.add('hidden');
    }
}

function toggleDetail(id, btn){

let row = document.getElementById('detail-'+id);
let icon = btn.querySelector('span');

if(row.classList.contains('hidden')){
    // buka
    row.classList.remove('hidden');

    icon.innerHTML = '−'; // ganti jadi minus
    icon.classList.add('rotate-180');

}else{
    // tutup
    row.classList.add('hidden');

    icon.innerHTML = '+'; // balik ke plus
    icon.classList.remove('rotate-180');
}
}

document.addEventListener('input', function(e){

if(e.target.classList.contains('rupiah')){

    let value = e.target.value.replace(/[^0-9]/g, ''); // hapus selain angka

    if(value){
        e.target.value = new Intl.NumberFormat('id-ID').format(value);
    }else{
        e.target.value = '';
    }
}

});
</script>

@endsection