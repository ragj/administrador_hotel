<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Users/Zona
     */
    class Zona extends \Spot\Entity {

        protected static $table = 'zona';

        public static function fields() {
            return [
                'idzona' => ['type' => 'integer', 'primary' => true],
                'zona' => ['type' => 'string', 'required' => true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'contacts' => $mapper->hasMany($entity, 'Entity\Contact', 'zona_idzona'),
                'weathers' => $mapper->hasMany($entity, 'Entity\HotelImage', 'zona_idzona'),
                'transferBlocks' => $mapper->hasMany($entity, 'Entity\HotelImage', 'zona_idzona'),
                'experiences' => $mapper->hasMany($entity, 'Entity\HotelImage', 'zona_idzona'),
                'hotels' => $mapper->hasMany($entity, 'Entity\HotelImage', 'zona_idzona'),
                'users' =>$mapper->hasManyThrough($entity, 'Entity\Users', 'Entity\UsersZona', 'users_id', 'zona_idzona')
            ];
        }

    }

?>