<?php
namespace Hotel;

use PDO;
use DateTime;
use Hotel\BaseService;
use Exception;

class Room extends BaseService
{ 

   public function get($roomId)
    {
        $parameters = [
            ':room_id'=> $roomId,
        ];
        return $this->fetch('SELECT * FROM room WHERE room_id = :room_id', $parameters);
    }
   public function getCities() 
   {
       // Get all cities
       $cities = [];
       $rows = $this->fetchAll('SELECT DISTINCT city FROM room');
       
       // Loop through and populate the $cities array with city names
       foreach ($rows as $row) {
           $cities[] = $row['city'];
       }
   
       return $cities;
   }
   
   /**
    * Get a single room by its ID
    * 
    * @param int $roomId The ID of the room to retrieve
    * @return array|bool The room data or false if not found
    */
   public function getRoomById($roomId) 
   {
       // Validate room ID
       if (!is_numeric($roomId) || $roomId <= 0) {
           return false;
       }
       
       // Build the SQL query to fetch room details
        $sql = 'SELECT 
        room.*,
        room.name AS hotel_name, 
        room.city, 
        room.address, 
        room.photo_url, 
        room.count_of_guests, 
        room.price AS price_per_night,
        room.description_short,
        room.description_long,
        room.wifi,
        room.parking,
        room.pet_friendly,
        room.avg_reviews,
        room.location_lat,     
        room.location_long,     
        CASE 
            WHEN room.type_id = 1 THEN "Single Room"
            WHEN room.type_id = 2 THEN "Double Room"
            WHEN room.type_id = 3 THEN "Triple Room"
            WHEN room.type_id = 4 THEN "Fourfold Room"
            ELSE "Unknown Room Type"
        END AS room_type_title
        FROM room
        WHERE room.room_id = :room_id';
       
       // Parameters for the query
       $parameters = [':room_id' => $roomId];
       
       // Execute the query
       try {
           $roomData = $this->fetch($sql, $parameters);
           
           if (!$roomData) {
               return false; // Room not found
           }
           
           // Fetch room reviews (if exist)
           $roomData['reviews'] = $this->getRoomReviews($roomId);
           
           return $roomData;
       } catch (Exception $e) {
           // Log the error
           error_log("Error fetching room data: " . $e->getMessage());
           return false;
       }
   }
   
   /**
    * Get reviews for a specific room
    * 
    * @param int $roomId The ID of the room to get reviews for
    * @return array The reviews for the room
    */
    public function getRoomReviews($roomId) 
    {
        if (!is_numeric($roomId) || $roomId <= 0) {
            return [];
        }
    
        $sql = "SELECT review.rate, review.comment, review.created_time, user.name AS user_name
                FROM review
                LEFT JOIN user ON review.user_id = user.user_id
                WHERE review.room_id = :room_id
                ORDER BY review.created_time DESC";
        
        $parameters = [':room_id' => $roomId];
        
        try {
            return $this->fetchAll($sql, $parameters);
        } catch (Exception $e) {
            error_log("Error fetching reviews: " . $e->getMessage());
            return [];
        }
    }
    
   public function search($checkInDate, $checkOutDate, $city = NULL, $typeId = NULL, $minPrice = NULL, $maxPrice = NULL) 
   {
       // Check if both dates are valid DateTime objects
       if (!$checkInDate instanceof DateTime || !$checkOutDate instanceof DateTime) {
           return ['results' => [], 'count' => 0]; // Return empty results if dates are invalid
       }
       
       // Additional validation: check if check-out date is after check-in date
       if ($checkOutDate < $checkInDate) {
           return ['results' => [], 'count' => 0, 'error' => 'Check-out date must be after check-in date'];
       }
       
       // Build query and parameters dynamically
       $parameters = [
           ':check_in_date' => $checkInDate->format('Y-m-d'),
           ':check_out_date' => $checkOutDate->format('Y-m-d')
       ];
   
       $sql = 'SELECT 
           room.*,
           room.name AS hotel_name, 
           room.city, 
           room.address, 
           room.photo_url, 
           room.count_of_guests, 
           room.price AS price_per_night,
           room.description_short, 
           CASE 
               WHEN room.type_id = 1 THEN "Single Room"
               WHEN room.type_id = 2 THEN "Double Room"
               WHEN room.type_id = 3 THEN "Triple Room"
               WHEN room.type_id = 4 THEN "Fourfold Room"
               ELSE "Unknown Room Type"
           END AS room_type_title
       FROM room
       WHERE 1=1';
   
       // Add city condition if provided and not null/empty
       if ($city !== NULL && $city !== '' && $city !== 'null') {
           $sql .= ' AND room.city = :city';
           $parameters[':city'] = $city;
       }
   
       // Add room type condition if provided and not null/empty
       if ($typeId !== NULL && $typeId !== '' && $typeId !== 'null') {
           $sql .= ' AND room.type_id = :type_id';
           $parameters[':type_id'] = $typeId;
       }
   
       // Add price range conditions
       // Convert price parameters to numeric to ensure proper comparison
       $minPrice = is_numeric($minPrice) ? (float)$minPrice : NULL;
       $maxPrice = is_numeric($maxPrice) ? (float)$maxPrice : NULL;
       
       // Add minimum price condition
       if ($minPrice !== NULL && $minPrice >= 0) {
           $sql .= ' AND room.price >= :min_price';
           $parameters[':min_price'] = $minPrice;
       }
       
       // Add maximum price condition
       if ($maxPrice !== NULL && $maxPrice > 0) {
           $sql .= ' AND room.price <= :max_price';
           $parameters[':max_price'] = $maxPrice;
       }
       
       // If both min and max are set, ensure min <= max
       if ($minPrice !== NULL && $maxPrice !== NULL && $minPrice > $maxPrice) {
           return ['results' => [], 'count' => 0, 'error' => 'Minimum price cannot be greater than maximum price'];
       }
   
       // Exclude rooms already booked during the selected dates
       $sql .= ' AND room.room_id NOT IN (
           SELECT room_id
           FROM booking
           WHERE check_in_date < :check_out_date 
           AND check_out_date > :check_in_date
       )'; 
   
       // Execute the query to get results
       try {
           $results = $this->fetchAll($sql, $parameters);
           
           // Get the count of results
           $count = count($results);
           
           return ['results' => $results, 'count' => $count];
       } catch (Exception $e) {
           // Log the error and return empty results
           error_log("Search query error: " . $e->getMessage());
           return ['results' => [], 'count' => 0, 'error' => 'An error occurred while searching'];
       }
   }
}