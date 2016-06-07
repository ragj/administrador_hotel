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
                'description'=>['type'=>'text'],
                'description_esp'=>['type'=>'text'],     
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'images' => $mapper->hasMany($entity, 'Entity\VehicleImage', 'vehicle_idVehicle'),
                'passengers' => $mapper->hasMany($entity, 'Entity\VehiclePassengers', 'vehicle_idVehicle'),
            ];
        }
    }

?>