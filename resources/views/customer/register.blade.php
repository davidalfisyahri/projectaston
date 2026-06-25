<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Customer - PT Istimewa Aston Indonesia</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-900">

    <!-- Background -->
    <div class="absolute inset-0">
        <img src="{{ asset('asset/image/pabrik_luar.jpeg') }}" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-red-900/80 to-black/80"></div>
    </div>

    <!-- Register Card -->
    <div class="relative w-full max-w-lg mx-4 my-8">

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                    rounded-3xl shadow-2xl p-8">

            <h2 class="text-3xl font-bold text-center text-white mb-2">
                Daftar Akun Customer
            </h2>

            <p class="text-center text-gray-300 text-sm mb-6">
                Buat akun untuk memesan beton secara mandiri
            </p>

            {{-- ERRORS --}}
            @if($errors->any())
                <div class="bg-red-500/80 text-white p-3 rounded-xl mb-4 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('customer.register') }}" class="space-y-4">
                @csrf

                <!-- NAMA -->
                <div>
                    <label class="text-sm text-gray-300">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name_user" value="{{ old('name_user') }}" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Nama lengkap Anda">
                </div>

                <!-- USERNAME -->
                <div>
                    <label class="text-sm text-gray-300">Username <span class="text-red-400">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Username untuk login">
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="text-sm text-gray-300">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="email@contoh.com">
                </div>

                <!-- PHONE -->
                <div>
                    <label class="text-sm text-gray-300">No. Telepon <span class="text-red-400">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="08xxxxxxxxxx">
                </div>

                <!-- ALAMAT -->
                <div>
                    <label class="text-sm text-gray-300">Alamat</label>
                    <textarea name="address" rows="2"
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Alamat lengkap (opsional)">{{ old('address') }}</textarea>
                </div>

                <!-- NPWP -->
                <div>
                    <label class="text-sm text-gray-300">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp') }}"
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Nomor NPWP (opsional)">
                </div>

                <!-- PASSWORD -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm text-gray-300">Password <span class="text-red-400">*</span></label>
                        <input type="password" name="password" required
                            class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Min. 5 karakter">
                    </div>
                    <div>
                        <label class="text-sm text-gray-300">Konfirmasi <span class="text-red-400">*</span></label>
                        <input type="password" name="password_confirmation" required
                            class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Ulangi password">
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="grid grid-cols-2 gap-3 pt-2">

                    <a href="{{ route('login_user') }}" 
                       class="text-center border border-white/30 text-white 
                              py-3 rounded-xl backdrop-blur-md bg-white/10
                              hover:bg-white/20 transition font-semibold">
                        Kembali
                    </a>
                
                    <button type="submit"
                        class="bg-red-700 hover:bg-red-800 text-white py-3 rounded-xl w-full font-semibold transition">
                        Daftar
                    </button>
                
                </div>

            </form>

        </div>

    </div>

</body>
</html>
