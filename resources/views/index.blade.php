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
            <a href="{{ route('login_user') }}" 
               class="bg-red-700 hover:bg-red-800 text-white px-5 py-2 rounded-xl text-sm shadow">
                Login
            </a>
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
        <ul class="space-y-3 text-gray-600 text-sm list-disc list-inside">
        <p class="text-gray-600 max-w-3xl mx-auto">Nama: PT. Istimewa Aston Indonesia
        Domisili: Kabupaten Bogor</p>
        Legalitas: SK Menkumham No. AHU-0008876.AH.01.01.2026.
        </ul>
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
    
            <h3 class="font-semibold text-lg mb-4 text-red-700 text-center">
                Misi
            </h3>
        
            <ul class="space-y-3 text-gray-600 text-sm list-disc list-inside">
        
                <li>
                    Menyediakan produk beton dan material alam dengan spesifikasi
                    yang terstandardisasi dan mutu terjamin.
                </li>
        
                <li>
                    Memberikan pelayanan prima melalui pengiriman yang tepat waktu
                    dan dukungan teknis yang profesional.
                </li>
        
                <li>
                    Menjalin kemitraan jangka panjang yang saling menguntungkan
                    dengan kontraktor, developer, maupun pelanggan individu.
                </li>
        
                <li>
                    Mendukung percepatan pembangunan infrastruktur nasional
                    dengan material yang efisien dan ramah lingkungan.
                </li>
        
            </ul>
        
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
            <h3 class="text-2xl font-semibold text-red-800">BETON</h3>
            <p class="text-gray-500 text-sm">PT ISTIMEWA ASTON INDONESIA
                menyediakan semua jenis mutu beton
                diantara mutu tersebut</p>
        </div>

        <!-- Cards -->
        <div class="grid md:grid-cols-1 gap-5">

            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition p-6">
        
                <h4 class="font-semibold text-red-700 mb-4 text-center">
                    Jenis beton
                </h4>
        
                <!-- Grid isi -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 text-sm text-gray-700">
        
                    <div class="bg-red-50 p-2 rounded text-center">K-100 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-225 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-350 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-475 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 25 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FS 45 NFA</div>
        
                    <div class="bg-red-50 p-2 rounded text-center">K-100 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-225 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-350 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-475 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 25 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FS 45 FA</div>
        
                    <div class="bg-red-50 p-2 rounded text-center">K-125 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-250 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-375 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-500 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 30 NFA</div>
        
                    <div class="bg-gray-100 p-2 rounded text-center">K-125 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-250 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-375 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-500 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 30 FA</div>
        
                    <div class="bg-red-50 p-2 rounded text-center">K-150 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-275 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-400 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 10 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 35 NFA</div>
        
                    <div class="bg-gray-100 p-2 rounded text-center">K-150 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-275 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-400 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 10 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 35 FA</div>
        
                    <div class="bg-red-50 p-2 rounded text-center">K-175 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-300 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-425 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 15 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 40 NFA</div>
        
                    <div class="bg-gray-100 p-2 rounded text-center">K-175 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-300 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-425 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 15 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 40 FA</div>
        
                    <div class="bg-red-50 p-2 rounded text-center">K-200 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-325 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-450 NFA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 20 NFA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 45 NFA</div>
        
                    <div class="bg-gray-100 p-2 rounded text-center">K-200 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">K-325 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">K-450 FA</div>
                    <div class="bg-red-50 p-2 rounded text-center">FC 20 FA</div>
                    <div class="bg-gray-100 p-2 rounded text-center">FC 45 FA</div>
        
                </div>
        
                <!-- Note -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 italic">
                        Serta menyediakan spesifikasi khusus sesuai kebutuhan
                    </p>
                </div>
        
            </div>
        
        </div>
    </div>


    <!-- ================= PRODUCT B ================= -->
    <div class="max-w-7xl mx-auto">

        <!-- Title -->
        <div class="mb-8">
            <h3 class="text-2xl font-semibold text-red-800">MATERIAL ALAM</h3>
            <p class="text-gray-500 text-sm">PT ISTIMEWA ASTON INDONESIA
                menggunakan material material yang
                berkualias seperti pasir tayan,
                sementiga roda dan batu</p>
        </div>

        <!-- Cards -->
        <div class="grid md:grid-cols-3 gap-8">

            <!-- Card -->
            <div class="group">
    
                <!-- Image -->
                <div class="relative h-[500px] rounded-3xl overflow-hidden shadow-lg">
            
                    <!-- Gambar -->
                    <img 
                        src="{{ asset('asset/image/semen_3_roda.jpg') }}" 
                        alt="Semen Tiga Roda"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
            
                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            
                    <!-- Title di dalam gambar -->
                    <div class="absolute bottom-4 left-4 text-white">
                        <h4 class="font-bold text-lg">SEMEN TIGA RODA</h4>
                        <span class="text-xs bg-red-600 px-2 py-1 rounded-full"></span>
                    </div>
                </div>
            
                <!-- Glass Content -->
                <div class="-mt-10 backdrop-blur-md bg-white/60 border border-white/40 rounded-2xl p-5 shadow-md mx-3">
            
                    <p class="text-sm text-gray-700">
                        Semen Tiga Roda OPC (Ordinary
                        Portland Cement) tipe I adalah semen
                        kualitas tinggi dari indocement untuk
                        struktur utama. Ini ideal untuk konstruksi
                        berat seperti bangunan bertingkat,
                        jembatan, dan jalan karena kekuatan
                        awal tinggi.
                    </p>
            
                </div>
            </div>
        
        
            <!-- Card 2 -->
            <div class="group">
    
                <!-- Image -->
                <div class="relative h-[500px] rounded-3xl overflow-hidden shadow-lg">
            
                    <!-- Gambar -->
                    <img 
                        src="{{ asset('asset/image/pasir_tayan.jpg') }}" 
                        alt="Semen Tiga Roda"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
            
                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            
                    <!-- Title di dalam gambar -->
                    <div class="absolute bottom-4 left-4 text-white">
                        <h4 class="font-bold text-lg">PASIR TAYAN</h4>
                        <span class="text-xs bg-red-600 px-2 py-1 rounded-full"></span>
                    </div>
                </div>
            
                <!-- Glass Content -->
                <div class="-mt-10 backdrop-blur-md bg-white/60 border border-white/40 rounded-2xl p-5 shadow-md mx-3">
            
                    <p class="text-sm text-gray-700">
                        Menurut SNI 03-2834-2000, Pasir tayan
                        sering kali memenuhi gradasi Zone I
                        (Pasir Kasar) hingga zone II. ini
                        menjadkannya sangat baik untuk
                        campuran beton struktural
                        Pasir Tayan berkualitas baik umumnya
                        memiliki kadar lumpur yang rendah,
                        memenuhi syarat kadar lumpur < 5%
                        berat sesuai persyaratan agregat halus,
                        sehingga cocok untuk beton yang sesuai
                        dengan spesifikasi project.
                    </p>
            
                </div>
            </div>
        
        
            <!-- Card 3 -->
            <div class="group">
    
                <!-- Image -->
                <div class="relative h-[500px] rounded-3xl overflow-hidden shadow-lg">
            
                    <!-- Gambar -->
                    <img 
                        src="{{ asset('asset/image/batu_split.jpg') }}" 
                        alt="Semen Tiga Roda"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
            
                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            
                    <!-- Title di dalam gambar -->
                    <div class="absolute bottom-4 left-4 text-white">
                        <h4 class="font-bold text-lg">BATU SPLIT SUKABUMI</h4>
                        <span class="text-xs bg-red-600 px-2 py-1 rounded-full"></span>
                    </div>
                </div>
            
                <!-- Glass Content -->
                <div class="-mt-10 backdrop-blur-md bg-white/70 border border-white/40 rounded-2xl p-5 shadow-md mx-3 overflow-x-auto">

                    <h4 class="text-sm font-semibold text-red-700 mb-4 text-center">
                        Tabel Kualitas
                    </h4>
                
                    <table class="w-full text-sm text-gray-700 border-collapse">
                        
                        <!-- Head -->
                        <thead>
                            <tr class="bg-red-100 text-red-800">
                                <th class="p-2 text-left rounded-tl-lg">Parameter Teknis</th>
                                <th class="p-2 text-left">Standar ASTM</th>
                                <th class="p-2 text-left rounded-tr-lg">Nilai Ideal / Batas</th>
                            </tr>
                        </thead>
                
                        <!-- Body -->
                        <tbody class="divide-y">
                
                            <tr class="hover:bg-red-50 transition">
                                <td class="p-2">Penyerapan Air</td>
                                <td class="p-2">ASTM C127</td>
                                <td class="p-2">&lt; 2%</td>
                            </tr>
                
                            <tr class="hover:bg-red-50 transition">
                                <td class="p-2">Berat Jenis (SSD)</td>
                                <td class="p-2">ASTM C127</td>
                                <td class="p-2">2.5 – 2.8</td>
                            </tr>
                
                            <tr class="hover:bg-red-50 transition">
                                <td class="p-2">Gumpalan Lempung</td>
                                <td class="p-2">ASTM C142</td>
                                <td class="p-2">Maks. 3.0%</td>
                            </tr>
                
                            <tr class="hover:bg-red-50 transition">
                                <td class="p-2">Reaktivitas (ASR)</td>
                                <td class="p-2">ASTM C1260</td>
                                <td class="p-2">Ekspansi &lt; 0.10%</td>
                            </tr>
                
                            <tr class="hover:bg-red-50 transition">
                                <td class="p-2">Butiran Pipih/Lonjong</td>
                                <td class="p-2">ASTM D4791</td>
                                <td class="p-2">Maks. 10%</td>
                            </tr>
                
                        </tbody>
                    </table>
                
                </div>
            </div>
        
        </div>
    </div>

</section>

<!-- Footer -->
<footer id="contact" class="bg-red-900 text-white py-14 px-6">
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">

        <!-- Contact -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Contact</h3>

            <div class="space-y-3 text-base text-gray-200">

                <!-- Phone -->
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                      </svg>
                    <p>0851-2296-3317</p>
                </div>

                <!-- Email -->
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/>
                      </svg>
                    <p>istimewabetonindonesiapt@gmail.com</p>
                </div>

                <!-- Address -->
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                      </svg>
                    <p>Jl. Kp. Bojong Koneng, Cibinong, Kec. Cibinong, Kabupaten Bogor, Jawa Barat 16911</p>
                </div>

            </div>
        </div>
    </div>

    <div class="text-center text-gray-300 text-sm mt-12">
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
