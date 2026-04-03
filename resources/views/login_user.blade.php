<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Ajag Ujug</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-900">

    <!-- Background -->
    <div class="absolute inset-0">
        <img src="{{ asset('asset/image/pabrik_luar.jpeg') }}" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-red-900/80 to-black/80"></div>
    </div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md mx-4">

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                    rounded-3xl shadow-2xl p-8">

            <!-- Title -->
            <h2 class="text-3xl font-bold text-center text-white mb-2">
                Login
            </h2>

            <p class="text-center text-gray-300 text-sm mb-6">
                Silakan masuk ke akun Anda
            </p>

            <!-- Form -->
            <form class="space-y-5">

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-300">Email</label>
                    <input type="email" placeholder="Masukkan email"
                        class="w-full mt-1 px-4 py-3 rounded-xl 
                               bg-white/20 text-white placeholder-gray-300
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Password -->
                <div>
                    <label class="text-sm text-gray-300">Password</label>
                    <input type="password" placeholder="Masukkan password"
                        class="w-full mt-1 px-4 py-3 rounded-xl 
                               bg-white/20 text-white placeholder-gray-300
                               border border-white/30
                               focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Remember -->
                <div class="flex justify-between items-center text-sm text-gray-300">
                    {{-- <label class="flex items-center gap-2">
                        <input type="checkbox" class="accent-red-500">
                        Remember me
                    </label> --}}

                    {{-- <a href="#" class="hover:text-white">
                        Lupa password?
                    </a> --}}
                </div>

                <!-- Button -->
                <div class="grid grid-cols-2 gap-3">

                    <!-- Back -->
                    <a href="/" 
                       class="text-center border border-white/30 text-white 
                              py-3 rounded-xl backdrop-blur-md bg-white/10
                              hover:bg-white/20 transition font-semibold">
                        Back
                    </a>
                
                    <!-- Login -->
                    <button type="submit"
                        class="bg-red-700 hover:bg-red-800 
                               text-white py-3 rounded-xl font-semibold 
                               transition shadow-lg hover:shadow-2xl">
                        Login
                    </button>
                
                </div>

            </form>

        </div>
        <!-- 🔥 BUTTON ADMIN -->
        <div class="mt-5 text-center">

        <a href="#" 
           class="inline-block w-full border border-white/30 text-white 
                  py-3 rounded-xl backdrop-blur-md bg-white/10
                  hover:bg-white/20 transition">
            Login sebagai Admin
        </a>

        </div>

    </div>

</body>
</html>