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
            <span class="ml-1 bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">{{ $crPending->total() }}</span>
        </button>
        <button onclick="switchTab('po')" id="tab-po"
            class="tab-btn px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 bg-gray-200 text-gray-600 hover:bg-gray-300">
            Procurement
            <span class="ml-1 bg-gray-300 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $poPending->total() }}</span>
        </button>
    </div>

    {{-- ============================================= --}}
    {{-- TAB 1: CUSTOMER REQUEST --}}
    {{-- ============================================= --}}
    <div id="panel-cr" class="tab-panel">

        {{-- TABEL PENDING --}}
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> Menunggu Approval
        </h2>

        @if($crPending->isEmpty())
        <div class="bg-white rounded-2xl shadow p-8 text-center mb-8">
            <p class="text-gray-400">Tidak ada Customer Request yang menunggu approval.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($crPending as $cr)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cr->request_code }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cr->customer_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cr->tanggal }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cr->user->name_user ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Waiting</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @php $myApproval = $cr->approvals->where('role', auth()->user()->position)->first(); @endphp
                                @if($myApproval && $myApproval->status == 'pending')
                                <form action="{{ route('approval.customer_request', $cr->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approved">
                                    <button type="submit" onclick="return confirm('Approve?')"
                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('approval.customer_request', $cr->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="rejected">
                                    <button type="submit" onclick="return confirm('Reject?')"
                                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition">
                                        Reject
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                                <a href="/customer-request/pdf/{{ $cr->id }}" target="_blank"
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                <span>{{ $crPending->firstItem() ?? 0 }}-{{ $crPending->lastItem() ?? 0 }} of {{ $crPending->total() }}</span>
                <div class="flex items-center gap-1 ml-4">
                    @if($crPending->onFirstPage())
                    <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                    @else
                    <a href="{{ $crPending->previousPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                    @endif
                    <span class="px-2 py-1 font-semibold text-gray-800">{{ $crPending->currentPage() }}</span>
                    @if($crPending->hasMorePages())
                    <a href="{{ $crPending->nextPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                    @else
                    <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- TABEL HISTORY --}}
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-gray-400 rounded-full"></span> History Approval
        </h2>

        @if($crHistory->isEmpty())
        <div class="bg-white rounded-2xl shadow p-8 text-center">
            <p class="text-gray-400">Belum ada history approval.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($crHistory as $cr)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cr->request_code }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cr->customer_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cr->tanggal }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cr->user->name_user ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($cr->status == 'approved')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="/customer-request/pdf/{{ $cr->id }}" target="_blank"
                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                <span>{{ $crHistory->firstItem() ?? 0 }}-{{ $crHistory->lastItem() ?? 0 }} of {{ $crHistory->total() }}</span>
                <div class="flex items-center gap-1 ml-4">
                    @if($crHistory->onFirstPage())
                    <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                    @else
                    <a href="{{ $crHistory->previousPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                    @endif
                    <span class="px-2 py-1 font-semibold text-gray-800">{{ $crHistory->currentPage() }}</span>
                    @if($crHistory->hasMorePages())
                    <a href="{{ $crHistory->nextPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                    @else
                    <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ============================================= --}}
    {{-- TAB 2: PROCUREMENT --}}
    {{-- ============================================= --}}
    <div id="panel-po" class="tab-panel hidden">

        {{-- TABEL PENDING --}}
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> Menunggu Approval
        </h2>

        @if($poPending->isEmpty())
        <div class="bg-white rounded-2xl shadow p-8 text-center mb-8">
            <p class="text-gray-400">Tidak ada Procurement yang menunggu approval.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">No PO</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($poPending as $po)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $po->no_po }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $po->supplier->name_pt ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $po->tanggal }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('approval.procurement', $po->id_po) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approved">
                                    <button type="submit" onclick="return confirm('Approve?')"
                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('approval.procurement', $po->id_po) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="rejected">
                                    <button type="submit" onclick="return confirm('Reject?')"
                                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition">
                                        Reject
                                    </button>
                                </form>
                                <a href="/procurement/pdf/{{ $po->id_po }}" target="_blank"
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                <span>{{ $poPending->firstItem() ?? 0 }}-{{ $poPending->lastItem() ?? 0 }} of {{ $poPending->total() }}</span>
                <div class="flex items-center gap-1 ml-4">
                    @if($poPending->onFirstPage())
                    <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                    @else
                    <a href="{{ $poPending->previousPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                    @endif
                    <span class="px-2 py-1 font-semibold text-gray-800">{{ $poPending->currentPage() }}</span>
                    @if($poPending->hasMorePages())
                    <a href="{{ $poPending->nextPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                    @else
                    <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- TABEL HISTORY --}}
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-gray-400 rounded-full"></span> History Approval
        </h2>

        @if($poHistory->isEmpty())
        <div class="bg-white rounded-2xl shadow p-8 text-center">
            <p class="text-gray-400">Belum ada history approval.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">No PO</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($poHistory as $po)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $po->no_po }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $po->supplier->name_pt ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $po->tanggal }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($po->status == 'approved')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="/procurement/pdf/{{ $po->id_po }}" target="_blank"
                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex items-center justify-end px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
                <span>{{ $poHistory->firstItem() ?? 0 }}-{{ $poHistory->lastItem() ?? 0 }} of {{ $poHistory->total() }}</span>
                <div class="flex items-center gap-1 ml-4">
                    @if($poHistory->onFirstPage())
                    <span class="px-2 py-1 text-gray-300">&lsaquo;</span>
                    @else
                    <a href="{{ $poHistory->previousPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&lsaquo;</a>
                    @endif
                    <span class="px-2 py-1 font-semibold text-gray-800">{{ $poHistory->currentPage() }}</span>
                    @if($poHistory->hasMorePages())
                    <a href="{{ $poHistory->nextPageUrl() }}" class="px-2 py-1 hover:text-gray-800 transition">&rsaquo;</a>
                    @else
                    <span class="px-2 py-1 text-gray-300">&rsaquo;</span>
                    @endif
                </div>
            </div>
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
</script>

@endsection
