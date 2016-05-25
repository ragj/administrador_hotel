<?php

	namespace Entity;

	use Spot\EntityInterface as Entity;
	use Spot\MapperInterface as Mapper;

	/**
	 *  Model for Hotel Image  
	 */
	class VehicleImage extends \Spot\Entity {

	    protected static $table = 'vehicleImages';

	    public static function fields() {
	        return [
	            'idvehicleImages' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
	            'path' => ['type' => 'string' ],
	            'vehicle_idVehicle' => ['type' => 'integer', 'required' => true]
	        ];
	    }
	    public static function relations(Mapper $mapper, Entity $entity)
	    {
	        return ['vehicle' => $mapper->belongsTo($entity, 'Entity\Vehicle', 'vehicle_idVehicle')];
	    }
	}
?>