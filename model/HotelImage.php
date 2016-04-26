<?php

	namespace Entity;

	use Spot\EntityInterface as Entity;
	use Spot\MapperInterface as Mapper;

	/**
	 *  Model for Hotel Image  
	 */
	class HotelImage extends \Spot\Entity {

	    protected static $table = 'hotelImages';

	    public static function fields() {
	        return [
	            'idhotelImages' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
	            'path' => ['type' => 'string' ],
	            'tipo' => ['type' => 'string'],
	            'hotel_idhotel' => ['type' => 'integer', 'required' => true]
	        ];
	    }
	    public static function relations(Mapper $mapper, Entity $entity)
	    {
	        return ['hotel' => $mapper->belongsTo($entity, 'Entity\Hotel', 'hotel_idhotel')];
	    }
	}
?>