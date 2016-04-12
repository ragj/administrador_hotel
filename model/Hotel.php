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
class Hotel extends \Spot\Entity {

    protected static $table = 'hotel';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'name' => ['type' => 'string', 'required' => true],
            'thumbnail' => ['type' => 'string'],
            'description' => ['type' => 'text'],
            'description_esp' => ['type' => 'text'],
            'address' => ['type' => 'string' ],
            'website' => ['type' => 'string' ],
            'map' => ['type' => 'text' ],
            'uri' => ['type' => 'text' ],
            'tel' => ['type' => 'string' ],
            'email' => ['type' => 'string' ],
            'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ]
        ];
    }

    public static function events(EventEmitter $eventEmitter)
    {
        $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
            $entity->uri = self::normalize( str_replace( " " , "-" , $entity->name ) );
        });
        $eventEmitter->on('beforeUpdate', function (Entity $entity, Mapper $mapper) {
            $entity->uri = self::normalize( str_replace( " " , "-" , $entity->name ) );
        });
    }


    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'images' => $mapper->hasMany($entity, 'Entity\HotelImage', 'id_hotel'),
        ];
    
    }


    static function normalize ($string){
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        return utf8_encode(strtolower(strtr(utf8_decode($string), utf8_decode($a), $b) ));
    }

    

    
}

?>