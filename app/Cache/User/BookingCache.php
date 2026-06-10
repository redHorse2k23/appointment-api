<?php

namespace App\Cache\User;

use App\Cache\CacheProvider;
use App\Models\Booking;

class BookingCache extends CacheProvider
{
    public function __construct($key, $expiration = 600, $store = null)
    {
        parent::__construct($key, $expiration, $store);
    }

    public static function forUser($userId, $ttl = 600)
    {
        return new static('user_bookings_' . $userId, $ttl);
    }

    public static function forBooking($userId, $bookingId, $ttl = 600)
    {
        return new static('user_booking_' . $userId . '_' . $bookingId, $ttl);
    }

    public static function getBookings($userId, $ttl = 600)
    {
        return static::forUser($userId, $ttl)->rememberData(function () use ($userId) {
            return Booking::where('user_id_1', $userId)
                ->orderBy('booking_date', 'desc')
                ->get();
        });
    }

    public static function getBooking($userId, $bookingId, $ttl = 600)
    {
        return static::forBooking($userId, $bookingId, $ttl)->rememberData(function () use ($userId, $bookingId) {
            return Booking::where('user_id_1', $userId)
                ->where('id', $bookingId)
                ->first();
        });
    }

    public static function clearBookings($userId)
    {
        static::forUser($userId)->clearData();
    }

    public static function clearBooking($userId, $bookingId)
    {
        static::forBooking($userId, $bookingId)->clearData();
    }
}
