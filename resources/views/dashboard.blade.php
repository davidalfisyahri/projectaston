<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT. Istimewa Aston Indonesia</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-red-900 text-white p-6 space-y-4">

        <h2 class="text-xl font-bold mb-6">PT. Istimewa Aston Indonesia</h2>

        <a href="#" class="block bg-red-800 px-4 py-3 rounded-lg hover:bg-red-700">Order</a>
        <a href="#" class="block bg-red-800 px-4 py-3 rounded-lg hover:bg-red-700">Approval</a>
        <a href="#" class="block bg-red-800 px-4 py-3 rounded-lg hover:bg-red-700">Stock Opname</a>
        <a href="#" class="block bg-red-800 px-4 py-3 rounded-lg hover:bg-red-700">Procurement</a>
        <a href="#" class="block bg-red-800 px-4 py-3 rounded-lg hover:bg-red-700">Account</a>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-8">

        <!-- TITLE -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Dashboard
        </h1>

        <!-- CARD INFO -->
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

    </main>

</div>

</body>
</html>