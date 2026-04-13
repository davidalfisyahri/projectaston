@extends('main')
@section('title','Inventory')

@section('container')

<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- ================= INVENTORY ================= --}}
        <div class="bg-white shadow-lg rounded-2xl p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Inventory Material</h2>
                <button onclick="openModal('addInventory')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Add Material
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($inventory as $inv)
                <div class="border rounded-xl p-4 shadow-sm">

                    <h3 class="font-bold text-lg">{{ $inv->name_material }}</h3>
                    <p class="text-sm text-gray-500">Type: {{ $inv->type }}</p>

                    <p class="mt-2">
                        Stock: 
                        <span class="font-semibold {{ $inv->stock == 0 ? 'text-red-500' : 'text-green-600' }}">
                            {{ number_format($inv->stock, 0, ',', '.') }}
                        </span>
                    </p>

                    <div class="flex gap-2 mt-3">
                        <button onclick="openModal('editInventory{{ $inv->id_inventory }}')" 
                            class="bg-yellow-400 px-3 py-1 rounded text-sm">
                            Edit
                        </button>

                        <a href="/inventory/delete/{{ $inv->id_inventory }}"
                            onclick="return confirmDelete()"
                            class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                            Delete
                         </a>
                    </div>

                </div>

                {{-- MODAL EDIT INVENTORY --}}
                <div id="editInventory{{ $inv->id_inventory }}" class="modal hidden">
                    <div class="modal-box">
                        <h3 class="font-bold mb-3">Edit Material</h3>

                        <form action="/inventory/update/{{ $inv->id_inventory }}" method="POST">
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
        </div>

        {{-- ================= GRADE ================= --}}
        <div class="bg-white shadow-lg rounded-2xl p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Grade Beton</h2>
                <button onclick="openModal('addGrade')" 
                    class="bg-green-600 text-white px-4 py-2 rounded-lg">
                    + Add Grade
                </button>
            </div>

            <div class="space-y-4">
                @foreach($grade as $g)
                <div class="border rounded-xl p-4 shadow-sm">

                    <h3 class="font-bold text-lg">{{ $g->name_grade }}</h3>
                    <p class="text-sm text-gray-500">MPA: {{ $g->mpa }}</p>

                    <div class="flex gap-2 mt-3">
                        <button onclick="openModal('detail{{ $g->id_grade }}')" 
                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                            Detail
                        </button>

                        <button onclick="openModal('editGrade{{ $g->id_grade }}')" 
                            class="bg-yellow-400 px-3 py-1 rounded text-sm">
                            Edit
                        </button>

                        <a href="/grade/delete/{{ $g->id_grade }}"
                            onclick="return confirmDelete()"
                            class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                            Delete
                         </a>
                    </div>

                </div>

                {{-- MODAL DETAIL --}}
                <div id="detail{{ $g->id_grade }}" class="modal hidden">
                    <div class="modal-box w-[500px]">
                
                        <h3 class="text-lg font-bold mb-4">Detail Grade Beton</h3>
                
                        <!-- INFO GRADE -->
                        <div class="mb-4 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama Grade</span>
                                <span class="font-semibold">{{ $g->name_grade }}</span>
                            </div>
                
                            <div class="flex justify-between">
                                <span class="text-gray-600">MPA</span>
                                <span class="font-semibold">{{ $g->mpa }}</span>
                            </div>
                
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga FA</span>
                                <span class="font-semibold">
                                    Rp {{ number_format($g->harga_fa, 0, ',', '.') }}
                                </span>
                            </div>
                
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga NFA</span>
                                <span class="font-semibold">
                                    Rp {{ number_format($g->harga_nfa, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                
                        <!-- COMPOSITION -->
                        <div>
                            <h4 class="font-semibold mb-2">Composition Material</h4>
                
                            <div class="border rounded-lg">
                                @foreach($g->composition as $c)
                                <div class="flex justify-between border-b px-3 py-2 text-sm">
                                    <span>{{ $c->inventory->name_material }}</span>
                                    <span>{{ number_format($c->qty, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                
                        <!-- ACTION -->
                        <div class="mt-5 text-right">
                            <button onclick="closeModal('detail{{ $g->id_grade }}')" class="btn-cancel">
                                Close
                            </button>
                        </div>
                
                    </div>
                </div>

                @endforeach
            </div>
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

            <input type="number" name="stock" placeholder="Stock" class="input mt-2">

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addInventory')" class="btn-cancel">Cancel</button>
                <button class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL ADD GRADE ================= --}}
<div id="addGrade" class="modal hidden">
    <div class="modal-box w-[500px]">

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
                            <select name="inventory_id[]" class="input" required>
                                <option value="">-- pilih material --</option>
                                @foreach($inventory as $inv)
                                <option value="{{ $inv->id_inventory }}">
                                    {{ $inv->name_material }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="p-2">
                            <input type="number" step="0.01" name="qty[]" class="input" required>
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
                @foreach($inventory as $inv)
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
                @foreach($inventory as $inv)
                <option value="{{ $inv->id_inventory }}">
                    {{ $inv->name_material }}
                </option>
                @endforeach
            </select>
        </td>

        <td class="p-2">
            <input type="number" step="0.01" name="qty[]" class="input" required>
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

</script>

@endsection