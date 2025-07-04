@extends('layouts.app')

@section('title', 'Laporan - Monitoring Kendaraan')

@section('content')
    <h2 class="text-3xl font-bold mb-6">Laporan Kendaraan</h2>

    {{-- Filter Section --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('reports') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label for="filter_type" class="block text-gray-700 text-sm font-bold mb-2">Jenis Laporan</label>
                <select name="filter_type" id="filter_type" onchange="toggleFilterInputs()"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $filterType === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <div id="monthly-filter" class="flex-1 min-w-48" style="{{ $filterType === 'yearly' ? 'display: none;' : '' }}">
                <label for="month" class="block text-gray-700 text-sm font-bold mb-2">Bulan</label>
                <input type="month" name="month" id="month" value="{{ $selectedMonth }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div id="yearly-filter" class="flex-1 min-w-48" style="{{ $filterType === 'monthly' ? 'display: none;' : '' }}">
                <label for="year" class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                <select name="year" id="year"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <button type="button" onclick="exportToExcel()"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Total Pemesanan</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $reportData['summary']['total_bookings'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Disetujui</h3>
            <p class="text-2xl font-bold text-green-600">{{ $reportData['summary']['approved_bookings'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Menunggu</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $reportData['summary']['pending_bookings'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Ditolak</h3>
            <p class="text-2xl font-bold text-red-600">{{ $reportData['summary']['rejected_bookings'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Selesai</h3>
            <p class="text-2xl font-bold text-gray-600">{{ $reportData['summary']['completed_bookings'] }}</p>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                Laporan {{ $reportData['summary']['period_type'] }} - {{ $reportData['summary']['period'] }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            No
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal Pemesanan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Pemohon
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kendaraan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Driver
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tujuan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Periode Pemakaian
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportData['bookings'] as $index => $booking)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $index + 1 }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $booking->created_at->format('d/m/Y H:i') }}
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $booking->user->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $booking->vehicle->plate_number ?? '-' }} - {{ $booking->vehicle->model ?? '-' }}
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $booking->driver->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $booking->purpose }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $booking->start_time->format('d/m/Y H:i') }} -
                                    {{ $booking->end_time->format('d/m/Y H:i') }}
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                @php
                                    $statusColors = [
                                        'pending' => ['bg-yellow-200', 'text-yellow-900', 'Menunggu'],
                                        'approved' => ['bg-green-200', 'text-green-900', 'Disetujui'],
                                        'rejected' => ['bg-red-200', 'text-red-900', 'Ditolak'],
                                        'completed' => ['bg-gray-200', 'text-gray-900', 'Selesai'],
                                    ];
                                    $color = $statusColors[$booking->status] ?? [
                                        'bg-gray-200',
                                        'text-gray-900',
                                        ucfirst($booking->status),
                                    ];
                                @endphp
                                <span
                                    class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $color[1] }}">
                                    <span aria-hidden
                                        class="absolute inset-0 {{ $color[0] }} opacity-50 rounded-full"></span>
                                    <span class="relative">{{ $color[2] }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                Tidak ada data pemesanan untuk periode yang dipilih
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleFilterInputs() {
            const filterType = document.getElementById('filter_type').value;
            const monthlyFilter = document.getElementById('monthly-filter');
            const yearlyFilter = document.getElementById('yearly-filter');

            if (filterType === 'monthly') {
                monthlyFilter.style.display = 'block';
                yearlyFilter.style.display = 'none';
            } else {
                monthlyFilter.style.display = 'none';
                yearlyFilter.style.display = 'block';
            }
        }

        function exportToExcel() {
            const form = document.querySelector('form');
            const formData = new FormData(form);

            // Create export URL with current filter parameters
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                params.append(key, value);
            }

            // Redirect to export endpoint
            window.location.href = '{{ route('reports.export') }}?' + params.toString();
        }
    </script>
@endpush
