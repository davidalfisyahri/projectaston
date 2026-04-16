<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT. Istimewa Aston Indonesia</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">

<div class="min-h-screen">

    <!-- SIDEBAR -->
    <aside class="fixed top-0 left-0 z-50 w-20 hover:w-64 h-screen bg-red-900 text-white p-4 transition-all duration-300 group flex flex-col shadow-xl">
        <!-- ATAS (PROFILE + MENU) -->
        <div>
    
            <!-- PROFILE -->
            <div class="flex flex-col items-center mb-10">
    
                <div class="w-10 h-10 flex items-center justify-center rounded-full 
                bg-white/20 border-2 border-white
                transition-all duration-300 group-hover:w-16 group-hover:h-16">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-15 h-15 text-white"
             fill="currentColor"
             viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" 
                  d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
        </svg>

    </div>
    
                <h3 class="mt-3 text-sm font-semibold opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                    {{ auth()->user()->name_user }}
                </h3>
    
                <p class="text-xs text-gray-300 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                    {{ auth()->user()->position }}
                </p>
            </div>
    
            <!-- MENU -->
            <div class="space-y-4">
        
                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- ICON  -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>    
                    </svg>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Dashboard
                    </span>
                </a>
    
                <!-- ITEM -->
                <a href="{{ route('customer_req') }}"
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- ICON  -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                    </svg>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Customer request
                    </span>
                </a>
        
                <!-- COPY ITEM -->
                <a href="#" 
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- WRAPPER ICON -->
                <div class="relative flex items-center justify-center">
    
                    <!-- ICON -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                    </svg>
    
                    <!-- BADGE -->
                    <span class="absolute -top-1 -right-2 
                    bg-red-600 text-white text-[10px] 
                    px-1.5 py-0.5 rounded-full font-bold">
                        5
                    </span>
    
                </div>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Approval
                    </span>
                </a>
        
                <a href="#" 
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- ICON (langsung tanpa wrapper lebar) -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2A.5.5 0 0 1 16 9.07v4.286a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2.001a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.357V9.071a.5.5 0 0 1 .252-.434L3.75 6.638V2.643a.5.5 0 0 1 .252-.434zM4.25 7.504 1.508 9.071l2.742 1.567 2.742-1.567zM7.5 9.933l-2.75 1.571v3.134l2.75-1.571zm1 3.134 2.75 1.571v-3.134L8.5 9.933zm.508-3.996 2.742 1.567 2.742-1.567-2.742-1.567zm2.242-2.433V3.504L8.5 5.076V8.21zM7.5 8.21V5.076L4.75 3.504v3.134zM5.258 2.643 8 4.21l2.742-1.567L8 1.076zM15 9.933l-2.75 1.571v3.134L15 13.067zM3.75 14.638v-3.134L1 9.933v3.134z"/>
                    </svg>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Stock Opname
                    </span>
                </a>
        
                <a href="{{ route('procurement') }}" 
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- ICON (langsung tanpa wrapper lebar) -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z"/>
                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zm6.854 7.354-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708"/>                  
                    </svg>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Procurement
                    </span>
                </a>

                <a href="{{ route('inventory') }}" 
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- ICON (langsung tanpa wrapper lebar) -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.01-.003.268-.108a.75.75 0 0 1 .558 0l.269.108.01.003zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z"/>
                    </svg>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Inventory
                    </span>
                </a>

                @if(auth()->user()->role === 'superadmin')
                <a href="{{ route('setting') }}" 
                class="flex items-center gap-2 px-3 py-3 rounded-xl 
                hover:bg-red-800 transition
                justify-start">
    
                    <!-- ICON (langsung tanpa wrapper lebar) -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 16 16">
                        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                    </svg>
    
                    <!-- TEXT -->
                    <span class="opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                        Settings
                    </span>
                </a>
                @endif
                
        
            </div>
        </div>
    
        <!-- 🔴 LOGOUT -->
        <div class="mt-auto pt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                
                <button type="submit"
                onclick="return confirm('Yakin mau logout?')"
                class="w-full flex items-center gap-3 px-3 py-3 rounded-xl
                    bg-white/10 hover:bg-white/20
                    border border-white/10 hover:border-white/20
                    transition-all duration-200
                    group">

                <!-- ICON -->
                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 flex-shrink-0"
                                    fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                                    <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>

                </svg>

                <!-- TEXT -->
                <span class="text-sm font-medium text-white/80
                            opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                    Logout
                </span>

                </button>
    
            </form>
    
        </div>
    
    </aside>
    
    <!-- CONTENT -->
    <main class="ml-20 p-8">

        @yield('container')

    </main>

</div>

</body>
</html>