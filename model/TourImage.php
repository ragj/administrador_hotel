<?php

//  EJEMPLO

namespace Entity;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;

/**
 *  Model for Viaje
 *  
 */
class TourImage extends \Spot\Entity {

    protected static $table = 'tour_image';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'id_tour' => ['type' => 'integer', 'required' => true],
            'url' => ['type' => 'string' ]
        ];
    }

    

    
}

?>