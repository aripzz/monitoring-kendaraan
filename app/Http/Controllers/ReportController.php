<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Vehicles;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->get('filter_type', 'monthly');
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedYear = $request->get('year', Carbon::now()->year);

        // Get report data based on filter type
        if ($filterType === 'monthly') {
            $reportData = $this->getMonthlyReport($selectedMonth);
        } else {
            $reportData = $this->getYearlyReport($selectedYear);
        }

        return view('reports', compact('reportData', 'filterType', 'selectedMonth', 'selectedYear'));
    }

    private function getMonthlyReport($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $bookings = Bookings::with(['user', 'vehicle', 'driver'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time', 'desc')
            ->get();

        $summary = [
            'total_bookings' => $bookings->count(),
            'approved_bookings' => $bookings->where('status', 'approved')->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'rejected_bookings' => $bookings->where('status', 'rejected')->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'period' => $startDate->format('F Y'),
            'period_type' => 'Bulanan'
        ];

        return [
            'bookings' => $bookings,
            'summary' => $summary
        ];
    }

    private function getYearlyReport($year)
    {
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();

        $bookings = Bookings::with(['user', 'vehicle', 'driver'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time', 'desc')
            ->get();

        $summary = [
            'total_bookings' => $bookings->count(),
            'approved_bookings' => $bookings->where('status', 'approved')->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'rejected_bookings' => $bookings->where('status', 'rejected')->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'period' => $year,
            'period_type' => 'Tahunan'
        ];

        return [
            'bookings' => $bookings,
            'summary' => $summary
        ];
    }

    public function exportExcel(Request $request)
    {
        $filterType = $request->get('filter_type', 'monthly');
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedYear = $request->get('year', Carbon::now()->year);

        // Get report data based on filter type
        if ($filterType === 'monthly') {
            $reportData = $this->getMonthlyReport($selectedMonth);
            $filename = 'laporan_kendaraan_' . str_replace('-', '_', $selectedMonth) . '.csv';
        } else {
            $reportData = $this->getYearlyReport($selectedYear);
            $filename = 'laporan_kendaraan_' . $selectedYear . '.csv';
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Headers
            fputcsv($file, [
                'No',
                'Tanggal Pemesanan',
                'Pemohon',
                'Kendaraan',
                'Driver',
                'Tujuan',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Status'
            ]);

            // CSV Data
            foreach ($reportData['bookings'] as $index => $booking) {
                fputcsv($file, [
                    $index + 1,
                    $booking->created_at->format('d/m/Y H:i'),
                    $booking->user->name ?? '-',
                    ($booking->vehicle->plate_number ?? '-') . ' - ' . ($booking->vehicle->model ?? '-'),
                    $booking->driver->name ?? '-',
                    $booking->purpose,
                    $booking->start_time->format('d/m/Y H:i'),
                    $booking->end_time->format('d/m/Y H:i'),
                    ucfirst($booking->status)
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}