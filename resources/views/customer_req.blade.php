@extends('main')
@section('title', 'customer_req')
@section('container')

<h1 class="text-2xl font-bold mb-6">Form Customer Order</h1>

<!-- FORM -->
<div class="bg-white p-6 rounded-xl shadow mb-6 border">

    <form id="orderForm" class="space-y-6">

        <!-- SECTION: DATA CUSTOMER -->
        <div>
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                Data Customer
            </h2>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="label">Nama Customer *</label>
                    <input type="text" name="customer_name" class="input" required>
                </div>

                <div>
                    <label class="label">Nomor Customer</label>
                    <input type="text" name="customer_number" class="input">
                </div>

                <div>
                    <label class="label">Region</label>
                    <input type="text" name="region" class="input">
                </div>

                <div>
                    <label class="label">Qty Grade</label>
                    <input type="number" name="qty_grade" class="input">
                </div>

            </div>
        </div>

        <!-- SECTION: KONTAK -->
        <div>
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                Kontak
            </h2>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="label">No HP</label>
                    <input type="text" name="phone_number" class="input">
                </div>

                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" class="input">
                </div>

            </div>
        </div>

        <!-- SECTION: OWNER -->
        <div>
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                Data Pemilik
            </h2>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="label">Nama Owner</label>
                    <input type="text" name="owner_name" class="input">
                </div>

                <div>
                    <label class="label">Alamat Owner</label>
                    <input type="text" name="owner_address" class="input">
                </div>

            </div>
        </div>

        <!-- SECTION: BISNIS -->
        <div>
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                Data Bisnis
            </h2>

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="label">Kepemilikan Bisnis</label>
                    <select name="business_ownership" class="input">
                        <option value="">-- Pilih --</option>
                        <option value="one_own">Milik Sendiri</option>
                        <option value="rent">Sewa</option>
                        <option value="branch">Cabang</option>
                        <option value="no_branch">Non Cabang</option>
                        <option value="main_headoffice">Kantor Pusat</option>
                        <option value="project">Project</option>
                    </select>
                </div>

                <div>
                    <label class="label">Alamat Kantor</label>
                    <input type="text" name="office_address" class="input">
                </div>

            </div>
        </div>

        <!-- SECTION: STATUS -->
        <div>
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                Status Order
            </h2>

            <select name="status" class="input w-full">
                <option value="pending">Pending</option>
                <option value="in_progress">Sedang Diproses</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>

        <!-- NOTE -->
        <div>
            <label class="label">Catatan</label>
            <textarea name="note" class="input w-full"></textarea>
        </div>

        <!-- BUTTON -->
        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold text-lg">
            SIMPAN DATA
        </button>

    </form>
</div>

<!-- CARD CONTAINER -->
<div id="cardContainer" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>

<!-- MODAL DETAIL -->
<div id="modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Detail Customer</h2>
        <div id="modalContent" class="text-sm space-y-2"></div>

        <button onclick="closeModal()" 
            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
            Tutup
        </button>
    </div>
</div>

<style>
.input {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    width: 100%;
}

.label {
    font-size: 13px;
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
    color: #444;
}
</style>

<script>
let dataList = [];

document.getElementById('orderForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);
    let data = {};

    formData.forEach((value, key) => {
        data[key] = value;
    });

    dataList.push(data);
    renderCard();

    this.reset();
});

function renderCard(){
    let container = document.getElementById('cardContainer');
    container.innerHTML = '';

    dataList.forEach((item, index) => {

        let statusColor = {
            pending: 'bg-yellow-500',
            in_progress: 'bg-blue-500',
            approved: 'bg-green-600',
            rejected: 'bg-red-600'
        };

        container.innerHTML += `
        <div class="bg-white rounded-xl shadow p-4 border">

            <!-- STATUS -->
            <div class="text-white text-xs px-3 py-1 rounded mb-3 font-semibold ${statusColor[item.status]}">
                ${item.status.toUpperCase()}
            </div>

            <h3 class="font-bold text-base">${item.customer_name}</h3>
            <p class="text-xs text-gray-500">${item.customer_number || '-'}</p>

            <div class="text-sm mt-2 space-y-1">
                <p><b>Region:</b> ${item.region || '-'}</p>
                <p><b>Phone:</b> ${item.phone_number || '-'}</p>
            </div>

            <button onclick="showDetail(${index})"
                class="mt-3 w-full bg-blue-500 hover:bg-blue-600 text-white text-sm py-2 rounded">
                Lihat Detail
            </button>
        </div>
        `;
    });
}

function showDetail(index){
    let data = dataList[index];
    let content = '';

    for (let key in data){
        content += `<p><b>${key}</b>: ${data[key]}</p>`;
    }

    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modal').classList.add('flex');
}

function closeModal(){
    document.getElementById('modal').classList.add('hidden');
}
</script>

@endsection