@extends('layouts.app')

@section('title', 'Konfigurasi Atasan - Monitoring Kendaraan')

@section('content')
    <h2 class="text-3xl font-bold mb-6">Konfigurasi Atasan untuk {{ $user->name }}</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('users.update-superior', $user->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="superior_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Atasan</label>
                <select name="superior_id" id="superior_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('superior_id') border-red-500 @enderror">
                    <option value="">Pilih Atasan</option>
                    @foreach ($allUsers as $potentialSuperior)
                        <option value="{{ $potentialSuperior->id }}"
                            {{ in_array($potentialSuperior->id, $currentSuperiors) ? 'selected' : '' }}>
                            {{ $potentialSuperior->name }} ({{ $potentialSuperior->email }}) -
                            {{ $potentialSuperior->role }}
                        </option>
                    @endforeach
                </select>
                @error('superior_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Konfigurasi
                </button>
                <a href="{{ route('users.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi</h3>
        <p class="text-blue-700 text-sm">
            Pilih satu atasan untuk user ini. Atasan yang dipilih akan memiliki akses untuk menyetujui permintaan dari user
            ini.
        </p>
    </div>
@endsection
