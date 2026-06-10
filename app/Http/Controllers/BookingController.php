<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Cache\CourtScheduleCache;
use App\Cache\User\BookingCache;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // $this->authorize('create', Booking::class);

        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'booking_date' => ['required', 'date', 'date_format:Y-m-d', function ($attribute, $value, $fail) {
                $tomorrow = Carbon::tomorrow()->toDateString();
                if ($value !== $tomorrow) {
                    $fail('Bookings are only allowed for the next day (' . $tomorrow . ').');
                }
            }],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) use ($request) {
                if ($request->filled('start_time') && $request->filled('end_time')) {
                    $start = Carbon::createFromFormat('H:i', $request->input('start_time'));
                    $end = Carbon::createFromFormat('H:i', $request->input('end_time'));
                    $expectedDuration = $start->diffInMinutes($end);
                    if ($expectedDuration !== (int) $value) {
                        $fail('The duration_minutes value must equal the difference between end_time and start_time (' . $expectedDuration . ' minutes).');
                    }
                }
            }],
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit_card,paypal,cash',
            'transaction_id' => 'sometimes|nullable|string',
            'reference_number' => 'sometimes|nullable|string',
            'attachment' => 'sometimes|nullable|string',
            'notes' => 'sometimes|nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $bookingDay = Carbon::parse($validated['booking_date'])->format('l');
            $bookingDay = strtolower($bookingDay);

            $schedules = CourtScheduleCache::getSchedules($validated['court_id'], $bookingDay);

            if ($schedules->isEmpty()) {
                return response()->json([
                    'message' => 'No court schedule exists for ' . $bookingDay . '. Please check available weekdays for this court.'
                ], 422);
            }

            $allowedSchedule = $schedules->first(function ($schedule) use ($validated) {
                return $schedule->status === 'available'
                    && $schedule->start_time <= $validated['start_time']
                    && $schedule->end_time >= $validated['end_time'];
            });

            if (!$allowedSchedule) {
                return response()->json([
                    'message' => 'The court is not scheduled as available for the requested time range on ' . $bookingDay . '.'
                ], 422);
            }

            $conflict = Booking::where('court_id', $validated['court_id'])
                ->where('booking_date', $validated['booking_date'])
                ->whereNotIn('status', ['cancelled'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhere(function ($query) use ($validated) {
                            $query->where('start_time', '<=', $validated['start_time'])
                                  ->where('end_time', '>=', $validated['end_time']);
                        });
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                return response()->json([
                    'message' => 'Requested time conflicts with an existing booking'
                ], 409);
            }

            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'court_id' => $validated['court_id'],
                'booking_date' => $validated['booking_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_minutes' => $validated['duration_minutes'],
                'amount' => $validated['amount'],
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'attachment' => $validated['attachment'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            BookingCache::clearBookings($request->user()->id);
            BookingCache::clearBooking($request->user()->id, $booking->id);

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking,
            ], 201);
        }, 3);
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $bookings = BookingCache::getBookings($userId, 300);

        return response()->json([
            'bookings' => $bookings,
        ]);
    }

    public function show(Request $request, $bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found',
            ], 404);
        }

        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        $userId = $request->user()->id;
        $bookingData = BookingCache::getBooking($userId, $booking->id, 300);

        return response()->json([
            'booking' => $bookingData,
        ]);
    }

    public function cancel(Request $request, $bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found',
            ], 404);
        }

        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        $createdAt = $booking->created_at;
        $now = Carbon::now();
        $minutesElapsed = $createdAt->diffInMinutes($now);

        if ($minutesElapsed > 5) {
            return response()->json([
                'message' => 'Booking cancellation is only allowed within 5 minutes of creation',
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => (string) $request->user()->id,
            'cancelled_at' => Carbon::now(),
        ]);

        BookingCache::clearBookings($request->user()->id);
        BookingCache::clearBooking($request->user()->id, $booking->id);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking,
        ]);
    }
}
