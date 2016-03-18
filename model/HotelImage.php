<?php

//  EJEMPLO

namespace Entity;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;

/**
 *  Model for Viaje
 *  
 */
class HotelImage extends \Spot\Entity {

    protected static $table = 'hotel_image';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'id_hotel' => ['type' => 'integer', 'required' => true],
            'url' => ['type' => 'string' ]
        ];
    }

    

    
}

?>