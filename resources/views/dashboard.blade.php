@extends('layouts.app') {{-- Extends the master layout --}}

@section('title', 'Dashboard - Monitoring Kendaraan') {{-- Sets the title for this page --}}

@push('styles')
    {{-- Add any dashboard-specific styles here if needed --}}
@endpush

@section('content')
    <h2 class="text-3xl font-bold mb-6">Dashboard Monitoring Kendaraan</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Kendaraan</h3>
            <p class="text-4xl font-bold text-blue-600">{{ $totalVehicles }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Kendaraan Terpakai Hari Ini</h3>
            <p class="text-4xl font-bold text-green-600">{{ $vehiclesInUseToday }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pemesanan Menunggu Persetujuan</h3>
            <p class="text-4xl font-bold text-yellow-600">{{ $pendingBookings }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Grafik Pemakaian Kendaraan Bulanan</h3>
        </div>
        <canvas id="vehicleUsageChart"></canvas>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Kendaraan Mendekati Jadwal Servis</h3>
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Plat Nomor
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Model
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tanggal Servis Berikutnya
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($upcomingServices as $vehicle)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $vehicle->plate_number }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $vehicle->model }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $vehicle->next_service_date ? date('d/m/Y', strtotime($vehicle->next_service_date)) : '-' }}
                            </p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Tidak ada kendaraan yang mendekati jadwal servis
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('vehicleUsageChart').getContext('2d');
        const vehicleUsageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($monthlyUsage)) !!},
                datasets: [{
                    label: 'Jumlah Pemakaian Kendaraan',
                    data: {!! json_encode(array_values($monthlyUsage)) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Contoh fetch data dashboard dari API (gunakan Axios atau Fetch API)
        // fetch('/api/dashboard-data')
        //     .then(response => response.json())
        //     .then(data => {
        //         // Update statistik
        //         document.querySelector('.text-blue-600').textContent = data.stats.total_vehicles;
        //         document.querySelector('.text-green-600').textContent = data.stats.vehicles_in_use_today;
        //         document.querySelector('.text-yellow-600').textContent = data.stats.pending_bookings;

        //         // Update chart
        //         vehicleUsageChart.data.labels = Object.keys(data.monthly_usage_chart_data);
        //         vehicleUsageChart.data.datasets[0].data = Object.values(data.monthly_usage_chart_data);
        //         vehicleUsageChart.update();

        //         // Update upcoming services table (perlu loop dan append HTML)
        //     })
        //     .catch(error => console.error('Error fetching dashboard data:', error));
    </script>
@endpush
