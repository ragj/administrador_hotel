<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    /**
     *  Model for Type
     */
    class Type extends \Spot\Entity {
        protected static $table = 'type';
        public static function fields() {
            return [
                'idtype' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'type' => ['type' => 'string', 'required' => true]
            ];
        }     
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'experiences' => $mapper->hasMany($entity, 'Entity\Experience', 'type_idtype')
            ];
        }
    }

?>