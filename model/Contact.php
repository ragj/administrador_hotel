<?php

//  EJEMPLO

    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     *  TODO: Make it work
     */
    class Contact extends \Spot\Entity {

        protected static $table = 'Contact';

        public static function fields() {
            return [
                'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'nombre' => ['type' => 'string', 'required' => true],
                'email' => ['type' => 'string','requiered'=>true],
                'mensaje' => ['type' => 'string','requiered'=>true ],
                'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ]
            ];
        }
    }

?>