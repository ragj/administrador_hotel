<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;
    /**
     *  Model for Hotel
     */
    class RequestTransferD extends \Spot\Entity {

        protected static $table = 'requestTransferD';

        public static function fields() {
            return [
                'idrequestTransferD' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'wanted_date'=>['type' => 'datetime', 'required' => true ],
                'peaple'=> ['type' => 'integer', 'required' => true],
                'request_idrequest'=> ['type' => 'integer', 'required' => true],
                'transferDetail_idtransferDetail'=> ['type' => 'integer', 'required' => true],
                'vehicle_idvehicle'=>['type' => 'integer', 'required' => true],

            ];
        }

        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'request' => $mapper->belongsTo($entity, 'Entity\Request', 'request_idrequest'),
                'transferDetail' => $mapper->belongsTo($entity, 'Entity\TransferDetail', 'transferDetail_idtransferDetail')->execute(),
                'vehicle' => $mapper->belongsTo($entity, 'Entity\Vehicle', 'vehicle_idvehicle')->execute()
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