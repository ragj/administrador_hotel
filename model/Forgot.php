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
class Forgot extends \Spot\Entity {

    protected static $table = 'forgot';

    public static function fields() {
        return [
            'forgotid' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'createdat' => ['type' => 'datetime', 'required' => false],
            'email' => ['type' => 'string', 'required' => true],
            'uri' => ['type' => 'string', 'required' => true],
            'usado' => ['type' => 'boolean', 'required' => false],
            'userid'=>['type'=>'integer','required'=>true]
        ];
    }
    public static function relations(Mapper $mapper, Entity $entity)
    {
        return ['usuario' => $mapper->belongsTo($entity, 'Entity\Users', 'userid')];
    } 

    public static function events(EventEmitter $eventEmitter) {
        $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
            $entity->creatat = new \DateTime();
            $entity->uid = md5($entity->createdat->format('Y-m-d H:i:s')." ".$entity->userid);
            $entity->usado = false;
        });
    }
}
?>