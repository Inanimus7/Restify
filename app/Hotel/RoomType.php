<?php

namespace Hotel;

use Hotel\BaseService;

class RoomType extends BaseService
{
    // Function to get all room types and map their IDs to readable names
    public function getRoomTypeName($typeId)
    {
        // Room type mapping array
        $roomTypeMapping = [
            1 => 'Single Room',
            2 => 'Double Room',
            3 => 'Triple Room',
            4 => 'Fourfold Room',
        ];

        // Return the corresponding room type name or 'Not selected' if not found
        return isset($roomTypeMapping[$typeId]) ? $roomTypeMapping[$typeId] : 'Not selected';
    }

    // Function to get all room types from the database
    public function getAllTypes()
    {
        return $this->fetchAll('SELECT * FROM room_type');
    }
}
