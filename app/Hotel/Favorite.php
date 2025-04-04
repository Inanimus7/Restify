<?php

namespace Hotel;

use Hotel\User;
use Hotel\BaseService;

class Favorite extends BaseService
{
    /**
     * Retrieve a list of favorite rooms for a given user
     */
    public function getListByUser($userId)
    {
        $parameters = [
            ':user_id' => $userId,
        ];
        
        return $this->fetchAll(
            'SELECT * FROM favorite 
            INNER JOIN room ON favorite.room_id = room.room_id 
            WHERE user_id = :user_id', 
            $parameters
        );
    }

    /**
     * Check if a room is a favorite for a given user
     */
    public function isFavorite($roomId, $userId)
    {
        $parameters = [
            ':room_id' => $roomId,
            ':user_id' => $userId,
        ];
        
        $favorite = $this->fetch(
            'SELECT * FROM favorite WHERE room_id = :room_id AND user_id = :user_id', 
            $parameters
        );
        
        return !empty($favorite);
    }

    /**
     * Add a room to the user's favorites
     */
    public function addFavorite($roomId, $userId)
    {
        $parameters = [
            ':room_id' => $roomId,
            ':user_id' => $userId,
        ];
        
        $rows = $this->execute(
            'INSERT IGNORE INTO favorite (room_id, user_id) VALUES (:room_id, :user_id)',
            $parameters
        );
        
        return $rows == 1;
    }
    
    /**
     * Remove a room from the user's favorites
     */
    public function removeFavorite($roomId, $userId)
    {
        $parameters = [
            ':room_id' => $roomId,
            ':user_id' => $userId,
        ];
        
        $rows = $this->execute(
            'DELETE FROM favorite WHERE room_id = :room_id AND user_id = :user_id',
            $parameters
        );
        
        return $rows == 1;
    }
}