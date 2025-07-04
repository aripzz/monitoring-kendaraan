<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Kendaraan')</title> {{-- Dynamic title --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @stack('styles')
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white p-4 flex flex-col">
            <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>
            <ul class="flex-grow">
                {{-- Navigasi Sidebar --}}
                <li class="mb-2">
                    <a href="{{ route('dashboard') }}"
                        class="block p-2 rounded {{ Request::routeIs('dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                </li>
                @if (Auth::user()->role === 'admin')
                    <li class="mb-2">
                        <a href="{{ route('users.index') }}"
                            class="block p-2 rounded {{ Request::routeIs('users.index') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                            <i class="fas fa-users mr-2"></i> Master User
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('vehicles.index') }}"
                            class="block p-2 rounded {{ Request::routeIs('vehicles.manage') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                            <i class="fas fa-truck-pickup mr-2"></i> Master Kendaraan
                        </a>
                    </li>
                @endif
                <li class="mb-2">
                    <a href="{{ route('booking.index') }}"
                        class="block p-2 rounded {{ Request::routeIs('booking.index') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-alt mr-2"></i> Pemesanan
                    </a>
                </li>
                @if (Auth::user()->role === 'admin')
                    <li class="mb-2">
                        <a href="{{ route('reports') }}"
                            class="block p-2 rounded {{ Request::routeIs('reports') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                            <i class="fas fa-file-alt mr-2"></i> Laporan
                        </a>
                    </li>
                @endif
            </ul>
            <!-- Logout button di bagian bawah sidebar -->
            <div class="mt-auto">
                <form action="{{ route('logout-action') }}" method="POST">
                    @csrf {{-- CSRF Token for security --}}
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-8">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>

</html>
