<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     */
    class Visits extends \Spot\Entity {

        protected static $table = 'visits';

        public static function fields() {
            return [
                'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'userid' => ['type' => 'integer', 'required' => true],
                'slug' => ['type' => 'string',],
                'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ],
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return ['user' => $mapper->belongsTo($entity, 'Entity\Users', 'userid')];
        }
    }

?>