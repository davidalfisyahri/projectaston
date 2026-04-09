@extends('main')
@section('title', 'Dashboard')
@section('container')

<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="bg-white p-6 rounded-xl shadow">
    <div class="grid md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h3 class="text-gray-500 text-sm">Approval Waiting</h3>
            <p class="text-3xl font-bold text-red-700 mt-2">12</p>
        </div>
    
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h3 class="text-gray-500 text-sm">Approval</h3>
            <p class="text-3xl font-bold text-yellow-500 mt-2">8</p>
        </div>
    
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h3 class="text-gray-500 text-sm">Completed</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">25</p>
        </div>
    
    </div>
    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-lg font-semibold mb-4 text-gray-700">
            Total Penjualan
        </h2>

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>

                <tbody class="text-sm text-gray-700">

                    <tr class="border-b">
                        <td class="p-3">01-04-2026</td>
                        <td class="p-3">PT ABC</td>
                        <td class="p-3">Beton K-250</td>
                        <td class="p-3">Rp 5.000.000</td>
                        <td class="p-3 text-green-600">Completed</td>
                    </tr>

                    <tr class="border-b">
                        <td class="p-3">02-04-2026</td>
                        <td class="p-3">PT XYZ</td>
                        <td class="p-3">Beton K-300</td>
                        <td class="p-3">Rp 7.500.000</td>
                        <td class="p-3 text-yellow-500">Approval</td>
                    </tr>

                    <tr>
                        <td class="p-3">03-04-2026</td>
                        <td class="p-3">CV Maju</td>
                        <td class="p-3">Beton K-200</td>
                        <td class="p-3">Rp 3.200.000</td>
                        <td class="p-3 text-red-600">Waiting</td>
                    </tr>

                </tbody>

            </table>

        </div>
    
</div>

@endsection
