<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT. Istimewa Aston Indonesia</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans text-gray-800 scroll-smooth">

<!-- Navbar -->
<header class="fixed w-full bg-white/80 backdrop-blur-md shadow-sm z-50">
    <div class="max-w-7xl mx-auto flex items-center px-6 py-4">

        <!-- Kiri -->
        <div class="flex items-center gap-10">
            <div class="flex items-center gap-3">
                <img src="{{ asset('asset/image/Logo_aston.png') }}" alt="Logo" class="w-10 h-10 object-contain">
            </div>
    
            <nav class="hidden md:flex gap-8 text-sm font-medium">
                <a href="#home" class="hover:text-red-700 transition">Home</a>
                <a href="#about" class="hover:text-red-700 transition">About</a>
                <a href="#product" class="hover:text-red-700 transition">Product</a>
                <a href="#contact" class="hover:text-red-700 transition">Contact</a>
            </nav>
        </div>
    
        <!-- Kanan -->
        <div class="ml-auto">
            <button class="bg-red-700 hover:bg-red-800 text-white px-5 py-2 rounded-xl text-sm shadow">
                Login
            </button>
        </div>
    
    </div>
</header>

<!-- Hero -->
<section id="home" class="h-screen relative flex items-center justify-center text-white">

    <div class="absolute inset-0 bg-cover bg-center" 
     style="background-image: url('{{ asset('asset/image/pabrik_aston2.jpg') }}');">
    </div>

    <div class="absolute inset-0 bg-gradient-to-r from-red-900/80 to-black/70"></div>

    <div class="relative text-center px-6">
        <h1 class="text-4xl md:text-6xl font-bold mb-4 tracking-wide">PT. ISTIMEWA ASTON INDONESIA</h1>
        <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto">Solusi profesional dengan sentuhan kualitas dan kepercayaan untuk bisnis Anda.</p>

    </div>
</section>

<!-- About -->
<section id="about" class="py-24 px-6 max-w-6xl mx-auto text-center">
    <h2 class="text-3xl font-bold mb-6 text-red-800">About Us</h2>
    <p class="text-gray-600 max-w-3xl mx-auto">PT. Istimewa Aston Indonesia adalah perusahaan penyedia material konstruksi terkemuka yang berdedikasi untuk
mendukung pembangunan infrastruktur dan properti di seluruh Indonesia. Berbekal pengalaman dan komitmen
terhadap kualitas, kami hadir sebagai mitra strategis dan solusi satu atap (one-stop solution) untuk kebutuhan Beton
Siap Pakai (Readymix Concrete) dan Material Alam berkualitas tinggi.
Kami memahami bahwa kekuatan sebuah bangunan berawal dari material dasarnya. Oleh karena itu, kami
menerapkan standar kontrol kualitas yang ketat pada setiap armada beton dan kubikasi material alam yang kami
distribusikan, memastikan proyek Anda berdiri kokoh, aman, dan tahan lama.</p>
</section>

<!-- Legalitas -->
<section class="py-24 px-6 bg-red-50">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-6 text-red-800">Legalitas</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">Nama: PT. Istimewa Aston Indonesia
            Domisili: Kabupaten Bogor</p>
        <p>Legalitas: SK Menkumham No. AHU-0008876.AH.01.01.2026.</p>
    </div>
</section>

<!-- Motto -->
<section class="py-24 px-6 bg-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-10 text-red-800">Motto</h2>

        <div class="flex flex-col md:flex-row items-center justify-center gap-8">
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 bg-red-700 rounded-full"></div>
                <p class="text-gray-700">Istimewa
                    Kualitasnya</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-4 h-4 bg-red-700 rounded-full"></div>
                <p class="text-gray-700">Istimewa
                    Pelayanannya</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-4 h-4 bg-red-700 rounded-full"></div>
                <p class="text-gray-700">Istimewa
                    harganya</p>
            </div>
        </div>
    </div>
</section>

<!-- Visi Misi -->
<section class="py-24 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 text-center">

        <div class="p-6 rounded-2xl shadow bg-white border-t-4 border-red-700">
            <h3 class="font-semibold text-lg mb-2 text-red-700">Visi</h3>
            <p class="text-gray-600 text-sm">Menjadi perusahaan
                penyedia material
                konstruksi terpercaya
                dan terdepan di
                Indonesia yang
                mengutamakan kualitas,
                ketepatan waktu, dan
                kepuasan pelanggan.</p>
        </div>
        <div class="p-6 rounded-2xl shadow bg-white border-t-4 border-red-700">
            <h3 class="font-semibold text-lg mb-2 text-red-700">Misi</h3>
            <p class="text-gray-600 text-sm">Menyediakan produk beton dan
                material alam dengan spesifikasi
                yang terstandardisasi dan mutu
                terjamin.
                Memberikan pelayanan prima
                melalui pengiriman yang tepat
                waktu dan dukungan teknis yang
                profesional.
                Menjalin kemitraan jangka panjang
                yang saling menguntungkan
                dengan kontraktor, developer,
                maupun pelanggan individu.
                Mendukung percepatan
                pembangunan infrastruktur nasional
                dengan material yang efisien dan
                ramah lingkungan.</p>
        </div>
    </div>
