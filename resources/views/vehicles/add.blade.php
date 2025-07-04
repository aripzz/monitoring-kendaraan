@extends('layouts.app')

@section('title', 'Tambah Kendaraan - Monitoring Kendaraan')

@section('content')
    <h2 class="text-3xl font-bold mb-6">Tambah Kendaraan Baru</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('vehicles.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="plate_number" class="block text-gray-700 text-sm font-bold mb-2">Plat Nomor</label>
                <input type="text" name="plate_number" id="plate_number"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('plate_number') border-red-500 @enderror"
                    value="{{ old('plate_number') }}" required>
                @error('plate_number')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="model" class="block text-gray-700 text-sm font-bold mb-2">Model</label>
                <input type="text" name="model" id="model"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('model') border-red-500 @enderror"
                    value="{{ old('model') }}" required>
                @error('model')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Jenis</label>
                <select name="type" id="type"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('type') border-red-500 @enderror"
                    required>
                    <option value="">Pilih Jenis</option>
                    <option value="pengangkut_orang" {{ old('type') == 'pengangkut_orang' ? 'selected' : '' }}>Pengangkut
                        Orang</option>
                    <option value="pengangkut_barang" {{ old('type') == 'pengangkut_barang' ? 'selected' : '' }}>Pengangkut
                        Barang</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="owner" class="block text-gray-700 text-sm font-bold mb-2">Pemilik</label>
                <select name="owner" id="owner"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('owner') border-red-500 @enderror"
                    required>
                    <option value="">Pilih Pemilik</option>
                    <option value="inhouse" {{ old('owner') == 'inhouse' ? 'selected' : '' }}>Inhouse</option>
                    <option value="rental" {{ old('owner') == 'rental' ? 'selected' : '' }}>Rental</option>
                </select>
                @error('owner')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="bbm" class="block text-gray-700 text-sm font-bold mb-2">Konsumsi BBM (km/L)</label>
                <input type="number" step="0.01" name="bbm" id="bbm"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('bbm') border-red-500 @enderror"
                    value="{{ old('bbm') }}">
                @error('bbm')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="next_service_date" class="block text-gray-700 text-sm font-bold mb-2">Jadwal Servis</label>
                <input type="date" name="next_service_date" id="next_service_date"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('next_service_date') border-red-500 @enderror"
                    value="{{ old('next_service_date') }}">
                @error('next_service_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
                <a href="{{ route('vehicles.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
