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
            'title' => ['type' => 'string', 'required' => true],
            'thumbnail' => ['type' => 'string'],
            'type' => ['type' => 'string' ],
            'duration' => ['type' => 'string' ],
            'description' => ['type' => 'text' ],
            'transfer' => ['type' => 'text' ],
            'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ]
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'images' => $mapper->hasMany($entity, 'Entity\TourImage', 'id_tour'),

        ];
    
    }

    

    
}

?>