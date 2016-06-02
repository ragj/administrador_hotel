<?php
    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;

    /**
     *  Model for Users
     */
    class Users extends \Spot\Entity {

        protected static $table = 'users';

        public static function fields() {
            return [
                'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'nombre' => ['type' => 'string', 'required' => false],
                'papellido' => ['type' => 'string', 'required' => false],
                'mapellido' => ['type' => 'string', 'required' => false],
                'usuario' => ['type' => 'string', 'required' => false],
                'password' => ['type' => 'string', 'required' => false],
                'telefono' => ['type' => 'string', 'required' => false],
                'iata' => ['type' => 'string', 'required' => false],
                'miembros' => ['type' => 'string', 'required' => false],
                'years' => ['type' => 'string', 'required' => false],
                'activo' => ['type' => 'boolean', 'required' => false ,'value'=>0],
                'create_at' => ['type' => 'datetime', 'required' => false],
                'rols_idrols'=> ['type' => 'integer', 'required' => false]
            ];
        }
        
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'rols' => $mapper->belongsTo($entity, 'Entity\Rols', 'rols_idrols'),
                'zonas' =>$mapper->hasManyThrough($entity, 'Entity\Zona', 'Entity\UsersZona', 'zona_idzona', 'users_id'),
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