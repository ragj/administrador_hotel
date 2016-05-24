<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     */
    class Vehicle extends \Spot\Entity {

        protected static $table = 'Vehicle';

        public static function fields() {
            return [
                'idVehicle' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'name' => ['type' => 'string', 'required' => true],
            ];
        }
    }

?>