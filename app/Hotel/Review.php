<?php
namespace Hotel;

use PDO;
use Exception;

class Review extends BaseService 
{
    /**
     * Submit a review for a specific room
     * 
     * @param int $roomId The ID of the room being reviewed
     * @param int $userId The ID of the user submitting the review
     * @param int $rating The rating given by the user (1-5)
     * @return bool Whether the review was successfully submitted
     */
    public function submitReview($roomId, $userId, $rating) 
    {
        // Validate inputs
        if (!is_numeric($roomId) || !is_numeric($userId) || 
            !is_numeric($rating) || $rating < 1 || $rating > 5) {
            return false;
        }

        try {
            // Start a transaction for consistent updates
            $pdo = $this->getPdo();
            $pdo->beginTransaction();

            // Insert the new review
            $insertSql = "INSERT INTO reviews (room_id, user_id, rating, created_at) 
                          VALUES (:room_id, :user_id, :rating, NOW())";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                ':room_id' => $roomId,
                ':user_id' => $userId,
                ':rating' => $rating
            ]);

            // Update room's average reviews and review count
            $updateSql = "UPDATE room 
                          SET avg_reviews = (
                              SELECT AVG(rating) 
                              FROM reviews 
                              WHERE room_id = :room_id
                          ),
                          count_reviews = (
                              SELECT COUNT(*) 
                              FROM reviews 
                              WHERE room_id = :room_id
                          )
                          WHERE room_id = :room_id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([':room_id' => $roomId]);

            // Commit the transaction
            $pdo->commit();

            return true;
        } catch (Exception $e) {
            // Rollback in case of error
            $pdo->rollBack();
            error_log("Review submission error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has already reviewed this room
     * 
     * @param int $roomId The ID of the room
     * @param int $userId The ID of the user
     * @return bool Whether the user has already reviewed the room
     */
    public function hasUserReviewedRoom($roomId, $userId)
    {
        $sql = "SELECT COUNT(*) as review_count 
                FROM reviews 
                WHERE room_id = :room_id AND user_id = :user_id";
        
        $parameters = [
            ':room_id' => $roomId,
            ':user_id' => $userId
        ];

        $result = $this->fetch($sql, $parameters);
        
        return $result['review_count'] > 0;
    }
}