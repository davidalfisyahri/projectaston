@extends('main')
@section('title', 'inventory')
@section('container')

<h1 class="text-2xl font-bold mb-6">Inventory Management</h1>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- ================= INVENTORY ================= -->
    <div class="bg-white p-5 rounded-xl shadow">
        <div class="flex justify-between mb-4">
            <h2 class="font-semibold">Material</h2>
            <button onclick="openInventoryModal()" 
                class="bg-green-500 text-white px-3 py-1 rounded">
                + Create
            </button>
        </div>

        <div id="inventoryList" class="space-y-2 text-sm"></div>
    </div>

    <!-- ================= GRADE ================= -->
    <div class="bg-white p-5 rounded-xl shadow">
        <div class="flex justify-between mb-4">
            <h2 class="font-semibold">Grade Beton</h2>
            <button onclick="openGradeModal()" 
                class="bg-blue-500 text-white px-3 py-1 rounded">
                + Create
            </button>
        </div>

        <div id="gradeList" class="space-y-3"></div>
    </div>

</div>

<!-- ================= MODAL INVENTORY ================= -->
<div id="inventoryModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-full max-w-md relative">

        <div class="flex justify-end mb-2">
            <button onclick="closeInventoryModal()" class="text-gray-400 text-lg">✕</button>
        </div>

        <h2 class="font-bold mb-3">Tambah Material</h2>

        <input id="inv_name" placeholder="Nama Material" class="input mb-2">
        <select id="inv_type" class="input mb-2">
            <option value="cement">Cement</option>
            <option value="FA">FA</option>
            <option value="Sand">Sand</option>
            <option value="Aggregate">Aggregate</option>
            <option value="Admixture">Admixture</option>
        </select>
        <input id="inv_stock" type="number" placeholder="Stock" class="input mb-4">

        <div class="flex gap-2">
            <button onclick="closeInventoryModal()" 
                class="w-full bg-gray-200 py-2 rounded">
                Exit
            </button>

            <button onclick="saveInventory()" 
                class="w-full bg-green-500 text-white py-2 rounded">
                Simpan
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL GRADE ================= -->
<div id="gradeModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center overflow-auto">
    <div class="bg-white p-6 rounded-xl w-full max-w-xl">

        <div class="flex justify-end mb-2">
            <button onclick="closeGradeModal()" class="text-gray-400 text-lg">✕</button>
        </div>

        <h2 class="font-bold mb-3">Tambah / Edit Grade</h2>

        <input id="grade_name" placeholder="Nama (K-250)" class="input mb-2">
        <input id="grade_fc" placeholder="Mpa" class="input mb-2">
        <input id="harga_fa" placeholder="Harga FA" class="input mb-2">
        <input id="harga_nfa" placeholder="Harga NFA" class="input mb-4">

        <h3 class="font-semibold mb-2">Komposisi Material</h3>

        <div id="compositionList" class="space-y-2"></div>

        <button onclick="addRow()" 
            class="text-sm bg-gray-200 px-2 py-1 rounded mb-3">
            + Tambah Material
        </button>

        <div class="flex gap-2">
            <button onclick="closeGradeModal()" 
                class="w-full bg-gray-200 py-2 rounded">
                Exit
            </button>

            <button onclick="saveGrade()" 
                class="w-full bg-blue-500 text-white py-2 rounded">
                Simpan
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-full max-w-md">

        <div class="flex justify-end mb-2">
            <button onclick="closeDetailModal()" class="text-gray-400 text-lg">✕</button>
        </div>

        <h2 class="font-bold mb-3">Detail Komposisi</h2>
        <div id="detailContent"></div>

        <button onclick="closeDetailModal()" 
            class="w-full mt-4 bg-gray-200 py-2 rounded">
            Exit
        </button>
    </div>
</div>

<style>
.input {
    border: 1px solid #ccc;
    padding: 8px;
    border-radius: 6px;
    width: 100%;
}
</style>

