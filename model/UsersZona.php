<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Users/Zona
     */
    class UsersZona extends \Spot\Entity {

        protected static $table = 'users_zona';

        public static function fields() {
            return [
                'users_id' => ['type' => 'integer', 'primary' => true],
                'zona_idzona' => ['type' => 'integer', 'primary' => true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'users' => $mapper->belongsTo($entity, 'Entity\Users', 'users_id'),
                'zonas' => $mapper->belongsTo($entity, 'Entity\Zona', 'zona_idzona')
            ];
        }

    }

?>