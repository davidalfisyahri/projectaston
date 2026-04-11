@extends('main')
@section('title', 'setting')
@section('container')
@if(auth()->user()->role === 'superadmin')
<h1 class="text-2xl font-bold mb-6">Settings</h1>

<div class="p-6">
    <div class="mb-3 flex justify">
        <button onclick="openAddModal()" 
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl text-sm shadow">
            + Tambah User
        </button>
    </div>
    <!-- GRID UTAMA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

        @forelse ($users as $user)
        <div class="bg-white shadow-md rounded-2xl p-5 
                    hover:shadow-xl transition 
                    flex flex-col justify-between 
                    min-h-[250px]">

            <!-- HEADER -->
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-blue-500 text-white flex items-center justify-center rounded-full font-bold text-lg">
                        {{ strtoupper(substr($user->name_user, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">{{ $user->name_user }}</h3>
                        <p class="text-xs text-gray-500">{{ $user->position }}</p>
                    </div>
                </div>

                <!-- DETAIL -->
                <div class="text-xs text-gray-600 space-y-2 mt-2">
                    <p><span class="font-medium">NIK:</span> {{ $user->nik }}</p>
                    <p><span class="font-medium">Role:</span> {{ $user->role }}</p>
                    <p><span class="font-medium">Branch:</span> {{ $user->office_branch }}</p>
                </div>
            </div>

            <!-- BUTTON -->
            @php
                $loginUser = auth()->user();
            @endphp

        <div class="mt-auto pt-4 flex gap-2">
            <button 
                onclick="openModal({{ $user->id_user }})"
                class="w-full text-xs bg-blue-500 text-white py-1.5 rounded-lg">
                Detail
            </button>

            <button class="w-full text-xs bg-yellow-400 text-white py-1.5 rounded-lg">
                Edit
            </button>

            @if ($loginUser && $loginUser->id_user != $user->id_user)
                <form action="/users/{{ $user->id_user }}" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button 
                    onclick="return confirm('Yakin ingin menghapus akun ini?')"
                    class="w-full flex items-center justify-center gap-1 text-xs 
                        bg-red-500 hover:bg-red-600 
                        text-white py-1.5 rounded-lg transition">
                                        Delete
                    </button>
                </form>
            @endif
        </div>

        </div>
        @empty
            <p class="col-span-4 text-center text-gray-500">Data user belum ada</p>
        @endforelse

    </div>

    <!-- PAGINATION -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>

</div>

{{-- Modal add user --}}
<div id="addUserModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-xl relative">

        <!-- CLOSE -->
        <button onclick="closeAddModal()" 
            class="absolute top-3 right-3 text-gray-400 hover:text-black text-lg">
            ✕
        </button>

        <h2 class="text-lg font-semibold mb-4">Tambah User</h2>

        <form action="/users" method="POST" class="space-y-3">
            @csrf

            <input type="text" name="name_user" placeholder="Nama"
                class="w-full border rounded-lg px-3 py-2 text-sm" required>

            <input type="text" name="username" placeholder="Username"
                class="w-full border rounded-lg px-3 py-2 text-sm" required>

            <input type="text" name="nik" placeholder="NIK"
                class="w-full border rounded-lg px-3 py-2 text-sm">

            <input type="email" name="email" placeholder="Email (optional)"
                class="w-full border rounded-lg px-3 py-2 text-sm">

            <input type="text" name="office_branch" placeholder="Branch"
                class="w-full border rounded-lg px-3 py-2 text-sm">

            <!-- ROLE -->
            <select name="role" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <option value="">Pilih Role</option>
                <option value="superadmin">Superadmin</option>
                <option value="admin">Admin</option>
                <option value="sales">Sales</option>
            </select>

            <!-- POSITION -->
            <select name="position" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <option value="">Pilih Position</option>
                <option value="sales_internal">Sales Internal</option>
                <option value="sales_external">Sales External</option>
                <option value="wakil_direktur">Wakil Direktur</option>
                <option value="direktur_utama">Direktur Utama</option>
                <option value="hrga">HRGA</option>
                <option value="logistik">Logistik</option>
                <option value="finance">Finance</option>
            </select>

            <input type="password" name="password" placeholder="Password"
                class="w-full border rounded-lg px-3 py-2 text-sm" required>

            <!-- BUTTON -->
            <div class="flex gap-2 pt-3">
                <button type="button" onclick="closeAddModal()"
                    class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg text-sm">
                    Batal
                </button>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm">
                    Simpan
                </button>
            </div>

        </form>

    </div>

</div>

{{-- MODAL DETAIL USER --}}
<div id="userModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-sm p-6 text-center shadow-2xl relative">

        <!-- AVATAR -->
        <div id="avatar"
            class="w-20 h-20 mx-auto mb-4 bg-blue-500 text-white flex items-center justify-center rounded-full text-2xl font-bold shadow-lg">
            {{ strtoupper(substr($user->name_user, 0, 1)) }}
        </div>

        <!-- NAME -->
        <h2 id="d_name" class="text-lg font-semibold text-gray-800"></h2>
        <p id="d_position" class="text-xs text-gray-500 mb-4"></p>

        <!-- INFO BOX -->
        <div class="bg-gray-50 rounded-xl p-4 text-sm text-left space-y-3">

            <div class="flex justify-between">
                <span class="text-gray-500">Username</span>
                <span id="d_username" class="font-medium text-gray-800"></span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Email</span>
                <span id="d_email" class="font-medium text-gray-800"></span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">NIK</span>
                <span id="d_nik" class="font-medium text-gray-800"></span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Role</span>
                <span id="d_role" class="font-semibold text-blue-600"></span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Branch</span>
                <span id="d_branch" class="font-medium text-gray-800"></span>
            </div>

        </div>

        <!-- BUTTON -->
        <button onclick="closeModal()" 
            class="mt-5 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl text-sm transition">
            Tutup
        </button>

    </div>

</div>

<script>
    const users = @json($users->items());

    function openModal(id) {
        const user = users.find(u => u.id_user == id);

        document.getElementById('d_name').innerText = user.name_user;
        document.getElementById('d_username').innerText = user.username;
        document.getElementById('d_email').innerText = user.email ?? '-';
        document.getElementById('d_nik').innerText = user.nik;
        document.getElementById('d_role').innerText = user.role;
        document.getElementById('d_position').innerText = user.position;
        document.getElementById('d_branch').innerText = user.office_branch ?? '-';

        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function openAddModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.getElementById('addUserModal').classList.add('flex');
}

function closeAddModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}
</script>
@endif
@endsection