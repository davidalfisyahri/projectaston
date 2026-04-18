@extends('main')
@section('title', 'customer_req')
@section('container')

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Customer Request
        </h1>

        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm shadow">
            + Buat Request
        </button>
    </div>

    <!-- ========================= -->
    <!-- TABLE -->
    <!-- ========================= -->
    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">Kode</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Region</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                <tr class="border-t">
                    <td class="p-3">{{ $d->request_code }}</td>
                    <td class="p-3">{{ $d->customer_name }}</td>
                    <td class="p-3">{{ $d->phone }}</td>
                    <td class="p-3">{{ $d->region }}</td>
                    <td class="p-3">{{ $d->status }}</td>
                    <td class="p-3">{{ $d->tanggal }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ========================= -->
<!-- MODAL -->
<!-- ========================= -->
<div id="modalForm"
    class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

<div class="bg-white w-full max-w-6xl rounded-xl p-6 overflow-y-auto max-h-[90vh]">

<form action="/customer-request/store" method="POST">
@csrf

<h2 class="text-lg font-semibold mb-4">Form Customer Request</h2>

<!-- ===================== -->
<!-- IDENTITAS -->
<!-- ===================== -->
<div class="grid grid-cols-2 gap-3 mb-4">

    <input name="customer_name" placeholder="Nama Customer" class="border p-2 rounded" required>
    <input name="phone" placeholder="No HP" class="border p-2 rounded">

    <input name="region" placeholder="Region" class="border p-2 rounded">
    <input name="customer_number" placeholder="Customer Number" class="border p-2 rounded">

    <textarea name="address" placeholder="Alamat" class="col-span-2 border p-2 rounded"></textarea>

    <textarea name="note" placeholder="Note" class="col-span-2 border p-2 rounded"></textarea>
</div>

<!-- ===================== -->
<!-- PROFILE BISNIS -->
<!-- ===================== -->
<h3 class="font-semibold mb-2">Profil Bisnis</h3>

<div class="grid grid-cols-2 gap-3 mb-4">

    <input name="no_identitas" placeholder="No Identitas" class="border p-2 rounded">
    <input name="form_business" placeholder="Bentuk Usaha" class="border p-2 rounded">

    <input name="section_business" placeholder="Bidang Usaha" class="border p-2 rounded">
    <textarea name="address_business" placeholder="Alamat Usaha" class="border p-2 rounded"></textarea>

    <input name="npwp" placeholder="NPWP" class="border p-2 rounded">
    <input name="tax_name" placeholder="Nama Pajak" class="border p-2 rounded">

    <textarea name="tax_address" placeholder="Alamat Pajak" class="col-span-2 border p-2 rounded"></textarea>
</div>

<!-- ===================== -->
<!-- IZIN -->
<!-- ===================== -->
<h3 class="font-semibold mb-2">Perizinan</h3>

<div class="grid grid-cols-3 gap-3 mb-4">

    <input name="izin_tdp" placeholder="TDP" class="border p-2 rounded">
    <input type="date" name="tdp_date" class="border p-2 rounded">

    <input name="izin_siup" placeholder="SIUP" class="border p-2 rounded">
    <input type="date" name="siup_date" class="border p-2 rounded">

    <input name="izin_sio" placeholder="SIO" class="border p-2 rounded">
    <input type="date" name="sio_date" class="border p-2 rounded">
</div>

<!-- ===================== -->
<!-- OWNER -->
<!-- ===================== -->
<h3 class="font-semibold mb-2">Owner</h3>

<div class="grid grid-cols-2 gap-3 mb-4">
    <input name="owner_name" placeholder="Nama Owner" class="border p-2 rounded">
    <textarea name="owner_address" placeholder="Alamat Owner" class="border p-2 rounded"></textarea>

    <input name="email" placeholder="Email" class="border p-2 rounded">
    <input name="business_ownership" placeholder="Kepemilikan Usaha" class="border p-2 rounded">
</div>

<!-- ===================== -->
<!-- PROJECT -->
<!-- ===================== -->
<div class="mb-4">
    <textarea name="office_address" placeholder="Alamat Kantor" class="w-full border p-2 rounded mb-2"></textarea>
    <textarea name="ongoing_project" placeholder="Proyek Berjalan" class="w-full border p-2 rounded"></textarea>
</div>

<!-- ===================== -->
<!-- DETAIL -->
<!-- ===================== -->
<h3 class="mb-2 font-semibold">Detail Order</h3>

<table class="w-full border text-sm">
<thead>
<tr>
    <th>Grade</th>
    <th>Type</th>
    <th>Qty</th>
    <th>Harga</th>
    <th>Total</th>
</tr>
</thead>

<tbody id="detailTable">
<tr>
<td>
<select name="grade_id[]" class="border p-1 gradeSelect">
@foreach($grades as $g)
<option value="{{ $g->id_grade }}"
data-fa="{{ $g->harga_fa }}"
data-nfa="{{ $g->harga_nfa }}">
{{ $g->name_grade }}
</option>
@endforeach
</select>
</td>

<td>
<select name="type[]" class="border p-1 typeSelect">
<option value="fa">FA</option>
<option value="nfa">NFA</option>
</select>
</td>

<td><input type="number" name="qty[]" class="border p-1 qtyInput"></td>
<td><input type="text" name="price[]" class="border p-1 priceInput" readonly></td>
<td><input type="text" class="border p-1 totalInput" readonly></td>
</tr>
</tbody>
</table>

<button type="button" onclick="addRow()" class="mt-2 text-blue-600">
+ Tambah Item
</button>

<div class="mt-4 text-right">
<button class="bg-blue-600 text-white px-4 py-2 rounded">
Submit
</button>
</div>

</form>
</div>
</div>

<script>
function openModal(){
    modalForm.classList.remove('hidden')
    modalForm.classList.add('flex')
}

function closeModal(){
    modalForm.classList.add('hidden')
}

function addRow(){
    let row = document.querySelector('#detailTable tr').cloneNode(true)
    document.getElementById('detailTable').appendChild(row)
}

document.addEventListener('input', function(e){
    let row = e.target.closest('tr')
    if(!row) return

    let grade = row.querySelector('.gradeSelect')
    let type = row.querySelector('.typeSelect')
    let qty = row.querySelector('.qtyInput')
    let price = row.querySelector('.priceInput')
    let total = row.querySelector('.totalInput')

    let selected = grade.options[grade.selectedIndex]
    let harga = type.value === 'fa' ? selected.dataset.fa : selected.dataset.nfa

    price.value = harga || 0
    total.value = (qty.value || 0) * (harga || 0)
})
</script>

@endsection