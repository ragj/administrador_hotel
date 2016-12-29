<?php

	namespace Entity;

	use Spot\EntityInterface as Entity;
	use Spot\MapperInterface as Mapper;

	/**
	 *  Model for Hotel Image  
	 */
	class VehiclePassengers extends \Spot\Entity {

	    protected static $table = 'vehiclePassengers';

	    public static function fields() {
	        return [
	            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
	            'np_initial' => ['type' => 'integer' ],
	            'np_final' => ['type' => 'integer' ],
	            'vehicle_idVehicle' => ['type' => 'integer', 'required' => true]
	        ];
	    }
	    public static function relations(Mapper $mapper, Entity $entity)
	    {
	        return ['vehicle' => $mapper->belongsTo($entity, 'Entity\Vehicle', 'vehicle_idVehicle')];
	    }
	}
?>