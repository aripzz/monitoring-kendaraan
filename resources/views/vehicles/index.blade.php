@extends('layouts.app') {{-- Extends the master layout --}}

@section('title', 'Users - Monitoring Kendaraan') {{-- Sets the title for this page --}}


@section('content')
    <h2 class="text-3xl font-bold mb-6">Master Kendaraan</h2>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('vehicles.create') }}"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            + Tambah Kendaraan
        </a>
        <div class="relative">
            <form action="{{ route('vehicles.index') }}" method="GET" class="inline">
                <select name="type" onchange="this.form.submit()"
                    class="block appearance-none bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                    <option value="all" {{ $selectedType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="pengangkut_orang" {{ $selectedType === 'pengangkut_orang' ? 'selected' : '' }}>Pengangkut
                        Orang</option>
                    <option value="pengangkut_barang" {{ $selectedType === 'pengangkut_barang' ? 'selected' : '' }}>
                        Pengangkut Barang</option>
                </select>
            </form>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
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
                        Jenis
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Konsumsi BBM (km/L)
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jadwal Servis
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vehicles as $vehicle)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $vehicle->plate_number }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $vehicle->model }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ str_replace('_', ' ', ucfirst($vehicle->type)) }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $vehicle->bbm ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $vehicle->next_service_date ? date('d/m/Y', strtotime($vehicle->next_service_date)) : '-' }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="{{ route('vehicles.edit', $vehicle->id) }}"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs mr-1">Edit</a>
                            <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus kendaraan ini?')"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs mr-1">Hapus</button>
                            </form>
                            <a href="{{ route('vehicles.history', $vehicle->id) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">Riwayat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
