@extends('layouts.app')

@section('title', 'Detail Pemesanan - Monitoring Kendaraan')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold">Detail Pemesanan Kendaraan</h2>
        <a href="{{ route('booking.index') }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Pemesanan
        </a>
    </div>

    {{-- Success/Error Messages --}}
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

    {{-- Booking Information --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Pemesanan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
            <div class="space-y-3">
                <div class="flex">
                    <span class="font-medium w-32">ID Pemesanan:</span>
                    <span>#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Pemohon:</span>
                    <span>{{ $booking->user->name ?? '-' }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Email Pemohon:</span>
                    <span>{{ $booking->user->email ?? '-' }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Tujuan:</span>
                    <span>{{ $booking->purpose }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Tanggal Dibuat:</span>
                    <span>{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex">
                    <span class="font-medium w-32">Kendaraan:</span>
                    <span>{{ $booking->vehicle->model ?? '-' }} ({{ $booking->vehicle->plate_number ?? '-' }})</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Jenis Kendaraan:</span>
                    <span>{{ $booking->vehicle ? str_replace('_', ' ', ucfirst($booking->vehicle->type)) : '-' }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Driver:</span>
                    <span>{{ $booking->driver->name ?? 'Tidak ada driver' }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Waktu Mulai:</span>
                    <span>{{ $booking->start_time->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex">
                    <span class="font-medium w-32">Waktu Selesai:</span>
                    <span>{{ $booking->end_time->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center">
                <span class="font-medium mr-3">Status Pemesanan:</span>
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
                    <span aria-hidden class="absolute inset-0 {{ $color[0] }} opacity-50 rounded-full"></span>
                    <span class="relative">{{ $color[2] }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Approval History --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Riwayat Persetujuan</h3>

        @if ($booking->approvals && $booking->approvals->count() > 0)
            <div class="space-y-4">
                @foreach ($booking->approvals as $approval)
                    <div
                        class="border-l-4 {{ $approval->status === 'approved' ? 'border-green-500' : ($approval->status === 'rejected' ? 'border-red-500' : 'border-yellow-500') }} pl-4 py-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">
                                    Level {{ $approval->level }} - {{ $approval->approver->name ?? 'Unknown' }}
                                </p>
                                <p class="text-sm text-gray-600">{{ $approval->approver->email ?? '-' }}</p>
                                @if ($approval->notes)
                                    <p class="text-sm text-gray-700 mt-1"><strong>Catatan:</strong> {{ $approval->notes }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                @php
                                    $approvalStatusColors = [
                                        'pending' => ['bg-yellow-200', 'text-yellow-900', 'Menunggu'],
                                        'approved' => ['bg-green-200', 'text-green-900', 'Disetujui'],
                                        'rejected' => ['bg-red-200', 'text-red-900', 'Ditolak'],
                                    ];
                                    $approvalColor = $approvalStatusColors[$approval->status] ?? [
                                        'bg-gray-200',
                                        'text-gray-900',
                                        ucfirst($approval->status),
                                    ];
                                @endphp
                                <span
                                    class="inline-block px-2 py-1 text-xs font-semibold {{ $approvalColor[1] }} {{ $approvalColor[0] }} rounded-full">
                                    {{ $approvalColor[2] }}
                                </span>
                                @if ($approval->approved_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $approval->approved_at->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada riwayat persetujuan</p>
        @endif
    </div>

    {{-- Vehicle Details --}}
    @if ($booking->vehicle)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Detail Kendaraan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <div class="space-y-3">
                    <div class="flex">
                        <span class="font-medium w-32">Plat Nomor:</span>
                        <span>{{ $booking->vehicle->plate_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32">Model:</span>
                        <span>{{ $booking->vehicle->model }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32">Jenis:</span>
                        <span>{{ str_replace('_', ' ', ucfirst($booking->vehicle->type)) }}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="font-medium w-32">Pemilik:</span>
                        <span>{{ ucfirst($booking->vehicle->owner) }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32">Konsumsi BBM:</span>
                        <span>{{ $booking->vehicle->bbm ? $booking->vehicle->bbm . ' km/L' : '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32">Jadwal Servis:</span>
                        <span>{{ $booking->vehicle->next_service_date ? date('d/m/Y', strtotime($booking->vehicle->next_service_date)) : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Driver Details --}}
    @if ($booking->driver)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Detail Driver</h3>
                @php
                    $level0Approved = $booking->approvals->where('level', 0)->where('status', 'approved')->first();
                @endphp
                @if (Auth::user()->role === 'admin' && $booking->status !== 'completed' && !$level0Approved)
                    <button onclick="showDriverModal()"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-exchange-alt mr-2"></i>Ganti Driver
                    </button>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <div class="space-y-3">
                    <div class="flex">
                        <span class="font-medium w-32">Nama:</span>
                        <span>{{ $booking->driver->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32">Email:</span>
                        <span>{{ $booking->driver->email }}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="font-medium w-32">Role:</span>
                        <span>{{ ucfirst($booking->driver->role) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Approval Actions --}}
    @php
        $canApprove = false;
        $level0Approved = false;

        // Check if level 0 approval exists and is approved
        $level0Approval = $booking->approvals->where('level', 0)->where('status', 'approved')->first();
        if ($level0Approval) {
            $level0Approved = true;
        }

        if (Auth::user()->role === 'admin' && !$level0Approved) {
            $canApprove = true;
        } elseif (Auth::user()->role !== 'admin') {
            // Check if current user is a parent/superior of the booking requester
            $userParent = App\Models\UsersParent::where('user_id', $booking->user_id)
                ->where('parent_id', Auth::user()->id)
                ->first();
            if ($userParent) {
                $canApprove = true;
            }
        }
    @endphp
    @if ($booking->status === 'pending' && $canApprove)
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Aksi Persetujuan</h3>
            <form action="{{ route('booking.approve', $booking->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="submit" name="action" value="reject"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-times-circle mr-2"></i>Tolak
                    </button>
                    <button type="submit" name="action" value="approve"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-check-circle mr-2"></i>Setujui
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Change Driver Modal --}}
    <div id="driverModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ganti Driver</h3>
                <form action="{{ route('booking.change-driver', $booking->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="driver_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Driver
                            Baru</label>
                        <select name="driver_id" id="driver_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Pilih Driver</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ $booking->driver_id == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="hideDriverModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showDriverModal() {
            document.getElementById('driverModal').classList.remove('hidden');
        }

        function hideDriverModal() {
            document.getElementById('driverModal').classList.add('hidden');
        }
    </script>
@endpush
