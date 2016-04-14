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
            'creatat' => ['type' => 'datetime', 'required' => false],
            'userid'=>['type'=>'integer','required'=>true],
            'email' => ['type' => 'string', 'required' => true],
            'uid' => ['type' => 'string', 'required' => true],
            'usado' => ['type' => 'boolean', 'required' => false],
        ];
    }

    public static function events(EventEmitter $eventEmitter) {
        $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
            $entity->creatat = new \DateTime();
            $entity->uid = md5($entity->email." ".$entity->creatat->format('Y-m-d H:i:s')." ".$entity->userid);
            $entity->usado = false;
        });
    }
}
?>