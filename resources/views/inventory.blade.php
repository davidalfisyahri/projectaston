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

        <h2 class="font-bold mb-3">Material</h2>

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
    let inventories = @json($inventory);
    let grades = @json($grade);
    
    let editInventoryId = null;
    function saveGrade(){

    let rows = document.querySelectorAll('#compositionList > div');

    if(rows.length === 0){
        alert('Tambahkan minimal 1 material!');
        return;
    }

    let compositions = [];

    rows.forEach(row => {
        let invId = row.querySelector('select').value;
        let qty = clean(row.querySelector('input').value);

        if(!qty){
            alert('Qty tidak boleh kosong');
            return;
        }

        compositions.push({
            inventory_id: invId,
            qty: qty
        });
    });

    fetch("/grade", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            name_grade: name_grade.value,
            mpa: mpa.value,
            harga_fa: clean(harga_fa.value),
            harga_nfa: clean(harga_nfa.value),
            compositions: compositions
        })
    })
    .then(res => {
        if(!res.ok) throw "error";
        return res.json();
    })
    .then(() => {
        alert('Berhasil simpan grade');
        location.reload();
    })
    .catch(() => {
        alert('Gagal simpan grade');
    });
}
    
    /* ================= FORMAT ================= */
    function formatRibuan(x){
        return String(x).replace(/\D/g,'')
        .replace(/\B(?=(\d{3})+(?!\d))/g,".");
    }
    function clean(x){
        return String(x).replace(/\./g,'');
    }
    
    /* ================= INVENTORY ================= */
    function resetInventoryForm(){
        editInventoryId = null;
        inv_name.value = '';
        inv_type.value = 'cement';
        inv_stock.value = '';
    }
    
    function openInventoryModal(isEdit = false){
        if(!isEdit){
            resetInventoryForm(); // hanya reset kalau ADD
        }
        inventoryModal.classList.remove('hidden');
        inventoryModal.classList.add('flex');
    }
    
    function closeInventoryModal(){
        inventoryModal.classList.add('hidden');
    }
    
    function saveInventory(){
    
        let url = editInventoryId ? `/inventory/${editInventoryId}` : `/inventory`;
        let method = editInventoryId ? "PUT" : "POST";
    
        fetch(url,{
            method:method,
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },
            body:JSON.stringify({
                name_material:inv_name.value,
                type:inv_type.value,
                stock:clean(inv_stock.value)
            })
        })
        .then(res=>res.json())
        .then(()=>location.reload());
    }
    
    function editInventory(id){
    
        let data = inventories.find(i=>i.id_inventory==id);
    
        editInventoryId = id;
    
        inv_name.value = data.name_material;
        inv_type.value = data.type;
        inv_stock.value = formatRibuan(data.stock);
    
        openInventoryModal(true); // 🔥 penting
    }
    
    function deleteInventory(id){
        if(!confirm('Yakin hapus?')) return;
    
        fetch(`/inventory/${id}`,{
            method:"DELETE",
            headers:{
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            }
        }).then(()=>location.reload());
    }
    
    function renderInventory(){
        let el = document.getElementById('inventoryList');
        el.innerHTML='';
    
        inventories.forEach(i=>{
            el.innerHTML+=`
            <div class="border p-2 rounded flex justify-between items-center">
                <div>
                    <b>${i.name_material}</b> (${i.type})<br>
                    Stock: ${formatRibuan(i.stock)}
                </div>
    
                <div class="flex gap-2">
                    <button onclick="editInventory(${i.id_inventory})"
                        class="bg-yellow-400 px-2 py-1 text-xs rounded">
                        Edit
                    </button>
    
                    <button onclick="deleteInventory(${i.id_inventory})"
                        class="bg-red-500 text-white px-2 py-1 text-xs rounded">
                        Delete
                    </button>
                </div>
            </div>`;
        });
    }
    

/* ================= GRADE ================= */
function openGradeModal(){
    grade_name.value = '';
    grade_fc.value = '';
    harga_fa.value = '';
    harga_nfa.value = '';
    compositionList.innerHTML = '';

    gradeModal.classList.remove('hidden');
    gradeModal.classList.add('flex');
}

function closeGradeModal(){
    gradeModal.classList.add('hidden');
}

/* ADD ROW + BUTTON X */
    function addRow(){

    let options = inventories.map(i =>
        `<option value="${i.id_inventory}">${i.name_material}</option>`
    ).join('');

    let div = document.createElement('div');
    div.className = "flex gap-2 items-center";

    div.innerHTML = `
        <select class="input">${options}</select>
        <input type="text" placeholder="Qty" class="input qty">
        <button type="button" class="bg-red-500 text-white px-2 py-1 rounded text-xs">✕</button>
    `;

    div.querySelector('button').onclick = () => div.remove();

    document.getElementById('compositionList').appendChild(div);
    }

/* SAVE GRADE */
    function saveGrade(){

    let rows = document.querySelectorAll('#compositionList > div');

    if(rows.length === 0){
        alert('Tambahkan minimal 1 material!');
        return;
    }

    let composition = [];
    let valid = true;

    rows.forEach(r=>{
        let invId = r.querySelector('select').value;
        let qty = clean(r.querySelector('input').value);

        if(!qty){
            valid = false;
        }

        composition.push({
            inventory_id: invId,
            qty: qty
        });
    });

    if(!valid){
        alert('Qty tidak boleh kosong!');
        return;
    }

    if(!grade_name.value || !grade_fc.value){
        alert('Nama Grade dan MPA wajib diisi!');
        return;
    }

    fetch("/grade",{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body:JSON.stringify({
            name_grade: grade_name.value,  
            mpa: grade_fc.value,            
            harga_fa: clean(harga_fa.value),
            harga_nfa: clean(harga_nfa.value),
            composition: composition
        })
    })
    .then(res=>{
        if(!res.ok) throw "error";
        return res.json();
    })
    .then(()=>{
        alert('Grade berhasil disimpan');
        closeGradeModal();
        location.reload();
    })
    .catch(()=>{
        alert('Gagal simpan grade (cek controller)');
    });
    }

    /* ================= FORMAT INPUT ================= */
    inv_stock.type = "text";
    
    inv_stock.addEventListener('input', e=>{
        e.target.value = formatRibuan(e.target.value);
    });
    
    harga_fa.addEventListener('input', e=>{
        e.target.value = formatRibuan(e.target.value);
    });
    
    harga_nfa.addEventListener('input', e=>{
        e.target.value = formatRibuan(e.target.value);
    });
    
    document.addEventListener('input', e=>{
        if(e.target.classList.contains('qty')){
            e.target.value = formatRibuan(e.target.value);
        }
    });
    
    /* ================= INIT ================= */
    document.addEventListener('DOMContentLoaded',()=>{
        renderInventory();
    });
    </script>

@endsection