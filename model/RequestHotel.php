<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;
    /**
     *  Model for Hotel
     */
    class RequestHotel extends \Spot\Entity {

        protected static $table = 'requestHotel';

        public static function fields() {
            return [
                'idrequestHotel' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'arrival_date'=>['type' => 'datetime', 'required' => true ],
                'departure_date'=>['type' => 'datetime', 'required' => true],
                'request_idrequest'=> ['type' => 'integer', 'required' => true],
                'hotel_idhotel'=> ['type' => 'integer', 'required' => true]
            ];
        }

        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'hotel' => $mapper->belongsTo($entity, 'Entity\Hotel', 'hotel_idhotel')->execute(),
                'request' => $mapper->belongsTo($entity, 'Entity\Request', 'request_idrequest')
            ];
        }
        public static function events(EventEmitter $eventEmitter)
        {   
           $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
               if (is_string( $entity->arrival_date )) {
                   $entity->arrival_date = new \DateTime( $entity->arrival_date );
               }
               if (is_string( $entity->departure_date )) {
                   $entity->departure_date = new \DateTime( $entity->departure_date );
               }
           });
        }
    }

?>