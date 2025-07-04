<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bookings;
use App\Models\Vehicles;
use App\Models\Approvals;
use App\Models\UsersParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        try {
            Log::info('BOOKING_INDEX: User accessing booking list', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'timestamp' => now()
            ]);

            $bookings = Bookings::with(['user', 'vehicle'])->get();

            Log::info('BOOKING_INDEX_SUCCESS: Booking list loaded successfully', [
                'user_id' => Auth::id(),
                'total_bookings' => $bookings->count(),
                'timestamp' => now()
            ]);

            return view('pemesanan.index', compact('bookings'));
        } catch (\Exception $e) {
            Log::error('BOOKING_INDEX_ERROR: Error loading booking list', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);
            throw $e;
        }
    }

    public function show()
    {
        try {
            Log::info('BOOKING_ADD_FORM: User accessing booking add form', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'timestamp' => now()
            ]);

            $userParent = UsersParent::where('user_id', Auth::id())->first();

            if (!$userParent) {
                Log::warning('BOOKING_ADD_FORM_ERROR: User has no superior for approval', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'timestamp' => now()
                ]);

                return redirect()->route('booking.index')
                    ->with('error', 'Tidak dapat membuat pemesanan, karena user yang digunakan tidak memiliki atasan untuk approval.');
            }

            $vehicles = Vehicles::all();
            $drivers = User::where('role', 'driver')->get();

            Log::info('BOOKING_ADD_FORM_SUCCESS: Form data loaded successfully', [
                'user_id' => Auth::id(),
                'vehicles_count' => $vehicles->count(),
                'drivers_count' => $drivers->count(),
                'user_parent_id' => $userParent->parent_id,
                'timestamp' => now()
            ]);

            return view('pemesanan.add', compact('vehicles', 'drivers'));
        } catch (\Exception $e) {
            Log::error('BOOKING_ADD_FORM_ERROR: Error loading booking form', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);
            throw $e;
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('BOOKING_STORE: User attempting to create booking', [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);

            $validated = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_id' => 'nullable|exists:users,id',
                'purpose' => 'required|string|max:255',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
            ]);

            // Find the first parent of the current user
            $userParent = UsersParent::where('user_id', Auth::id())->first();

            if (!$userParent) {
                Log::warning('BOOKING_STORE_ERROR: User has no superior for approval', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'timestamp' => now()
                ]);

                return redirect()->back()
                    ->with('error', 'Tidak dapat membuat pemesanan, karena user yang digunakan tidak memiliki atasan untuk approval.')
                    ->withInput();
            }

            // Begin transaction
            DB::beginTransaction();
            Log::info('BOOKING_STORE_TRANSACTION: Database transaction started', [
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $booking = new Bookings();
            $booking->user_id = Auth::id();
            $booking->vehicle_id = $validated['vehicle_id'];
            $booking->driver_id = $validated['driver_id'];
            $booking->purpose = $validated['purpose'];
            $booking->start_time = $validated['start_time'];
            $booking->end_time = $validated['end_time'];
            $booking->status = 'pending';
            $booking->save();

            Log::info('BOOKING_STORE_BOOKING_CREATED: Booking record created', [
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'driver_id' => $booking->driver_id,
                'purpose' => $booking->purpose,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'timestamp' => now()
            ]);

            // Create initial approval record
            $approval = new Approvals();
            $approval->booking_id = $booking->id;
            $approval->approver_id = $userParent->parent_id;
            $approval->level = 1;
            $approval->status = 'pending';
            $approval->save();

            Log::info('BOOKING_STORE_APPROVAL_CREATED: Approval record created', [
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'approval_id' => $approval->id,
                'approver_id' => $approval->approver_id,
                'level' => $approval->level,
                'timestamp' => now()
            ]);

            // Commit transaction
            DB::commit();
            Log::info('BOOKING_STORE_SUCCESS: Booking created successfully', [
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'timestamp' => now()
            ]);

            return redirect()->route('booking.index')
                ->with('success', 'Pemesanan kendaraan berhasil dibuat dan menunggu persetujuan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('BOOKING_STORE_VALIDATION_ERROR: Validation failed', [
                'user_id' => Auth::id(),
                'validation_errors' => $e->errors(),
                'request_data' => $request->except(['_token']),
                'timestamp' => now()
            ]);
            throw $e;
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            Log::error('BOOKING_STORE_ERROR: Error creating booking', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
                'timestamp' => now()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat membuat pemesanan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function detailView($id)
    {
        try {
            Log::info('BOOKING_DETAIL: User accessing booking detail', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'timestamp' => now()
            ]);

            $booking = Bookings::with(['user', 'vehicle', 'driver', 'approvals.approver'])
                ->findOrFail($id);

            // Get all drivers for admin change driver functionality
            $drivers = User::where('role', 'driver')->get();

            Log::info('BOOKING_DETAIL_SUCCESS: Booking detail loaded successfully', [
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'booking_status' => $booking->status,
                'requester_id' => $booking->user_id,
                'vehicle_id' => $booking->vehicle_id,
                'driver_id' => $booking->driver_id,
                'approvals_count' => $booking->approvals->count(),
                'timestamp' => now()
            ]);

            return view('pemesanan.detail', compact('booking', 'drivers'));
        } catch (\Exception $e) {
            Log::error('BOOKING_DETAIL_ERROR: Error loading booking detail', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);
            throw $e;
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            Log::info('BOOKING_APPROVE: User attempting to approve/reject booking', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'action' => $request->input('action'),
                'has_notes' => !empty($request->input('notes')),
                'timestamp' => now()
            ]);

            $booking = Bookings::with('approvals')->findOrFail($id);
            $action = $request->input('action');
            $notes = $request->input('notes');

            // Check if user has permission to approve
            $user = Auth::user();
            $canApprove = false;

            // Check if level 0 approval exists and is approved
            $level0Approved = $booking->approvals->where('level', 0)->where('status', 'approved')->first();

            if ($user->role === 'admin') {
                // Admin can only approve if level 0 hasn't approved yet
                if (!$level0Approved) {
                    $canApprove = true;
                }
            } else {
                // Check if user is the parent/superior of the booking requester
                $userParent = UsersParent::where('user_id', $booking->user_id)
                    ->where('parent_id', $user->id)
                    ->first();
                if ($userParent) {
                    $canApprove = true;
                }
            }

            if (!$canApprove) {
                Log::warning('BOOKING_APPROVE_UNAUTHORIZED: User not authorized to approve', [
                    'user_id' => Auth::id(),
                    'user_role' => $user->role,
                    'booking_id' => $id,
                    'booking_requester_id' => $booking->user_id,
                    'level0_approved' => $level0Approved ? true : false,
                    'timestamp' => now()
                ]);

                return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan approval.');
            }

            DB::beginTransaction();
            Log::info('BOOKING_APPROVE_TRANSACTION: Database transaction started', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'timestamp' => now()
            ]);

            // Update or create approval record
            $approval = Approvals::where('booking_id', $booking->id)
                ->where('approver_id', $user->id)
                ->first();

            if (!$approval) {
                $approval = new Approvals();
                $approval->booking_id = $booking->id;
                $approval->approver_id = $user->id;
                $approval->level = 0;

                Log::info('BOOKING_APPROVE_NEW_APPROVAL: Creating new approval record', [
                    'user_id' => Auth::id(),
                    'booking_id' => $id,
                    'level' => 0,
                    'timestamp' => now()
                ]);
            } else {
                Log::info('BOOKING_APPROVE_UPDATE_APPROVAL: Updating existing approval record', [
                    'user_id' => Auth::id(),
                    'booking_id' => $id,
                    'approval_id' => $approval->id,
                    'current_status' => $approval->status,
                    'timestamp' => now()
                ]);

                // Update booking status
                if ($action === 'approve') {
                    $booking->status = 'approved';
                    $message = 'Pemesanan berhasil disetujui.';
                }
            }

            if ($action === 'reject') {
                $booking->status = 'rejected';
                $message = 'Pemesanan berhasil ditolak.';
            }
            $booking->save();

            $approval->status = $action === 'approve' ? 'approved' : 'rejected';
            $approval->notes = $notes;
            $approval->approved_at = now();
            $approval->save();

            Log::info('BOOKING_APPROVE_SUCCESS: Booking approval processed successfully', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'action' => $action,
                'new_booking_status' => $booking->status,
                'approval_id' => $approval->id,
                'approval_status' => $approval->status,
                'has_notes' => !empty($notes),
                'timestamp' => now()
            ]);

            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BOOKING_APPROVE_ERROR: Error processing approval', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'action' => $request->input('action'),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses approval.');
        }
    }

    public function changeDriver(Request $request, $id)
    {
        try {
            Log::info('BOOKING_CHANGE_DRIVER: Admin attempting to change driver', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'new_driver_id' => $request->input('driver_id'),
                'timestamp' => now()
            ]);

            $booking = Bookings::with('approvals')->findOrFail($id);

            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                Log::warning('BOOKING_CHANGE_DRIVER_UNAUTHORIZED: Non-admin user attempted to change driver', [
                    'user_id' => Auth::id(),
                    'user_role' => Auth::user()->role,
                    'booking_id' => $id,
                    'timestamp' => now()
                ]);

                return redirect()->back()->with('error', 'Hanya admin yang dapat mengganti driver.');
            }

            // Check if level 0 approval exists and is approved
            $level0Approved = $booking->approvals->where('level', 0)->where('status', 'approved')->first();
            if ($level0Approved) {
                Log::warning('BOOKING_CHANGE_DRIVER_BLOCKED: Cannot change driver after level 0 approval', [
                    'user_id' => Auth::id(),
                    'booking_id' => $id,
                    'level0_approval_id' => $level0Approved->id,
                    'timestamp' => now()
                ]);

                return redirect()->back()->with('error', 'Tidak dapat mengganti driver setelah level 0 telah approve.');
            }

            $request->validate([
                'driver_id' => 'nullable|exists:users,id'
            ]);

            $oldDriverId = $booking->driver_id;
            $booking->driver_id = $request->driver_id;
            $booking->save();

            $driverName = $request->driver_id ? User::find($request->driver_id)->name : 'Tidak ada driver';

            Log::info('BOOKING_CHANGE_DRIVER_SUCCESS: Driver changed successfully', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'old_driver_id' => $oldDriverId,
                'new_driver_id' => $request->driver_id,
                'new_driver_name' => $driverName,
                'timestamp' => now()
            ]);

            return redirect()->back()->with('success', 'Driver berhasil diubah menjadi: ' . $driverName);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('BOOKING_CHANGE_DRIVER_VALIDATION_ERROR: Validation failed', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'validation_errors' => $e->errors(),
                'timestamp' => now()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('BOOKING_CHANGE_DRIVER_ERROR: Error changing driver', [
                'user_id' => Auth::id(),
                'booking_id' => $id,
                'new_driver_id' => $request->input('driver_id'),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengganti driver.');
        }
    }
}