@extends('main')
@section('title', 'customer_req')
@section('container')

<h1 class="text-2xl font-semibold mb-6 text-gray-800">
    Customer Request
</h1>

<div class="max-w-5xl mx-auto space-y-6">

    <!-- FORM INPUT -->
    <form action="/customer-request/store" method="POST"
        class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        @csrf

        <h2 class="text-sm font-semibold text-gray-600 mb-3">
            Form Pengajuan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input name="customer_name" placeholder="Nama Customer"
                class="input">

            <input name="phone" placeholder="No HP"
                class="input">

            <textarea name="address" placeholder="Alamat"
                class="input md:col-span-2"></textarea>
        </div>

        <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm">
            Ajukan Request
        </button>
    </form>


    <!-- LIST DATA -->
    @foreach($data as $d)
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-3">

            <div>
                <div class="font-semibold text-gray-800">
                    {{ $d->customer_name }}
                </div>
                <div class="text-xs text-gray-400">
                    {{ $d->request_code }}
                </div>
            </div>

            <span class="text-xs px-3 py-1 rounded-full 
                @if($d->status == 'waiting_approval') bg-yellow-100 text-yellow-700
                @elseif($d->status == 'approved') bg-green-100 text-green-700
                @elseif($d->status == 'rejected') bg-red-100 text-red-600
                @elseif($d->status == 'paid') bg-blue-100 text-blue-700
                @else bg-gray-100 text-gray-600
                @endif">
                {{ $d->status }}
            </span>
        </div>

        <!-- INFO -->
        <div class="text-sm text-gray-500 space-y-1 mb-3">
            <div>📞 {{ $d->phone }}</div>
            <div>📍 {{ $d->address }}</div>
        </div>


        <!-- ===================== -->
        <!-- 🔥 APPROVAL SECTION -->
        <!-- ===================== -->

        @if(
            in_array(auth()->user()->position, ['wakil_direktur','direktur_utama']) 
            && $d->status == 'waiting_approval'
        )
        <form action="/customer-request/approve/{{ $d->id }}" method="POST" class="flex gap-2">
            @csrf

            <button name="action" value="approved"
                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                ✔ Approve
            </button>

            <button name="action" value="rejected"
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                ✖ Reject
            </button>
        </form>
        @endif


        <!-- ===================== -->
        <!-- 💰 BAYAR -->
        <!-- ===================== -->

        @if($d->status == 'approved' && auth()->user()->position == 'sales_internal')
        <form action="/customer-request/pay/{{ $d->id }}" method="POST">
            @csrf
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm mt-2">
                Bayar Sekarang
            </button>
        </form>
        @endif


        <!-- ===================== -->
        <!-- 📲 WA CONFIRM -->
        <!-- ===================== -->

        @if($d->is_paid && !$d->is_wa_confirmed)

        <div class="mt-3 space-y-2">

            <a href="https://wa.me/628123456789" target="_blank"
                class="block bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm text-center">
                Chat WhatsApp
            </a>

            <form action="/customer-request/confirm-wa/{{ $d->id }}" method="POST">
                @csrf
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm w-full">
                    ✔ Konfirmasi WA
                </button>
            </form>

        </div>
        @endif


        <!-- ===================== -->
        <!-- 📅 SCHEDULE -->
        <!-- ===================== -->

        @if($d->is_wa_confirmed && !$d->schedule_date)

        <form action="/customer-request/schedule/{{ $d->id }}" method="POST" class="mt-3">
            @csrf

            <div class="flex gap-2">
                <input type="date" name="schedule_date"
                    class="input text-sm">

                <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                    Set Jadwal
                </button>
            </div>
        </form>

        @endif


        <!-- ===================== -->
        <!-- 📄 PDF -->
        <!-- ===================== -->

        @if($d->schedule_date)

        <a href="/customer-request/pdf/{{ $d->id }}"
            class="inline-block mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm">
            Print PDF
        </a>

        @endif

    </div>
    @endforeach

</div>

@endsection