@extends('main')
@section('title', 'procurement')
@section('container')
<h1 class="text-2xl font-bold mb-6">Procurement</h1>


    <!-- FORM -->
    <div class="bg-white rounded-2xl shadow p-6">
        <form action="/procurement/store" method="POST">
            @csrf

            <!-- HEADER FORM -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">

                <input type="text" name="no_po" class="input" placeholder="No PO">

                <input type="date" name="tanggal" class="input">

                <input type="text" name="name_pt" class="input" placeholder="Nama PT Supplier">

                <input type="text" name="supplier_name" class="input" placeholder="Nama PIC">

                <input type="text" name="supplier_address" class="input md:col-span-2" placeholder="Alamat">

                <input type="text" name="created_by"
                    value="{{ auth()->user()->name_user ?? 'Manual User' }}"
                    class="input md:col-span-2 bg-gray-100" readonly>
            </div>

            <!-- TABLE ITEM -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Item</th>
                        <th class="p-2">Unit</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Harga</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody id="table">
                    <tr>
                        <td>
                            <select name="inventory_id[]" class="input" required>
                                <option value="">Pilih Material</option>
                                @foreach($inventories as $inv)
                                    <option value="{{ $inv->id_inventory }}">{{ $inv->name_material }} - {{ $inv->type }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="unit[]" class="input text-center"></td>
                        <td><input type="number" name="qty[]" class="input text-center"></td>
                        <td><input type="text" name="price[]" class="input text-right"></td>
                        <td><button type="button" onclick="removeRow(this)">✕</button></td>
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

    <!-- TABLE DATA -->
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden border border-gray-100">
        <div class="flex justify-between items-center p-6">
            <h2 class="text-xl font-bold text-gray-800">Data PO</h2>

        </div>
    
        <table class="w-full text-left border-collapse">
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
                
                            <!-- PRINT -->
                            <a href="/procurement/pdf/{{ $p->id_po }}" 
                                class="text-amber-500 hover:text-amber-600 font-semibold">
                                Print
                            </a>
                
                            <!-- DELETE -->
                            <form action="/procurement/delete/{{ $p->id_po }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')"
                                    class="text-red-500 hover:text-red-600 font-semibold">
                                    Delete
                                </button>
                            </form>

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
                
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-2 text-left">Item</th>
                                    <th class="p-2 text-center">Unit</th>
                                    <th class="p-2 text-center">Qty</th>
                                    <th class="p-2 text-right">Harga</th>
                                    <th class="p-2 text-right">Total</th>
                                </tr>
                            </thead>
                
                            <tbody>
                                @foreach($p->details as $d)
                                <tr class="border-t">
                                    <td class="p-2">{{ $d->inventory->name_material ?? '-' }}</td>
                                    <td class="p-2 text-center">{{ $d->unit }}</td>
                                    <td class="p-2 text-center">{{ $d->qty }}</td>
                                    <td class="p-2 text-right">
                                        Rp {{ number_format($d->price,0,',','.') }}
                                    </td>
                                    <td class="p-2 text-right">
                                        Rp {{ number_format($d->total,0,',','.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                        <!-- GRAND TOTAL -->
                        <div class="text-right mt-3 font-bold text-green-600">
                            Grand Total: Rp {{ number_format($p->total,0,',','.') }}
                        </div>
                
                    </td>
                </tr>
                
                @endforeach
                </tbody>
        </table>
    
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
        <table class="w-full border text-sm">
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
        </table>

        <div class="text-right mt-4">
            <strong>Total: Rp <span id="d_total"></span></strong>
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

<script>
const inventoriesStr = `{!! addslashes(json_encode($inventories)) !!}`;
const inventoriesArray = JSON.parse(inventoriesStr);
let optionsHTML = '<option value="">Pilih Material</option>';
inventoriesArray.forEach(inv => {
    optionsHTML += `<option value="${inv.id_inventory}">${inv.name_material} - ${inv.type}</option>`;
});

function addRow(){
    let row = `
    <tr>
        <td>
            <select name="inventory_id[]" class="input" required>
                ${optionsHTML}
            </select>
        </td>
        <td><input type="text" name="unit[]" class="input text-center"></td>
        <td><input type="number" name="qty[]" class="input text-center"></td>
        <td><input type="text" name="price[]" class="input text-right"></td>
        <td><button type="button" onclick="removeRow(this)">✕</button></td>
    </tr>`;
    document.querySelector('#table').insertAdjacentHTML('beforeend', row);
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
</script>

@endsection