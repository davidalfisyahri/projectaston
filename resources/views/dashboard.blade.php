@extends('main')
@section('title', 'Dashboard')
@section('container')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Selamat datang kembali, pantau performa operasional hari ini.</p>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Active CR -->
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Active Requests</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalActiveCR }}</h3>
            </div>
        </div>

        <!-- Pending CR -->
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending Approval</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalPendingCR }}</h3>
            </div>
        </div>

        <!-- Pending PO -->
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending Procurements</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalPendingPO }}</h3>
            </div>
        </div>



    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Line Chart (Trend) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Trend Permintaan (6 Bulan Terakhir)</h2>
            <div class="relative h-72">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart (Popular Grades) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Grade Beton Terpopuler</h2>
            <div class="relative h-64 flex justify-center items-center">
                <canvas id="donutChart"></canvas>
            </div>
            <p class="text-xs text-center text-gray-500 mt-4">*Berdasarkan jumlah transaksi keseluruhan</p>
        </div>

    </div>

    <!-- TABLES SECTION -->
    <div class="grid grid-cols-1 gap-6">

        <!-- Recent CR Table -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">5 Permintaan Terbaru</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Customer</th>
                            <th class="px-4 py-3 font-medium">Tgl Request</th>
                            <th class="px-4 py-3 rounded-r-lg font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentCR as $cr)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $cr->request_code }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $cr->customer_name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $cr->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-[10px] uppercase font-bold rounded-full 
                                                @if($cr->status == 'waiting_approval') bg-yellow-100 text-yellow-700
                                                @elseif($cr->status == 'approved') bg-green-100 text-green-700
                                                @elseif($cr->status == 'rejected') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-600 @endif">
                                        {{ str_replace('_', ' ', $cr->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data permintaan terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



    </div>

    <!-- CHART.JS INITIALIZATION -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // 1. Line Chart (Monthly Trend)
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const monthlyLabels = {!! json_encode(array_reverse($monthlyTrendLabels)) !!};
            const monthlyData = {!! json_encode(array_reverse($monthlyTrendData)) !!};

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Jumlah Customer Request',
                        data: monthlyData,
                        borderColor: '#2563eb', // blue-600
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4 // curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 } // Integer only
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 2. Doughnut Chart (Popular Grades)
            const donutCtx = document.getElementById('donutChart').getContext('2d');
            const donutLabels = {!! json_encode($donutLabels) !!};
            const donutData = {!! json_encode($donutData) !!};

            // Fallback if no data
            if (donutData.length === 0) {
                donutLabels.push('Belum ada data');
                donutData.push(1);
            }

            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutData,
                        backgroundColor: [
                            '#ef4444', // red-500
                            '#f59e0b', // amber-500
                            '#10b981', // emerald-500
                            '#3b82f6', // blue-500
                            '#8b5cf6'  // violet-500
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, usePointStyle: true, padding: 20 }
                        }
                    }
                }
            });

        });
    </script>

@endsection