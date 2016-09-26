<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Rols
     */
    class Rols extends \Spot\Entity {

        protected static $table = 'rols';
        public static function fields() {
            return [
                'idrols' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'rol' => ['type' => 'string', 'required' => true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'users' => $mapper->hasMany($entity, 'Entity\Users', 'rols_idrols')
            ];
        }
    }

?>