</section>

<!-- Products -->
<section id="product" class="py-24 px-6 bg-red-50">
    <h2 class="text-3xl font-bold mb-16 text-center text-red-800">
        Our Products
    </h2>

    <!-- ================= PRODUCT A ================= -->
    <div class="max-w-7xl mx-auto mb-20">

        <!-- Title -->
        <div class="mb-8">
            <h3 class="text-2xl font-semibold text-red-800">Beton</h3>
            <p class="text-gray-500 text-sm">Kategori produk A</p>
        </div>

        <!-- Cards -->
        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition p-5">
                <div class="h-40 bg-gray-200 rounded-xl mb-4"></div>
                <h4 class="font-semibold text-red-700">Sub Product A1</h4>
                <p class="text-sm text-gray-500 mt-1">Lorem ipsum dolor sit amet</p>
            </div>

        </div>
    </div>


    <!-- ================= PRODUCT B ================= -->
    <div class="max-w-7xl mx-auto">

        <!-- Title -->
        <div class="mb-8">
            <h3 class="text-2xl font-semibold text-red-800">Product B</h3>
            <p class="text-gray-500 text-sm">Kategori produk B</p>
        </div>

        <!-- Cards -->
        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition p-5">
                <div class="h-40 bg-gray-200 rounded-xl mb-4"></div>
                <h4 class="font-semibold text-red-700">Sub Product B1</h4>
                <p class="text-sm text-gray-500 mt-1">Lorem ipsum dolor sit amet</p>
            </div>

            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition p-5">
                <div class="h-40 bg-gray-200 rounded-xl mb-4"></div>
                <h4 class="font-semibold text-red-700">Sub Product B2</h4>
                <p class="text-sm text-gray-500 mt-1">Lorem ipsum dolor sit amet</p>
            </div>

            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition p-5">
                <div class="h-40 bg-gray-200 rounded-xl mb-4"></div>
                <h4 class="font-semibold text-red-700">Sub Product B3</h4>
                <p class="text-sm text-gray-500 mt-1">Lorem ipsum dolor sit amet</p>
            </div>

        </div>
    </div>

</section>

<!-- Footer -->
<footer id="contact" class="bg-red-900 text-white py-12 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
        <div>
            <h2 class="text-xl font-bold mb-4">PT. Istimewa Aston Indonesia</h2>
            <p class="text-gray-300 text-sm">Perusahaan profesional dengan kualitas dan kepercayaan tinggi.</p>
        </div>
        <div>
            <h3 class="font-semibold mb-3">Contact</h3>
            <p class="text-gray-300 text-sm">Phone: 08123456789</p>
            <p class="text-gray-300 text-sm">Email: info@ajagujug.com</p>
            <p class="text-gray-300 text-sm">Address: Indonesia</p>
        </div>
        <div>
            <h3 class="font-semibold mb-3">Quick Links</h3>
            <p class="text-gray-300 text-sm">Home</p>
            <p class="text-gray-300 text-sm">About</p>
            <p class="text-gray-300 text-sm">Product</p>
            <p class="text-gray-300 text-sm">Contact</p>
        </div>
    </div>

    <div class="text-center text-gray-400 text-sm mt-10">
        © 2026 PT. Istimewa Aston Indonesia. All rights reserved.
    </div>
</footer>


<script>
// Smooth scroll with offset (biar tidak ketutup navbar)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));
        const offset = 80; // tinggi navbar

        const bodyRect = document.body.getBoundingClientRect().top;
        const elementRect = target.getBoundingClientRect().top;
        const elementPosition = elementRect - bodyRect;
        const offsetPosition = elementPosition - offset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    });
});

// Animation fade + slide saat muncul
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('opacity-100','translate-y-0');
        }
    });
}, { threshold: 0.2 });

document.querySelectorAll('section').forEach(section => {
    section.classList.add('opacity-0','translate-y-10','transition','duration-700');
    observer.observe(section);
});
</script>

</body>
</html>