<script>
    let inventories = @json($inventory ?? []);
    let grades = @json($grade ?? []);
    
    /* ================= HELPER ================= */
    function formatRibuan(angka){
        return String(angka)
            .replace(/\D/g, '')
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    function cleanNumber(angka){
        return String(angka).replace(/\./g, '');
    }
    
    /* ================= RESET ================= */
    function resetInventoryForm(){
        inv_name.value = '';
        inv_type.value = 'cement';
        inv_stock.value = '';
    }
    
    function resetGradeForm(){
        grade_name.value = '';
        grade_fc.value = '';
        harga_fa.value = '';
        harga_nfa.value = '';
        compositionList.innerHTML = '';
    }
    
    /* ================= INVENTORY ================= */
    function saveInventory(){
    
        fetch("{{ route('inventory.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                name_material: inv_name.value,
                type: inv_type.value,
                stock: cleanNumber(inv_stock.value)
            })
        })
        .then(res => res.json())
        .then(() => location.reload());
    }
    
    /* DELETE */
    function deleteInventory(id){
        if(!confirm('Yakin hapus?')) return;
    
        fetch(`/inventory/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        }).then(() => location.reload());
    }
    
    /* EDIT */
    function editInventory(data){
        inv_name.value = data.name_material;
        inv_type.value = data.type;
        inv_stock.value = formatRibuan(data.stock);
    
        openInventoryModal();
    
        // ubah save jadi update
        window.saveInventory = function(){
            fetch(`/inventory/${data.id_inventory}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    name_material: inv_name.value,
                    type: inv_type.value,
                    stock: cleanNumber(inv_stock.value)
                })
            }).then(() => location.reload());
        }
    }
    
    /* ================= RENDER INVENTORY ================= */
    function renderInventory(){
        let el = document.getElementById('inventoryList');
        el.innerHTML = '';
    
        inventories.forEach((item) => {
            el.innerHTML += `
            <div class="border p-2 rounded flex justify-between items-center">
                <div>
                    <b>${item.name_material}</b> (${item.type})<br>
                    Stock: ${formatRibuan(item.stock)}
                </div>
    
                <div class="flex gap-2">
                    <button onclick='editInventory(${JSON.stringify(item)})'
                        class="bg-yellow-400 px-2 py-1 rounded text-xs">
                        Edit
                    </button>
    
                    <button onclick="deleteInventory(${item.id_inventory})"
                        class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                        Delete
                    </button>
                </div>
            </div>
            `;
        });
    }
    
    /* ================= GRADE ================= */
    function saveGrade(){
    
        let rows = document.querySelectorAll('#compositionList > div');
    
        if(rows.length === 0){
            alert('Tambahkan minimal 1 komposisi!');
            return;
        }
    
        let compositions = [];
    
        rows.forEach(row => {
            let invId = row.querySelector('select').value;
            let qty = cleanNumber(row.querySelector('input').value);
    
            if(!qty){
                alert('Qty tidak boleh kosong');
                throw "error";
            }
    
            compositions.push({
                inventory_id: invId,
                qty: qty
            });
        });
    
        fetch("{{ route('grade.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                name: grade_name.value,
                fc: grade_fc.value,
                harga_fa: cleanNumber(harga_fa.value),
                harga_nfa: cleanNumber(harga_nfa.value),
                compositions: compositions
            })
        })
        .then(res => res.json())
        .then(() => location.reload())
        .catch(() => alert('Gagal simpan grade'));
    }
    
    /* ================= RENDER GRADE ================= */
    function renderGrade(){
        let el = document.getElementById('gradeList');
        el.innerHTML = '';
    
        grades.forEach((g) => {
    
            let comp = '';
            g.compositions?.forEach(c => {
                comp += `<div>${c.inventory?.name_material} : ${formatRibuan(c.qty)}</div>`;
            });
    
            el.innerHTML += `
            <div class="border p-3 rounded">
                <b>${g.name}</b> (FC: ${g.fc})
    
                <div class="text-xs mt-1">
                    FA: Rp ${formatRibuan(g.harga_fa)} |
                    NFA: Rp ${formatRibuan(g.harga_nfa)}
                </div>
    
                <div class="mt-2 text-xs">${comp}</div>
            </div>
            `;
        });
    }
    
    /* ================= COMPOSITION ================= */
    function addRow(){
        let options = inventories.map(inv =>
            `<option value="${inv.id_inventory}">${inv.name_material}</option>`
        ).join('');
    
        compositionList.innerHTML += `
        <div class="flex gap-2">
            <select class="input">${options}</select>
            <input type="text" placeholder="Qty" class="input qty">
        </div>
        `;
    }
    
    /* ================= FORMAT ================= */
    inv_stock.type = "text"; // 🔥 hilangin panah number
    
    inv_stock.addEventListener('input', function(){
        this.value = formatRibuan(this.value);
    });
    
    harga_fa.addEventListener('input', function(){
        this.value = formatRibuan(this.value);
    });
    
    harga_nfa.addEventListener('input', function(){
        this.value = formatRibuan(this.value);
    });
    
    document.addEventListener('input', function(e){
        if(e.target.classList.contains('qty')){
            e.target.value = formatRibuan(e.target.value);
        }
    });
    
    /* ================= MODAL ================= */
    function openInventoryModal(){
        resetInventoryForm();
        inventoryModal.classList.remove('hidden');
        inventoryModal.classList.add('flex');
    }
    
    function closeInventoryModal(){
        inventoryModal.classList.add('hidden');
    }
    
    function openGradeModal(){
        resetGradeForm();
        gradeModal.classList.remove('hidden');
        gradeModal.classList.add('flex');
    }
    
    function closeGradeModal(){
        gradeModal.classList.add('hidden');
    }
    
    /* ================= INIT ================= */
    document.addEventListener('DOMContentLoaded', () => {
        renderInventory();
        renderGrade();
    });
    </script>

@endsection