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
	            'content_spa' => ['type' => 'text'],
	            'uri'=>['type' => 'text' ],
	            'uri_es'=>['type' => 'text' ],
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
            	$aux=$spot;
            	$hotelMapper=$aux->mapper("Entity\Hotel");
            	$hotel=$hotelMapper->select()->where(["idhotel"=>$entity->hotel_idhotel])->first();
                $entity->uri = $hotel->uri;
                $entity->uri_es=$hotel->uri_es;
                $entity->content_spa=$entity->content;
            });
        }
	}
?>