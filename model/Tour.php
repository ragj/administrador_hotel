<?php

//  EJEMPLO

namespace Entity;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;
use Spot\EventEmitter as EventEmitter;

/**
 *  Model for Viaje
 *  
 */
class Tour extends \Spot\Entity {

    protected static $table = 'tour';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'title' => ['type' => 'string', 'required' => true],
            'thumbnail' => ['type' => 'string'],
            'type' => ['type' => 'string' ],
            'duration' => ['type' => 'string' ],
            'duration_esp' => ['type' => 'string' ],
            'description' => ['type' => 'text' ],
            'description_esp' => ['type' => 'text' ],
            'uri' => ['type' => 'text' ],
            'transfer' => ['type' => 'text' ],
            'transfer_esp' => ['type' => 'text' ],
            'home' => ['type' => 'boolean' ],
            'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ]
        ];
    }

     public static function events(EventEmitter $eventEmitter)
    {
        $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
            $entity->uri = self::normalize( str_replace( " " , "-" , $entity->title ) );
        });
        $eventEmitter->on('beforeUpdate', function (Entity $entity, Mapper $mapper) {
            $entity->uri = self::normalize( str_replace( " " , "-" , $entity->title ) );
        });
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'images' => $mapper->hasMany($entity, 'Entity\TourImage', 'id_tour'),

        ];
    
    }

    static function normalize ($string){
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        return utf8_encode(strtolower(strtr(utf8_decode($string), utf8_decode($a), $b) ));
    }

    

    
}

?>