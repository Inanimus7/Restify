<?php
namespace Hotel;

use DateTime;
use Exception;

class Booking extends BaseService 
{
    /**
     * Book a room for specific dates
     * 
     * @param int $roomId The ID of the room to book
     * @param int $userId The ID of the user booking the room
     * @param DateTime $checkInDate The check-in date
     * @param DateTime $checkOutDate The check-out date
     * @return bool|array Booking details or false if booking fails
     */
    public function bookRoom($roomId, $userId, $checkInDate, $checkOutDate) 
    {
        // Validate inputs
        if (!$checkInDate instanceof DateTime || 
            !$checkOutDate instanceof DateTime || 
            $checkOutDate <= $checkInDate) {
            return false;
        }

        try {
            $pdo = $this->getPdo();
            $pdo->beginTransaction();

            // Check room availability
            $availabilitySql = "SELECT COUNT(*) as booked_count 
                                FROM booking 
                                WHERE room_id = :room_id 
                                AND (
                                    (check_in_date < :check_out_date) AND 
                                    (check_out_date > :check_in_date)
                                )";
            $availabilityStmt = $pdo->prepare($availabilitySql);
            $availabilityStmt->execute([
                ':room_id' => $roomId,
                ':check_in_date' => $checkInDate->format('Y-m-d'),
                ':check_out_date' => $checkOutDate->format('Y-m-d')
            ]);

            $availability = $availabilityStmt->fetch(PDO::FETCH_ASSOC);
            
            // If room is already booked during these dates, return false
            if ($availability['booked_count'] > 0) {
                return false;
            }

            // Insert booking
            $bookingSql = "INSERT INTO booking (
                room_id, 
                user_id, 
                check_in_date, 
                check_out_date, 
                created_at
            ) VALUES (
                :room_id, 
                :user_id, 
                :check_in_date, 
                :check_out_date, 
                NOW()
            )";
            $bookingStmt = $pdo->prepare($bookingSql);
            $bookingStmt->execute([
                ':room_id' => $roomId,
                ':user_id' => $userId,
                ':check_in_date' => $checkInDate->format('Y-m-d'),
                ':check_out_date' => $checkOutDate->format('Y-m-d')
            ]);

            $bookingId = $pdo->lastInsertId();

            // Commit the transaction
            $pdo->commit();

            return [
                'booking_id' => $bookingId,
                'room_id' => $roomId,
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkOutDate->format('Y-m-d')
            ];
        } catch (Exception $e) {
            // Rollback in case of error
            $pdo->rollBack();
            error_log("Booking error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get bookings for a specific room
     * 
     * @param int $roomId The ID of the room
     * @return array List of current and future bookings
     */
    public function getRoomBookings($roomId) 
    {
        $sql = "SELECT 
                    check_in_date, 
                    check_out_date 
                FROM booking 
                WHERE room_id = :room_id 
                AND check_out_date >= CURDATE()";
        
        $parameters = [':room_id' => $roomId];
        
        return $this->fetchAll($sql, $parameters);
    }

    /**
     * Check room availability for given dates
     * 
     * @param int $roomId The ID of the room
     * @param DateTime $checkInDate The check-in date
     * @param DateTime $checkOutDate The check-out date
     * @return bool Whether the room is available
     */
    public function isRoomAvailable($roomId, $checkInDate, $checkOutDate)
    {
        $sql = "SELECT COUNT(*) as booked_count 
                FROM booking 
                WHERE room_id = :room_id 
                AND (
                    (check_in_date < :check_out_date) AND 
                    (check_out_date > :check_in_date)
                )";
        
        $parameters = [
            ':room_id' => $roomId,
            ':check_in_date' => $checkInDate->format('Y-m-d'),
            ':check_out_date' => $checkOutDate->format('Y-m-d')
        ];

        $result = $this->fetch($sql, $parameters);
        
        return $result['booked_count'] == 0;
    }
}