<?php
    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for TransferDetail
     */
    class TransferDetail extends \Spot\Entity {

        protected static $table = 'transferDetail';

        public static function fields() {
            return [
                'idtransferDetail' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'description' => ['type' => 'string','required'=>true],
                'description_esp' => ['type' => 'string','required'=>true],
                'transferBlock_idtransferBlock' => ['type' => 'integer','required'=>true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'transferBlock' => $mapper->belongsTo($entity, 'Entity\transferBlock', 'transferBlock_idtransferBlock'),
                'transferValue' => $mapper->hasMany($entity, 'Entity\TransferValue', 'transferDetail_idtransferDetail')
            ];
        }

    }

?>