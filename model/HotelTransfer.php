<?php

	namespace Entity;

	use Spot\EntityInterface as Entity;
	use Spot\MapperInterface as Mapper;
	use Spot\EventEmitter as EventEmitter;

	/**
	 *  Model for Hotel Image  
	 */
	class HotelTransfer extends \Spot\Entity {

	    protected static $table = 'hotelTransfer';

	    public static function fields() {
	        return [
	            'idhotelTransfer' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
	            'content' => ['type' => 'text' ],
	            'uri'=>['type' => 'text' ],
	            'hotel_idhotel' => ['type' => 'integer', 'required' => true]
	        ];
	    }
	    public static function relations(Mapper $mapper, Entity $entity)
	    {
	        return ['hotel' => $mapper->belongsTo($entity, 'Entity\Hotel', 'hotel_idhotel')];
	    }
	    public static function events(EventEmitter $eventEmitter)
        {
            $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
            	global $spot;
            	$hotelMapper=$spot->mapper("Entity\Hotel");
            	$hotel=$hotelMapper->select()->where(["idhotel"=>$entity->hotel_idhotel])->first();
                $entity->uri = $hotel->uri;
            });
            $eventEmitter->on('beforeUpdate', function (Entity $entity, Mapper $mapper) {
            	global $spot;
                $hotelMapper=$spot->mapper("Entity\Hotel");
            	$hotel=$hotelMapper->select()->where(["idhotel"=>$entity->hotel_idhotel])->first();
                $entity->uri = $hotel->uri;
            });
        }
	}
?>