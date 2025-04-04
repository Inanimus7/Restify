<?php

namespace Hotel;

use Hotel\BaseService;
use \DateTime;

class Booking extends BaseService // Corrected from BaserService
{
    public function getListByUser($userId)
{
    $parameters = [
        ':user_id' => $userId,
    ];

            // Explicitly select all columns from 'booking', and specific columns from 'room' and 'room_type'
            return $this->fetchAll('SELECT booking.*, room.*, room_type.title as room_type
                    FROM booking
                    INNER JOIN room ON booking.room_id = room.room_id 
                    INNER JOIN room_type ON room.type_id = room_type.type_id
                    WHERE booking.user_id = :user_id', $parameters);
        }


    public function bookNow($roomId, $userId, $checkInDate, $checkOutDate)
{
    // Begin transaction
    $this->getPdo()->beginTransaction();
    
    try {
        // Get room info
        $parameters = [
            ':room_id' => $roomId,
        ];
        $roomInfo = $this->fetch('SELECT * FROM room WHERE room_id = :room_id', $parameters);
        
        // Make sure the price column name matches your database
        $price = $roomInfo['price']; 
        
        // Ensure dates are in the correct format
        // If they're already in Y-m-d format (strings), we can use them directly
        $checkInString = $checkInDate;
        $checkOutString = $checkOutDate;
        
        // If we got DateTime objects, convert them to strings
        if ($checkInDate instanceof DateTime) {
            $checkInString = $checkInDate->format('Y-m-d');
        }
        
        if ($checkOutDate instanceof DateTime) {
            $checkOutString = $checkOutDate->format('Y-m-d');
        }
        
        // Calculate the days difference using DateTime
        $checkInDateTime = new DateTime($checkInString);
        $checkOutDateTime = new DateTime($checkOutString);
        $daysDiff = $checkOutDateTime->diff($checkInDateTime)->days;
        
        // If days difference is 0, make it at least 1 day
        if ($daysDiff <= 0) {
            $daysDiff = 1;
        }
        
        // Calculate the total price
        $totalPrice = $price * $daysDiff;
        
        // Insert the booking
        $parameters = [
            ':room_id' => $roomId,
            ':user_id' => $userId,
            ':total_price' => $totalPrice,
            ':check_in_date' => $checkInString,
            ':check_out_date' => $checkOutString
        ];
        
        $this->execute('INSERT INTO booking (room_id, user_id, total_price, check_in_date, check_out_date) 
                       VALUES (:room_id, :user_id, :total_price, :check_in_date, :check_out_date)', $parameters);
        
        // Commit the transaction
        $this->getPdo()->commit();
        return true;
    } catch (\Exception $e) {
        // If something went wrong, rollback
        $this->getPdo()->rollBack();
        error_log("Booking error: " . $e->getMessage());
        return false;
    }
}
    public function isBooked($roomId, $checkInDate, $checkOutDate)
    {
        // Ensure we're working with strings in 'Y-m-d' format
        $checkInString = $checkInDate;
        $checkOutString = $checkOutDate;
        
        // If we got DateTime objects, convert them to strings
        if ($checkInDate instanceof DateTime) {
            $checkInString = $checkInDate->format('Y-m-d');
        }
        
        if ($checkOutDate instanceof DateTime) {
            $checkOutString = $checkOutDate->format('Y-m-d');
        }
        
        $parameters = [
            ':room_id' => $roomId,
            ':check_in_date' => $checkInString, 
            ':check_out_date' => $checkOutString,
        ];
        
        // Make sure our date strings are valid
        if (empty($checkInString) || empty($checkOutString)) {
            return true; // Consider it booked (unavailable) if dates are invalid
        }
        
        // The query to check for overlapping bookings
        $sql = 'SELECT booking_id 
                FROM booking 
                WHERE room_id = :room_id 
                AND check_out_date > :check_in_date 
                AND check_in_date < :check_out_date';
        
        $rows = $this->fetchAll($sql, $parameters);
        
        return count($rows) > 0;
    }
}