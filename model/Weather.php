<?php

//  EJEMPLO

namespace Entity;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;

/**
 *  Model for Viaje
 *  
 */
class Weather extends \Spot\Entity {

    protected static $table = 'weather';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'data' => ['type' => 'text', 'required' => true],
            'updated' => ['type' => 'datetime', 'required' => true]
        ];
    }

    

    
}

?>