<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;
    /**
     *  Model for Hotel
     */
    class RequestExperience extends \Spot\Entity {

        protected static $table = 'requestExperience';

        public static function fields() {
            return [
                'idrequestExperience' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'wanted_date'=>['type' => 'datetime', 'required' => true ],
                'peaple'=> ['type' => 'integer', 'required' => true],
                'request_idrequest'=> ['type' => 'integer', 'required' => true],
                'experience_idexperience'=> ['type' => 'integer', 'required' => true],
                
            ];
        }

        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                //'images' => $mapper->hasMany($entity, 'Entity\HotelImage', 'hotel_idhotel'),
                'request' => $mapper->belongsTo($entity, 'Entity\Request', 'request_idrequest'),
                'experience' => $mapper->belongsTo($entity, 'Entity\Experience', 'experience_idexperience')->execute()
            ];
        }
        public static function events(EventEmitter $eventEmitter)
        {   
           $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
               if (is_string( $entity->wanted_date )) {
                   $entity->wanted_date = new \DateTime( $entity->wanted_date );
               }
           });
        }
    }

?>