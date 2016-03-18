<?php

//  EJEMPLO

namespace Entity;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;

/**
 *  Model for Viaje
 *  
 */
class Hotel extends \Spot\Entity {

    protected static $table = 'hotel';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'name' => ['type' => 'string', 'required' => true],
            'thumbnail' => ['type' => 'string'],
            'description' => ['type' => 'text'],
            'address' => ['type' => 'string' ],
            'website' => ['type' => 'string' ],
            'map' => ['type' => 'text' ],
            'tel' => ['type' => 'string' ],
            'email' => ['type' => 'string' ],
            'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ]
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'images' => $mapper->hasMany($entity, 'Entity\HotelImage', 'id_hotel'),
        ];
    
    }

    

    
}

?>