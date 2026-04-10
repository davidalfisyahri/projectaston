<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Istimewa beton</title>
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

            <h2 class="text-3xl font-bold text-center text-white mb-2">
                Login
            </h2>

            <p class="text-center text-gray-300 text-sm mb-6">
                Silakan masuk ke akun Anda
            </p>

            {{-- ERROR --}}
            @if(session('error'))
                <div class="bg-red-500 text-white p-2 rounded mb-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- USERNAME -->
                <div>
                    <label class="text-sm text-gray-300">Username</label>
                    <input type="text" name="username" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white">
                </div>
            
                <!-- PASSWORD -->
                <div>
                    <label class="text-sm text-gray-300">Password</label>
                    <input type="password" name="password" required
                        class="w-full mt-1 px-4 py-3 rounded-xl bg-white/20 text-white">
                </div>

                <!-- BUTTON -->
                <div class="grid grid-cols-2 gap-3">

                    <a href="/" 
                       class="text-center border border-white/30 text-white 
                              py-3 rounded-xl backdrop-blur-md bg-white/10
                              hover:bg-white/20 transition font-semibold">
                        Back
                    </a>
                
                    <button type="submit"
                        class="bg-red-700 text-white py-3 rounded-xl w-full">
                        Login
                    </button>
                
                </div>

            </form>

        </div>

    </div>

</body>
</html>