<?php

//  EJEMPLO

    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     *  TODO: Make it work
     */
    class TransferBlock extends \Spot\Entity {

        protected static $table = 'transfer_block';

        public static function fields() {
            return [
                'idTransferBlock' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'TransferBlockTitle' => ['type' => 'string'],
                'TransferBlockTitle_esp' => ['type' => 'string']
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return ['detail' => $mapper->hasMany($entity, 'Entity\TransferDetail', 'tf_id')];
        }

    }

?>