@extends('main')
@section('title', 'Approval')
@section('container')

<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">Approval Management</h1>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- TAB NAVIGATION --}}
    <div class="flex gap-2 mb-6">
        <button onclick="switchTab('cr')" id="tab-cr"
            class="tab-btn px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 bg-red-900 text-white shadow-md">
            Customer Request
            <span class="ml-1 bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">{{ count($customerRequests) }}</span>
        </button>
        <button onclick="switchTab('po')" id="tab-po"
            class="tab-btn px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 bg-gray-200 text-gray-600 hover:bg-gray-300">
            Procurement
            <span class="ml-1 bg-gray-300 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ count($procurements) }}</span>
        </button>
    </div>

    {{-- ============================================= --}}
    {{-- TAB 1: CUSTOMER REQUEST --}}
    {{-- ============================================= --}}
    <div id="panel-cr" class="tab-panel">

        @if($customerRequests->isEmpty())
        <div class="bg-white rounded-2xl shadow p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 text-lg">Tidak ada Customer Request yang menunggu approval.</p>
        </div>
        @else

        <div class="space-y-4">
            @foreach($customerRequests as $cr)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">

                {{-- HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 cursor-pointer"
                     onclick="toggleCrDetail({{ $cr->id }})">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $cr->request_code }}</h3>
                            <p class="text-sm text-gray-400">{{ $cr->customer_name }} &bull; {{ $cr->tanggal }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- STATUS BADGE --}}
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            Waiting Approval
                        </span>

                        {{-- TOGGLE ICON --}}
                        <span id="cr-icon-{{ $cr->id }}" class="text-gray-400 transition-transform duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- DETAIL (HIDDEN) --}}
                <div id="cr-detail-{{ $cr->id }}" class="hidden">
                    <div class="px-6 py-4 bg-gray-50/50">

                        {{-- INFO GRID --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                            <div>
                                <span class="text-gray-400">Customer</span>
                                <p class="font-medium text-gray-700">{{ $cr->customer_name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Phone</span>
                                <p class="font-medium text-gray-700">{{ $cr->phone ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Region</span>
                                <p class="font-medium text-gray-700">{{ $cr->region ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Project</span>
                                <p class="font-medium text-gray-700">{{ $cr->ongoing_project ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Dibuat Oleh</span>
                                <p class="font-medium text-gray-700">{{ $cr->user->name_user ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Alamat</span>
                                <p class="font-medium text-gray-700">{{ $cr->address ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- DETAILS TABLE --}}
                        @if($cr->details->count())
                        <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-4">
                            <thead class="bg-gray-100 text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Grade</th>
                                    <th class="px-4 py-2 text-center">Type</th>
                                    <th class="px-4 py-2 text-center">Qty</th>
                                    <th class="px-4 py-2 text-right">Price</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($cr->details as $d)
                                <tr>
                                    <td class="px-4 py-2">{{ $d->grade->name_grade ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $d->type }}</td>
                                    <td class="px-4 py-2 text-center">{{ $d->qty }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($d->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($d->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        {{-- APPROVAL STATUS --}}
                        @if($cr->approvals->count())
                        <div class="mb-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status Approval</h4>
                            <div class="flex gap-3">
                                @foreach($cr->approvals as $appr)
                                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200">
                                    @if($appr->status == 'approved')
                                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                                    @elseif($appr->status == 'rejected')
                                        <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                                    @else
                                        <span class="w-2.5 h-2.5 bg-yellow-400 rounded-full"></span>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $appr->role)) }}</span>
                                    <span class="text-xs text-gray-400">({{ $appr->status }})</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- ACTION BUTTONS --}}
                        @php
                            $myApproval = $cr->approvals->where('role', auth()->user()->position)->first();
                        @endphp

                        @if($myApproval && $myApproval->status == 'pending')
                        <div class="flex gap-3 pt-2">
                            <form action="{{ route('approval.customer_request', $cr->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="approved">
                                <button type="submit"
                                    onclick="return confirm('Approve Customer Request ini?')"
                                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('approval.customer_request', $cr->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="rejected">
                                <button type="submit"
                                    onclick="return confirm('Reject Customer Request ini?')"
                                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </form>
                        </div>
                        @elseif($myApproval)
                        <div class="pt-2">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                                {{ $myApproval->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                @if($myApproval->status == 'approved')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Anda sudah meng-approve
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Anda sudah me-reject
                                @endif
                            </span>
                        </div>
                        @endif

                    </div>
                </div>

            </div>
            @endforeach
        </div>

        @endif
    </div>

    {{-- ============================================= --}}
    {{-- TAB 2: PROCUREMENT --}}
    {{-- ============================================= --}}
    <div id="panel-po" class="tab-panel hidden">

        @if($procurements->isEmpty())
        <div class="bg-white rounded-2xl shadow p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 text-lg">Tidak ada Procurement yang menunggu approval.</p>
        </div>
        @else

        <div class="space-y-4">
            @foreach($procurements as $po)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">

                {{-- HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 cursor-pointer"
                     onclick="togglePoDetail({{ $po->id_po }})">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z"/>
                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zm6.854 7.354-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $po->no_po }}</h3>
                            <p class="text-sm text-gray-400">{{ $po->supplier->name_pt ?? '-' }} &bull; {{ $po->tanggal }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="font-bold text-green-600">Rp {{ number_format($po->total, 0, ',', '.') }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            Pending
                        </span>
                        <span id="po-icon-{{ $po->id_po }}" class="text-gray-400 transition-transform duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- DETAIL --}}
                <div id="po-detail-{{ $po->id_po }}" class="hidden">
                    <div class="px-6 py-4 bg-gray-50/50">

                        {{-- INFO --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                            <div>
                                <span class="text-gray-400">No PO</span>
                                <p class="font-medium text-gray-700">{{ $po->no_po }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Tanggal</span>
                                <p class="font-medium text-gray-700">{{ $po->tanggal }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Supplier</span>
                                <p class="font-medium text-gray-700">{{ $po->supplier->name_pt ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Dibuat Oleh</span>
                                <p class="font-medium text-gray-700">{{ $po->created_by }}</p>
                            </div>
                        </div>

                        {{-- ITEMS TABLE --}}
                        @if($po->details->count())
                        <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-4">
                            <thead class="bg-gray-100 text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Item</th>
                                    <th class="px-4 py-2 text-center">Qty</th>
                                    <th class="px-4 py-2 text-right">Harga</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($po->details as $d)
                                <tr>
                                    <td class="px-4 py-2">{{ $d->inventory->name_material ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        @if($d->qty >= 1000)
                                            {{ rtrim(rtrim(number_format($d->qty / 1000, 2, ',', '.'), '0'), ',') }} ton
                                            <div class="text-xs text-gray-400">({{ number_format($d->qty, 0, ',', '.') }} kg)</div>
                                        @else
                                            {{ number_format($d->qty, 0, ',', '.') }} kg
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($d->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($d->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        {{-- GRAND TOTAL --}}
                        <div class="text-right mb-4 font-bold text-green-600 text-lg">
                            Grand Total: Rp {{ number_format($po->total, 0, ',', '.') }}
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex gap-3 pt-2">
                            <form action="{{ route('approval.procurement', $po->id_po) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="approved">
                                <button type="submit"
                                    onclick="return confirm('Approve Procurement ini?')"
                                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('approval.procurement', $po->id_po) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="rejected">
                                <button type="submit"
                                    onclick="return confirm('Reject Procurement ini?')"
                                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
            @endforeach
        </div>

        @endif
    </div>

</div>

<script>
// TAB SWITCHING
function switchTab(tab) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    // Reset all tab buttons
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-red-900', 'text-white', 'shadow-md');
        b.classList.add('bg-gray-200', 'text-gray-600');
        // Reset badge
        const badge = b.querySelector('span');
        if (badge) {
            badge.classList.remove('bg-white/20', 'text-white');
            badge.classList.add('bg-gray-300', 'text-gray-600');
        }
    });

    // Show selected panel
    document.getElementById('panel-' + tab).classList.remove('hidden');

    // Activate selected tab
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.remove('bg-gray-200', 'text-gray-600');
    activeBtn.classList.add('bg-red-900', 'text-white', 'shadow-md');
    const activeBadge = activeBtn.querySelector('span');
    if (activeBadge) {
        activeBadge.classList.remove('bg-gray-300', 'text-gray-600');
        activeBadge.classList.add('bg-white/20', 'text-white');
    }
}

// TOGGLE DETAIL — Customer Request
function toggleCrDetail(id) {
    const detail = document.getElementById('cr-detail-' + id);
    const icon = document.getElementById('cr-icon-' + id);

    detail.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// TOGGLE DETAIL — Procurement
function togglePoDetail(id) {
    const detail = document.getElementById('po-detail-' + id);
    const icon = document.getElementById('po-icon-' + id);

    detail.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>

@endsection
