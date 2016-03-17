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
class Usuario extends \Spot\Entity {

    protected static $table = 'd_usuario';

    public static function fields() {
        return [
            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'nombre' => ['type' => 'string', 'required' => true],
            'papellido' => ['type' => 'string', 'required' => true],
            'usuario' => ['type' => 'string', 'required' => true],
            'password' => ['type' => 'string', 'required' => true],
            'create_at' => ['type' => 'datetime', 'required' => false],
        ];
    }

    
    public static function events(EventEmitter $eventEmitter) {
        $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
            $entity->password = md5($entity->password);
            $entity->create_at = new \DateTime();
        });
        $eventEmitter->on('beforeUpdate', function (Entity $entity, Mapper $mapper) {
            $current_passr = $mapper->first(['id' => $entity->toArray()['id']])->toArray()['password'];
            if ($entity->password != $current_passr) {
                $entity->password = md5($entity->password);
            }
        });
    }

}

?>