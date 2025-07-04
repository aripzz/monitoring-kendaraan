@extends('layouts.app')

@section('title', 'Riwayat Kendaraan - Monitoring Kendaraan')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold">Riwayat Penggunaan Kendaraan</h2>
        <a href="{{ route('vehicles.index') }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kendaraan
        </a>
    </div>

    {{-- Vehicle Information --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Kendaraan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
            <div class="space-y-3">
                <div class="flex">
                    <span class="font-medium w-32">Plat Nomor:</span>
                    <span>{{ $vehicle->plate_number }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Model:</span>
                    <span>{{ $vehicle->model }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Jenis:</span>
                    <span>{{ str_replace('_', ' ', ucfirst($vehicle->type)) }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex">
                    <span class="font-medium w-32">Pemilik:</span>
                    <span>{{ ucfirst($vehicle->owner) }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Konsumsi BBM:</span>
                    <span>{{ $vehicle->bbm ? $vehicle->bbm . ' km/L' : '-' }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Jadwal Servis:</span>
                    <span>{{ $vehicle->next_service_date ? date('d/m/Y', strtotime($vehicle->next_service_date)) : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking History Table --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Riwayat Pemesanan</h3>
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
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $index => $booking)
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
                                    {{ $booking->driver->name ?? 'Tidak ada driver' }}</p>
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
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="{{ route('booking.detail', $booking->id) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                Belum ada riwayat pemesanan untuk kendaraan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
