@extends('main')
@section('title', 'customer_req')
@section('container')

    <div class="max-w-6xl mx-auto">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                Customer Request
            </h1>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <form action="/customer-request" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, no hp, dll..." 
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 w-full sm:w-64">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm border border-gray-300 font-medium transition">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="/customer-request" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm border border-red-200 font-medium transition">
                            Clear
                        </a>
                    @endif
                </form>

                <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 transition text-white px-4 py-2 rounded-lg text-sm shadow w-full sm:w-auto whitespace-nowrap">
                    + Buat Request
                </button>
            </div>
        </div>

        <!-- ========================= -->
        <!-- TABLE -->
        <!-- ========================= -->
        <div class="bg-white rounded-xl shadow border overflow-hidden">

            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Customer</th>
                        <th class="p-3 text-center">Phone</th>
                        <th class="p-3 text-center">Region</th>
                        <th class="p-3 text-center">Dibuat Oleh</th>
                        <th class="p-3 text-center">Tanggal</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($data as $d)

                        <!-- MAIN ROW -->
                        <tr class="border-t hover:bg-gray-50">

                            <td class="p-3 text-xs text-gray-500">
                                {{ $d->request_code }}
                            </td>

                            <td class="p-3 font-medium">
                                {{ $d->customer_name }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $d->phone }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $d->region }}
                            </td>

                            <td class="p-3 text-center text-gray-600">
                                {{ $d->user->name_user ?? '-' }}
                            </td>

                            <td class="p-3 text-center text-gray-500">
                                {{ date('d-m-Y', strtotime($d->tanggal)) }}
                            </td>

                            <td class="p-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full
                                                                            @if($d->status == 'waiting_approval') bg-yellow-100 text-yellow-700
                                                                            @elseif($d->status == 'approved') bg-green-100 text-green-700
                                                                            @elseif($d->status == 'rejected') bg-red-100 text-red-600
                                                                            @else bg-gray-100 text-gray-600
                                                                            @endif">
                                    {{ $d->status }}
                                </span>
                            </td>

                            <td class="p-3 text-center flex justify-center gap-2">

                                <!-- VIEW DETAIL -->
                                <button type="button" onclick="openDetail({{ $d->id }})"
                                    class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-xs hover:bg-blue-200">
                                    View
                                </button>

                                <!-- DOWNLOAD PDF -->
                                <a href="/customer-request/pdf/{{ $d->id }}?download=1"
                                    class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs hover:bg-green-200">
                                    Download
                                </a>

                                <!-- DELETE -->
                                <form action="/customer-request/delete/{{ $d->id }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-500 hover:text-red-700">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>

                        <!-- DETAIL ROW -->
                        <tr id="detail-{{ $d->id }}" class="hidden bg-gray-50">
                            <td colspan="9" class="p-4">

                                <div class="border rounded-lg overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-200 text-gray-600">
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
                                                    <td class="p-2">
                                                        {{ $item->grade->name_grade }}
                                                    </td>

                                                    <td class="p-2 text-center uppercase">
                                                        {{ $item->type }}
                                                    </td>

                                                    <td class="p-2 text-center">
                                                        {{ number_format($item->qty, 2, ',', '.') }}
                                                    </td>

                                                    <td class="p-2 text-right">
                                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                                    </td>

                                                    <td class="p-2 text-right font-semibold text-green-600">
                                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>

                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
            <div class="p-4">
                {{ $data->links() }}
            </div>

        </div>
    </div>

    <!-- ========================= -->
    <!-- DETAIL MODALS -->
    <!-- ========================= -->
    @foreach($data as $d)
        <div id="detailModal-{{ $d->id }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
            <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg overflow-hidden max-h-[85vh] flex flex-col">

                <!-- HEADER -->
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Detail Customer Request</h2>
                </div>

                <!-- BODY -->
                <div class="p-6 overflow-y-auto space-y-4 text-sm">

                    <!-- IDENTITAS -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Identitas Customer</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Kode Request</td><td class="py-1 font-medium">{{ $d->request_code }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Nama Customer</td><td class="py-1">{{ $d->customer_name }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Phone</td><td class="py-1">{{ $d->phone ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Region</td><td class="py-1">{{ $d->region ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Customer Number</td><td class="py-1">{{ $d->customer_number ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Pengiriman</td><td class="py-1">{{ $d->address ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Note</td><td class="py-1">{{ $d->note ?? '-' }}</td></tr>
                    </table>

                    <!-- PROFIL BISNIS -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Profil Bisnis</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">No Identitas (NIK)</td><td class="py-1">{{ $d->no_identitas ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Bentuk Usaha</td><td class="py-1">{{ $d->form_business ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Kepemilikan</td><td class="py-1">{{ $d->business_ownership ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Bidang Usaha</td><td class="py-1">{{ $d->section_business ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Usaha</td><td class="py-1">{{ $d->address_business ?? '-' }}</td></tr>
                    </table>

                    <!-- PAJAK -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Pajak</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">NPWP</td><td class="py-1">{{ $d->npwp ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Nama Pajak</td><td class="py-1">{{ $d->tax_name ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Pajak</td><td class="py-1">{{ $d->tax_address ?? '-' }}</td></tr>
                    </table>

                    <!-- IZIN -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Perizinan</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">TDP</td><td class="py-1">{{ $d->izin_tdp ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Berlaku TDP</td><td class="py-1">{{ $d->tdp_date ? date('d-m-Y', strtotime($d->tdp_date)) : '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">SIUP</td><td class="py-1">{{ $d->izin_siup ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Berlaku SIUP</td><td class="py-1">{{ $d->siup_date ? date('d-m-Y', strtotime($d->siup_date)) : '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">SIO</td><td class="py-1">{{ $d->izin_sio ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Berlaku SIO</td><td class="py-1">{{ $d->sio_date ? date('d-m-Y', strtotime($d->sio_date)) : '-' }}</td></tr>
                    </table>

                    <!-- OWNER -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Owner</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Nama Pemilik</td><td class="py-1">{{ $d->owner_name ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Alamat Pemilik</td><td class="py-1">{{ $d->owner_address ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Email</td><td class="py-1">{{ $d->email ?? '-' }}</td></tr>
                    </table>

                    <!-- PROJECT -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Project</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Alamat Kantor Induk</td><td class="py-1">{{ $d->office_address ?? '-' }}</td></tr>
                        <tr><td class="py-1 text-gray-500">Ongoing Project</td><td class="py-1">{{ $d->ongoing_project ?? '-' }}</td></tr>
                    </table>

                    <!-- JADWAL -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Jadwal</h3>
                    <table class="w-full">
                        <tr><td class="py-1 text-gray-500 w-44">Jadwal Pengiriman</td><td class="py-1">{{ $d->schedule_date ? date('d-m-Y', strtotime($d->schedule_date)) : '-' }}</td></tr>
                    </table>

                    <!-- DETAIL ORDER -->
                    <h3 class="font-semibold text-gray-700 border-b pb-1">Detail Order</h3>
                    @if($d->details->count())
                        <div class="border rounded-lg overflow-hidden">
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
                                            <td class="p-2 text-right font-semibold text-green-600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
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
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-center">
                    <button type="button" onclick="closeDetail({{ $d->id }})"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endforeach

    <!-- ========================= -->
    <!-- MODAL -->
    <!-- ========================= -->
    <div id="modalForm" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-6xl rounded-xl shadow-lg overflow-hidden max-h-[90vh] flex flex-col">

            <!-- HEADER -->
            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    Form Customer Request
                </h2>

                <!-- BUTTON X -->
                <button onclick="closeModal()" class="text-gray-500 hover:text-red-500 text-xl font-bold">
                    ✕
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 overflow-y-auto">

                <form action="/customer-request/store" method="POST">
                    @csrf

                    <!-- ===================== -->
                    <!-- IDENTITAS -->
                    <!-- ===================== -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input name="customer_name" placeholder="Nama Customer" class="border p-2 rounded" required>
                        <input name="phone" placeholder="No HP" class="border p-2 rounded">

                        <input name="region" placeholder="Region" class="border p-2 rounded">
                        <input name="customer_number" placeholder="Customer Number" class="border p-2 rounded">

                        <textarea name="note" placeholder="Note" class="col-span-2 border p-2 rounded"></textarea>
                    </div>

                    <!-- ===================== -->
                    <!-- PROFILE BISNIS -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Profil Bisnis</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input name="no_identitas" placeholder="NIK" class="border p-2 rounded">
                        <input name="form_business" placeholder="Bentuk Usaha" class="border p-2 rounded">
                        <select name="business_ownership" class="border p-2 rounded w-full">
                            <option value="">-- Pilih Kepemilikan --</option>
                            <option value="milik_sendiri">Milik Sendiri</option>
                            <option value="tidak_ada_cabang">Tidak Ada Cabang</option>
                            <option value="sewa_kontrak">Sewa / Kontrak</option>
                            <option value="kantor_pusat">Kantor Pusat / Induk</option>
                            <option value="cabang">Cabang</option>
                            <option value="proyek">Proyek</option>
                        </select>

                        <input name="section_business" placeholder="Bidang Usaha" class="border p-2 rounded">
                        <textarea name="address_business" placeholder="Alamat Usaha" class="border p-2 rounded"></textarea>

                        <input name="npwp" placeholder="NPWP" class="border p-2 rounded">
                        <input name="tax_name" placeholder="Nama Pajak" class="border p-2 rounded">
                        <textarea name="address" placeholder="Alamat pengiriman"
                            class="col-span-2 border p-2 rounded"></textarea>
                        <textarea name="tax_address" placeholder="Alamat Pajak"
                            class="col-span-2 border p-2 rounded"></textarea>
                    </div>

                    <!-- ===================== -->
                    <!-- PROJECT -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Project</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <textarea name="office_address" placeholder="Jika kantor cabang, Alamat Kantor induk"
                            class="border p-2 rounded"></textarea>

                        <div>
                            <label class="text-xs text-gray-600">Project yang sedang berjalan</label>

                            <select name="ongoing_project" id="ongoing_project" class="border p-2 rounded w-full">

                                <option value="">-- Pilih Project (Opsional) --</option>

                                @foreach($projects as $p)
                                    <option value="{{ $p->ongoing_project }}">
                                        {{ $p->ongoing_project }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <!-- ===================== -->
                    <!-- IZIN -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Perizinan</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">

                        <!-- TDP -->
                        <div>
                            <label class="text-xs text-gray-600">TDP</label>
                            <input name="izin_tdp" class="border p-2 rounded w-full">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Tanggal Berlaku TDP</label>
                            <input type="date" name="tdp_date" class="border p-2 rounded w-full">
                        </div>

                        <!-- SIUP -->
                        <div>
                            <label class="text-xs text-gray-600">SIUP</label>
                            <input name="izin_siup" class="border p-2 rounded w-full">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Tanggal Berlaku SIUP</label>
                            <input type="date" name="siup_date" class="border p-2 rounded w-full">
                        </div>

                        <!-- SIO -->
                        <div>
                            <label class="text-xs text-gray-600">SIO</label>
                            <input name="izin_sio" class="border p-2 rounded w-full">
                        </div>

                        <div>
                            <label class="text-xs text-gray-600">Tanggal Berlaku SIO</label>
                            <input type="date" name="sio_date" class="border p-2 rounded w-full">
                        </div>

                    </div>

                    <!-- ===================== -->
                    <!-- OWNER -->
                    <!-- ===================== -->
                    <h3 class="font-semibold mb-2">Owner</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input name="owner_name" placeholder="Nama Pemilik" class="border p-2 rounded">

                        <input name="email" type="email" placeholder="Email pemilik" class="border p-2 rounded">

                        <textarea name="owner_address" placeholder="Alamat pemilik"
                            class="col-span-2 border p-2 rounded"></textarea>
                    </div>

                    <h3 class="mb-3 font-semibold text-gray-800 border-b pb-2">
                        Jadwal
                    </h3>
                    <div>
                        <label class="text-xs text-gray-600">Jadwal Pengiriman</label>
                        <input type="date" name="schedule_date" class="border p-2 rounded w-full">
                    </div>
                    <br>
                    <!--=====================-->
                    <!-- DETAIL -->
                    <!-- ===================== -->
                    <h3 class="mb-3 font-semibold text-gray-800 border-b pb-2">
                        Detail Order
                    </h3>


                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border rounded-lg overflow-hidden">

                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="p-2 text-left">Grade</th>
                                    <th class="p-2 text-center">Type</th>
                                    <th class="p-2 text-center">Qty</th>
                                    <th class="p-2 text-right">Harga</th>
                                    <th class="p-2 text-right">Total</th>
                                    <th class="p-2 text-center w-10">#</th>
                                </tr>
                            </thead>

                            <tbody id="detailTable">
                                <tr class="border-t">
                                    <td class="p-2">
                                        <select name="grade_id[]" class="border rounded px-2 py-1 w-full gradeSelect">
                                            @foreach($grades as $g)
                                                <option value="{{ $g->id_grade }}" data-fa="{{ $g->harga_fa }}"
                                                    data-nfa="{{ $g->harga_nfa }}">
                                                    {{ $g->name_grade }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="p-2">
                                        <select name="type[]" class="border rounded px-2 py-1 w-full typeSelect">
                                            <option value="fa">FA</option>
                                            <option value="nfa">NFA</option>
                                        </select>
                                    </td>

                                    <td class="p-2">
                                        <input type="number" name="qty[]" class="border rounded px-2 py-1 w-full qtyInput">
                                    </td>

                                    <td class="p-2">
                                        <input type="text" name="price[]"
                                            class="border rounded px-2 py-1 w-full priceInput bg-gray-50" readonly>
                                    </td>

                                    <td class="p-2">
                                        <input type="text" class="border rounded px-2 py-1 w-full totalInput bg-gray-100"
                                            readonly>
                                    </td>

                                    <td class="p-2 text-center">
                                        <button type="button" class="btn-remove text-red-500 text-lg">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" onclick="addRow()" class="mt-3 text-blue-600 text-sm">
                        + Tambah Item
                    </button>

                    <!-- FOOTER -->
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">
                            Batal
                        </button>

                        <button class="bg-blue-600 text-white px-4 py-2 rounded">
                            Submit
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // =====================
            // TAMBAH ROW
            // =====================
            window.addRow = function () {
                let table = document.getElementById('detailTable')
                let row = table.querySelector('tr').cloneNode(true)

                // reset input value
                row.querySelectorAll('input').forEach(input => input.value = '')

                table.appendChild(row)
            }

            $(document).ready(function () {
                $('#ongoing_project').select2({
                    placeholder: "Cari atau pilih project...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modalForm') // 🔥 WAJIB karena modal
                });
            });
            // =====================
            // HAPUS ROW (AMAN)
            // =====================
            document.getElementById('detailTable').addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-remove')) {
                    let rows = document.querySelectorAll('#detailTable tr')

                    if (rows.length > 1) {
                        e.target.closest('tr').remove()
                    }
                }
            })

            function closeModal() {
                document.getElementById('modalForm').classList.add('hidden')
            }

            function openModal() {
                document.getElementById('modalForm').classList.remove('hidden')
                document.getElementById('modalForm').classList.add('flex')
            }

            // klik background = close
            document.getElementById('modalForm').addEventListener('click', function (e) {
                if (e.target.id === 'modalForm') {
                    closeModal()
                }
            })

            // =====================
            // AUTO HITUNG
            // =====================
            document.getElementById('detailTable').addEventListener('input', function (e) {
                let row = e.target.closest('tr')
                if (!row) return

                let grade = row.querySelector('.gradeSelect')
                let type = row.querySelector('.typeSelect')
                let qty = row.querySelector('.qtyInput')
                let price = row.querySelector('.priceInput')
                let total = row.querySelector('.totalInput')

                let selected = grade.options[grade.selectedIndex]

                let harga = type.value === 'fa'
                    ? selected.dataset.fa
                    : selected.dataset.nfa

                price.value = harga || 0

                let totalVal = (qty.value || 0) * (harga || 0)
                total.value = totalVal
            })

            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num)
            }

            document.addEventListener('input', function (e) {
                let row = e.target.closest('tr')
                if (!row) return

                let grade = row.querySelector('.gradeSelect')
                let type = row.querySelector('.typeSelect')
                let qty = row.querySelector('.qtyInput')
                let price = row.querySelector('.priceInput')
                let total = row.querySelector('.totalInput')

                if (!grade || !type) return

                let selected = grade.options[grade.selectedIndex]
                let harga = type.value === 'fa'
                    ? selected.dataset.fa
                    : selected.dataset.nfa

                // 👉 ubah ke number dulu
                harga = parseFloat(harga) || 0
                let qtyVal = parseFloat(qty.value) || 0

                // 👉 tampilkan dengan format ribuan
                price.value = formatNumber(harga)

                let totalVal = qtyVal * harga
                total.value = formatNumber(totalVal)
            })

        })

        function openModal() {
            modalForm.classList.remove('hidden')
            modalForm.classList.add('flex')
        }

        function closeModal() {
            modalForm.classList.add('hidden')
        }

        function openDetail(id) {
            document.getElementById('detailModal-' + id).classList.remove('hidden')
            document.getElementById('detailModal-' + id).classList.add('flex')
        }

        function closeDetail(id) {
            document.getElementById('detailModal-' + id).classList.add('hidden')
            document.getElementById('detailModal-' + id).classList.remove('flex')
        }

        function addRow() {
            let row = document.querySelector('#detailTable tr').cloneNode(true)
            document.getElementById('detailTable').appendChild(row)
        }

        document.addEventListener('input', function (e) {
            let row = e.target.closest('tr')
            if (!row) return

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