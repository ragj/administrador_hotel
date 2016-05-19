<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     */
    class Contact extends \Spot\Entity {

        protected static $table = 'Contact';

        public static function fields() {
            return [
                'idContact' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'nombre' => ['type' => 'string', 'required' => true],
                'email' => ['type' => 'string','requiered'=>true],
                'mensaje' => ['type' => 'string','requiered'=>true ],
                'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ],
                'zona_idzona'      => ['type' => 'integer', 'required' => true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return ['zona' => $mapper->belongsTo($entity, 'Entity\Zona', 'zona_idzona')];
        }
    }

?>