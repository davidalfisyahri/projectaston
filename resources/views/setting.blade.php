@extends('main')
@section('title', 'setting')
@section('container')
@if(auth()->user()->role === 'superadmin')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
        <p class="text-sm text-gray-500">Kelola semua pengguna platform</p>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
    <div id="successAlert" class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl text-sm shadow-sm animate-fade-in">
        <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('successAlert').remove()" class="ml-auto text-green-400 hover:text-green-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif

    {{-- ERROR ALERT --}}
    @if($errors->any())
    <div id="errorAlert" class="mb-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm shadow-sm animate-fade-in">
        <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <span class="font-semibold">Gagal menyimpan data:</span>
            <ul class="mt-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="document.getElementById('errorAlert').remove()" class="ml-auto text-red-400 hover:text-red-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    @endif

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 mt-4">
        <!-- Total Pengguna -->
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Pengguna</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $users->total() }}</p>
        </div>
        
        <!-- Pengguna Aktif -->
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Pengguna Aktif</h3>
            <p class="text-2xl font-bold text-green-600">{{ $users->total() }}</p>
        </div>
        
        <!-- Admin -->
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Admin</h3>
            <p class="text-2xl font-bold text-blue-600">
                @php
                    $adminCount = collect($users->items())->filter(fn($u) => in_array(strtolower($u->role), ['admin', 'superadmin']))->count();
                @endphp
                {{ $adminCount }}
            </p>
        </div>
    </div>

    <!-- FILTER & ADD BAR -->
    <form method="GET" action="/setting" class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex-1 w-full relative">
            <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengguna..." class="pl-10 w-full md:w-96 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="role" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white">
                <option value="">Semua Role</option>
                <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="sales" {{ request('role') == 'sales' ? 'selected' : '' }}>Sales</option>
            </select>
            <button type="submit" class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-50 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter
            </button>
            <button type="button" onclick="openAddModal()" class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-700 transition whitespace-nowrap shadow-sm">
                + Tambah User
            </button>
        </div>
    </form>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
            <h2 class="text-xl font-bold text-gray-800">Daftar Pengguna</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-white text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nama</th>
                        <th class="px-6 py-4 font-medium">Jabatan</th>
                        <th class="px-6 py-4 font-medium">NIK</th>
                        <th class="px-6 py-4 font-medium text-center">Role</th>
                        <th class="px-6 py-4 font-medium">Branch</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $user->name_user }}</td>
                        <td class="px-6 py-4">{{ $user->position }}</td>
                        <td class="px-6 py-4">{{ $user->nik }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide 
                                {{ in_array(strtolower($user->role), ['admin', 'superadmin']) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $user->office_branch }}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            @php $loginUser = auth()->user(); @endphp
                            
                            <button onclick="openeditModal({{ $user->id_user }})" class="text-gray-500 hover:text-yellow-600 transition" title="Edit">
                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>

                            <button onclick="openModal({{ $user->id_user }})" class="text-gray-500 hover:text-blue-600 transition" title="Detail">
                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            @if ($loginUser && $loginUser->id_user != $user->id_user)
                            <form action="/users/{{ $user->id_user }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Delete">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                @if(request('search') || request('role'))
                                    <p class="text-gray-500 font-medium">Data tidak ditemukan</p>
                                    <p class="text-gray-400 text-xs">
                                        Tidak ada pengguna yang cocok dengan
                                        @if(request('search'))pencarian "<span class="font-semibold">{{ request('search') }}</span>"@endif
                                        @if(request('search') && request('role')) dan @endif
                                        @if(request('role'))filter role "<span class="font-semibold">{{ ucfirst(request('role')) }}</span>"@endif
                                    </p>
                                    <a href="/setting" class="mt-2 text-blue-500 hover:text-blue-700 text-xs font-medium transition">← Reset Filter</a>
                                @else
                                    <p class="text-gray-500">Data user belum ada</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-end px-6 py-4 border-t border-gray-100 bg-white text-sm text-gray-600">
            <div class="flex items-center gap-4">
                <a href="{{ $users->previousPageUrl() ?? '#' }}" 
                   class="{{ $users->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <span class="font-medium text-gray-800">{{ $users->currentPage() }}</span>
                <span>{{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</span>
                <a href="{{ $users->nextPageUrl() ?? '#' }}" 
                   class="{{ !$users->hasMorePages() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:text-gray-900 transition' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Modal add user --}}
<div id="addUserModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative">
        <!-- CLOSE -->
        <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah User</h2>

        <form action="/users" method="POST" class="space-y-4">
            @csrf

            <div>
                <input type="text" name="name_user" placeholder="Nama" value="{{ old('name_user') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
            </div>

            <div>
                <input type="text" name="username" placeholder="Username" value="{{ old('username') }}"
                    class="w-full border {{ $errors->has('username') ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300' }} rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
                @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input type="password" name="password" placeholder="Password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
            </div>

            <div>
                <input type="text" name="nik" placeholder="NIK" value="{{ old('nik') }}"
                    class="w-full border {{ $errors->has('nik') ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300' }} rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                @error('nik')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input type="email" name="email" placeholder="Email (optional)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
            </div>

            <div>
                <input type="text" name="office_branch" placeholder="Branch"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
            </div>

            <!-- ROLE -->
            <div>
                <select name="role" id="roleSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white" required>
                    <option value="" disabled selected class="text-gray-400">Pilih Role</option>
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Admin</option>
                    <option value="sales">Sales</option>
                </select>
            </div>

            <!-- POSITION -->
            <div>
                <select name="position" id="positionSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white" required>
                    <option value="" disabled selected>Pilih Position</option>
                    <option value="sales_internal">Sales Internal</option>
                    <option value="sales_external">Sales External</option>
                    <option value="wakil_direktur">Wakil Direktur</option>
                    <option value="direktur_utama">Direktur Utama</option>
                    <option value="hrga">HRGA</option>
                    <option value="logistik">Logistik</option>
                    <option value="finance">Finance</option>
                </select>
            </div>

            <!-- BUTTON -->
            <div class="flex gap-4 pt-4 mt-2">
                <button type="button" onclick="closeAddModal()"
                    class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 rounded-lg text-sm transition">
                    Batal
                </button>

                <button type="submit"
                    class="w-1/2 bg-[#2b6cb0] hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg text-sm transition shadow-sm" style="background-color: #3b82f6;">
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
            <span id="d_avatar"></span>
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

{{-- MODAL EDIT USER --}}
<div id="editUserModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative">
        <!-- CLOSE -->
        <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h2 class="text-xl font-bold text-gray-800 mb-6">Edit User</h2>

        <form id="editUserForm" method="POST" class="space-y-4">
            @csrf

            <div>
                <input type="text" id="edit_name_user" name="name_user" placeholder="Nama"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
            </div>

            <div>
                <input type="text" id="edit_username" name="username" placeholder="Username"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
            </div>

            <div>
                <input type="text" id="edit_nik" name="nik" placeholder="NIK"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
            </div>

            <div>
                <input type="email" id="edit_email" name="email" placeholder="Email (optional)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
            </div>

            <div>
                <input type="text" id="edit_office_branch" name="office_branch" placeholder="Branch"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
            </div>

            <!-- ROLE -->
            <div>
                <select id="edit_role" name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white" required>
                    <option value="" disabled>Pilih Role</option>
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Admin</option>
                    <option value="sales">Sales</option>
                </select>
            </div>

            <!-- POSITION -->
            <div>
                <select id="edit_position" name="position" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white" required>
                    <option value="" disabled>Pilih Position</option>
                    <option value="sales_internal">Sales Internal</option>
                    <option value="sales_external">Sales External</option>
                    <option value="wakil_direktur">Wakil Direktur</option>
                    <option value="direktur_utama">Direktur Utama</option>
                    <option value="hrga">HRGA</option>
                    <option value="logistik">Logistik</option>
                    <option value="finance">Finance</option>
                </select>
            </div>

            <!-- PASSWORD (OPSIONAL) -->
            <div class="border-t border-gray-200 pt-4 mt-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Password (Opsional)</label>
                <input type="password" id="edit_password" name="password" placeholder="Masukkan password baru"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                <p class="text-gray-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Kosongkan jika tidak ingin mengubah password
                </p>
            </div>

            <!-- BUTTON -->
            <div class="flex gap-4 pt-4 mt-2">
                <button type="button" onclick="closeEditModal()"
                    class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 rounded-lg text-sm transition">
                    Batal
                </button>

                <button type="submit"
                    class="w-1/2 bg-[#2b6cb0] hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg text-sm transition shadow-sm" style="background-color: #3b82f6;">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const users = @json($users->items());

    function openModal(id) {
        const user = users.find(u => u.id_user == id);

        document.getElementById('d_name').innerText = user.name_user;
        document.getElementById('d_avatar').innerText = user.name_user.charAt(0).toUpperCase();
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

function openeditModal(id) {
    const user = users.find(u => u.id_user == id);
    if (!user) return;

    // Set form action
    document.getElementById('editUserForm').action = '/user/update/' + id;

    // Fill fields
    document.getElementById('edit_name_user').value = user.name_user ?? '';
    document.getElementById('edit_username').value = user.username ?? '';
    document.getElementById('edit_nik').value = user.nik ?? '';
    document.getElementById('edit_email').value = user.email ?? '';
    document.getElementById('edit_office_branch').value = user.office_branch ?? '';
    document.getElementById('edit_role').value = user.role ?? '';
    document.getElementById('edit_position').value = user.position ?? '';
    document.getElementById('edit_password').value = '';

    // Show modal
    document.getElementById('editUserModal').classList.remove('hidden');
    document.getElementById('editUserModal').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

const roleSelect = document.getElementById('roleSelect');
const positionSelect = document.getElementById('positionSelect');

// semua option disimpan dulu
const allOptions = Array.from(positionSelect.options);

roleSelect.addEventListener('change', function(){

    let role = this.value;

    // reset dulu
    positionSelect.innerHTML = '<option value="">Pilih Position</option>';

    if(role === 'sales'){
        // hanya sales_internal & sales_external
        allOptions.forEach(opt => {
            if(opt.value === 'sales_internal' || opt.value === 'sales_external'){
                positionSelect.appendChild(opt.cloneNode(true));
            }
        });
    } else {
        // tampilkan semua
        allOptions.forEach(opt => {
            if(opt.value !== ""){
                positionSelect.appendChild(opt.cloneNode(true));
            }
        });
    }

});

// Auto-open modal jika ada validation error
@if($errors->any())
    openAddModal();
@endif

// Auto-hide success alert after 4 seconds
const successAlert = document.getElementById('successAlert');
if (successAlert) {
    setTimeout(() => {
        successAlert.style.transition = 'opacity 0.5s';
        successAlert.style.opacity = '0';
        setTimeout(() => successAlert.remove(), 500);
    }, 4000);
}
</script>
@endif
@endsection