@extends('main')
@section('title', 'setting')
@section('container')

<h1 class="text-2xl font-bold mb-6">Settings</h1>

<div class="p-6">
    
    <!-- GRID 5 KOLOM -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

        <!-- CARD 1 -->
        <div class="bg-white shadow-md rounded-2xl p-4 hover:shadow-xl transition flex flex-col justify-between">

            <!-- HEADER -->
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-blue-500 text-white flex items-center justify-center rounded-full font-bold">
                        B
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Budi Santoso</h3>
                        <p class="text-xs text-gray-500">Supervisor</p>
                    </div>
                </div>

                <!-- DETAIL -->
                <div class="text-xs text-gray-600 space-y-1">
                    <p><span class="font-medium">NIK:</span> 123456789</p>
                    <p><span class="font-medium">Role:</span> Admin</p>
                    <p><span class="font-medium">Branch:</span> Jakarta</p>
                </div>
            </div>

            <!-- BUTTON -->
            <div class="mt-4 flex gap-2">
                <button class="w-full text-xs bg-blue-500 hover:bg-blue-600 text-white py-1 rounded-lg transition">
                    Detail
                </button>
                <button class="w-full text-xs bg-yellow-400 hover:bg-yellow-500 text-white py-1 rounded-lg transition">
                    Edit
                </button>
                <button class="w-full text-xs bg-red-500 hover:bg-red-600 text-white py-1 rounded-lg transition">
                    Delete
                </button>
            </div>

        </div>

        <!-- DUPLIKASI CARD LAIN (SAMA STRUKTUR) -->
    


    </div>

</div>
   
</div>

@endsection
