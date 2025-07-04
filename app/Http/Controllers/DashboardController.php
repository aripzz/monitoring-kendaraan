<?php

namespace App\Http\Controllers;

use App\Models\Vehicles;
use App\Models\Bookings;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $totalVehicles = Vehicles::count();

        // Get vehicles in use today
        $vehiclesInUseToday = Bookings::whereDate('start_time', '<=', Carbon::today())
            ->whereDate('end_time', '>=', Carbon::today())
            ->where('status', 'approved')
            ->count();

        // Get pending bookings
        $pendingBookings = Bookings::where('status', 'pending')->count();

        // Get vehicles approaching service date (within next 30 days)
        $upcomingServices = Vehicles::whereNotNull('next_service_date')
            ->whereBetween('next_service_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->orderBy('next_service_date', 'asc')
            ->get();

        // Get monthly usage data for chart (last 7 months)
        $monthlyUsage = [];
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Bookings::whereYear('start_time', $month->year)
                ->whereMonth('start_time', $month->month)
                ->where('status', 'approved')
                ->count();
            $monthlyUsage[$month->format('M')] = $count;
        }

        return view('dashboard', compact(
            'totalVehicles',
            'vehiclesInUseToday',
            'pendingBookings',
            'upcomingServices',
            'monthlyUsage'
        ));
    }
}