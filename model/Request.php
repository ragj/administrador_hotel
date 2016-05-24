<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;
    /**
     *  Model for Hotel
     */
    class Request extends \Spot\Entity {

        protected static $table = 'request';

        public static function fields() {
            return [
                'idrequest' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'comment' => ['type' => 'text'],
                'users_usersid'=>['type' => 'integer', 'required' => true],
                'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ],
            ];
        }

        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'user' => $mapper->BelongsTo($entity, 'Entity\Users', 'users_usersid'),
                'rhotels' => $mapper->hasMany($entity, 'Entity\RequestHotel', 'request_idrequest'),
                'rexperience' => $mapper->hasMany($entity, 'Entity\RequestExperience', 'request_idrequest'),
                'rtransfer' => $mapper->hasMany($entity, 'Entity\RequestTransferD', 'request_idrequest')
            ];
        }
    }

?>