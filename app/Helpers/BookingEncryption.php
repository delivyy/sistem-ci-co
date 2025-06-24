<?php

namespace App\Helpers;

class BookingEncryption
{
    /**
     * Encrypt the booking ID for URL sharing
     *
     * @param string $bookingId
     * @return string
     */
    public static function encrypt($bookingId)
    {
        // Use Laravel's encryption with base64 encoding for URL safety
        $encrypted = encrypt($bookingId);
        return base64_encode($encrypted);
    }

    /**
     * Decrypt the booking ID from URL parameter
     *
     * @param string $encryptedBookingId
     * @return string
     */
    public static function decrypt($encryptedBookingId)
    {
        try {
            // Decode from base64 and decrypt
            $decoded = base64_decode($encryptedBookingId);
            return decrypt($decoded);
        } catch (\Exception $e) {
            // Return empty string or handle error
            return '';
        }
    }
}