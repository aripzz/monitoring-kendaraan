<?php

namespace App\Http\Controllers;

use App\Models\Vehicles;
use Illuminate\Http\Request;

class VehiclesController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicles::query();

        // Filter by type if provided
        if ($request->has('type') && $request->type !== '' && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $vehicles = $query->get();
        $selectedType = $request->get('type', 'all');

        return view('vehicles.index', compact('vehicles', 'selectedType'));
    }

    public function create()
    {
        return view('vehicles.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number',
            'model' => 'required|string|max:255',
            'type' => 'required|in:pengangkut_orang,pengangkut_barang',
            'owner' => 'required|in:inhouse,rental',
            'bbm' => 'nullable|numeric|min:0',
            'next_service_date' => 'nullable|date',
        ]);

        $vehicle = new Vehicles();
        $vehicle->plate_number = $validated['plate_number'];
        $vehicle->model = $validated['model'];
        $vehicle->type = $validated['type'];
        $vehicle->owner = $validated['owner'];
        $vehicle->bbm = $validated['bbm'];
        $vehicle->next_service_date = $validated['next_service_date'];
        $vehicle->save();

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $vehicle = Vehicles::findOrFail($id);
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicles::findOrFail($id);

        $validated = $request->validate([
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number,' . $vehicle->id,
            'model' => 'required|string|max:255',
            'type' => 'required|in:pengangkut_orang,pengangkut_barang',
            'owner' => 'required|in:inhouse,rental',
            'bbm' => 'nullable|numeric|min:0',
            'next_service_date' => 'nullable|date',
        ]);

        $vehicle->plate_number = $validated['plate_number'];
        $vehicle->model = $validated['model'];
        $vehicle->type = $validated['type'];
        $vehicle->owner = $validated['owner'];
        $vehicle->bbm = $validated['bbm'];
        $vehicle->next_service_date = $validated['next_service_date'];
        $vehicle->save();

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicles::findOrFail($id);
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function history($id)
    {
        $vehicle = Vehicles::findOrFail($id);

        // Get all bookings for this vehicle with related data
        $bookings = \App\Models\Bookings::with(['user', 'vehicle', 'driver'])
            ->where('vehicle_id', $id)
            ->orderBy('start_time', 'desc')
            ->get();

        return view('vehicles.history', compact('vehicle', 'bookings'));
    }
}