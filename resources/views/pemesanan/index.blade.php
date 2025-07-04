@extends('layouts.app')

@section('title', 'Pemesanan - Monitoring Kendaraan')

@section('content')
    <h2 class="text-3xl font-bold mb-6">Pemesanan Kendaraan</h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex justify-end">
        <a href="{{ route('booking.add') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            + Booking Kendaraan
        </a>
    </div>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Pemohon
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jenis Kendaraan
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tujuan
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tanggal Pemakaian
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
                @foreach ($bookings as $booking)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $booking->user->name ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $booking->vehicle ? str_replace('_', ' ', ucfirst($booking->vehicle->type)) : '-' }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $booking->purpose }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $booking->start_time->format('d/m/Y') }}
                                @if ($booking->start_time->format('Y-m-d') != $booking->end_time->format('Y-m-d'))
                                    s/d {{ $booking->end_time->format('d/m/Y') }}
                                @endif
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            @php
                                $statusColors = [
                                    'pending' => ['bg-yellow-200', 'text-yellow-900', 'Menunggu Approval'],
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
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $color[1] }}">
                                <span aria-hidden
                                    class="absolute inset-0 {{ $color[0] }} opacity-50 rounded-full"></span>
                                <span class="relative">{{ $color[2] }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="{{ route('booking.detail', $booking->id) }}"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs mr-2">Detail</a>
                        </td>
                    </tr>
                @endforeach

                @if (count($bookings) === 0)
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Tidak ada data pemesanan
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
