<?php

namespace Hotel;

use Hotel\User;
use Hotel\BaseService;

class Review extends BaseService
{
    /**
     * Retrieve a list of reviews by a given user
     */
    public function getListByUser($userId)
    {
        $parameters = [
            ':user_id' => $userId,
        ];
        
        return $this->fetchAll(
            'SELECT review.*, room.name 
            FROM review
            INNER JOIN room ON review.room_id = room.room_id 
            WHERE review.user_id = :user_id', 
            $parameters
        );
    }

    /**
     * Add a review for a room
     */
    public function addReview($roomId, $userId, $rate, $comment)
    {
        try {
            $pdo = $this->getPdo();
            $pdo->beginTransaction();

            // Insert review data
            $parameters = [
                ':room_id' => $roomId,
                ':user_id' => $userId,
                ':rate' => $rate, 
                ':comment' => $comment,
            ];
            $this->execute(
                'INSERT INTO review (room_id, user_id, rate, comment) VALUES (:room_id, :user_id, :rate, :comment)',
                $parameters
            );

            // Calculate new average rating
            $parameters = [
                ':room_id' => $roomId
            ];
            $roomAverage = $this->fetch(
                'SELECT AVG(rate) AS avg_reviews, COUNT(*) AS count FROM review WHERE room_id = :room_id',
                $parameters
            );

            error_log("Room ID: $roomId, Avg Reviews: " . ($roomAverage['avg_reviews'] ?? 'NULL') . ", Count: " . ($roomAverage['count'] ?? 'NULL'));

            // Update room with new rating data
            $parameters = [
                ':room_id' => $roomId,
                ':avg_reviews' => $roomAverage['avg_reviews'] ?? 0,
                ':count_reviews' => $roomAverage['count'] ?? 0,
            ];
            $statement = $this->execute(
                'UPDATE room SET avg_reviews = :avg_reviews, count_reviews = :count_reviews WHERE room_id = :room_id',
                $parameters
            );
            
            $rowsAffected = $statement->rowCount();
            error_log("Rows affected by UPDATE: $rowsAffected");

            if ($rowsAffected === 0) {
                throw new \Exception("No rows updated for room_id: $roomId - Room may not exist or data unchanged");
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Review add failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve reviews for a specific room
     */
    public function getReviewsByRoom($roomId)
    {
        $parameters = [':room_id' => $roomId];
        
        return $this->fetchAll(
            'SELECT review.*, user.name AS user_name 
            FROM review 
            INNER JOIN user ON review.user_id = user.user_id 
            WHERE room_id = :room_id 
            ORDER BY created_time ASC', 
            $parameters
        );
    }
}