@extends('layouts.app')

@section('title', 'Tambah Booking - Monitoring Kendaraan')

@section('content')
    <h2 class="text-3xl font-bold mb-6">Tambah Booking Kendaraan</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('booking.store') }}" method="POST">
            @csrf

            <input type="hidden" name="user_id" value="{{ Auth::id() }}">

            <div class="mb-4">
                <label for="vehicle_id" class="block text-gray-700 text-sm font-bold mb-2">Kendaraan</label>
                <select name="vehicle_id" id="vehicle_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('vehicle_id') border-red-500 @enderror"
                    required>
                    <option value="">Pilih Kendaraan</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="driver_id" class="block text-gray-700 text-sm font-bold mb-2">Driver</label>
                <select name="driver_id" id="driver_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('driver_id') border-red-500 @enderror">
                    <option value="">Pilih Driver</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                @error('driver_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="purpose" class="block text-gray-700 text-sm font-bold mb-2">Tujuan</label>
                <input type="text" name="purpose" id="purpose"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('purpose') border-red-500 @enderror"
                    value="{{ old('purpose') }}" required>
                @error('purpose')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Waktu Mulai</label>
                <input type="datetime-local" name="start_time" id="start_time"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('start_time') border-red-500 @enderror"
                    value="{{ old('start_time') }}" required>
                @error('start_time')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">Waktu Selesai</label>
                <input type="datetime-local" name="end_time" id="end_time"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('end_time') border-red-500 @enderror"
                    value="{{ old('end_time') }}" required>
                @error('end_time')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
                <a href="{{ route('booking.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
