<?php
    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for TransferBlock
     */
    class TransferValue extends \Spot\Entity {
        protected static $table = 'transferValues';
        public static function fields() {
            return [
                'idtransferValues' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'val' => ['type' => 'decimal','required'=>true],
                'transferDetail_idtransferDetail'=> ['type' => 'integer', 'required' => true],
                'vp_id'=> ['type' => 'integer', 'required' => true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'transferDetail' => $mapper->belongsTo($entity, 'Entity\TransferDetail', 'transferDetail_idtransferDetail'),
                'transferDetail' => $mapper->belongsTo($entity, 'Entity\VehiclePassengers', 'vp_id')
            ];
        }

    }

?>