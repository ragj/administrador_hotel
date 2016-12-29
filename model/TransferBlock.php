<?php
    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for TransferBlock
     */
    class TransferBlock extends \Spot\Entity {
        protected static $table = 'transferBlock';
        public static function fields() {
            return [
                'idtransferBlock' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'TransferBlockTitle' => ['type' => 'string'],
                'TransferBlockTitle_es' => ['type' => 'string'],
                'tipo' => ['type' => 'string'],
                'zona_idzona'      => ['type' => 'integer', 'required' => true]
            ];
        }
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'detail' => $mapper->hasMany($entity, 'Entity\TransferDetail', 'transferBlock_idtransferBlock'),
                'zona' => $mapper->belongsTo($entity, 'Entity\Zona', 'zona_idzona')
            ];
        }

    }

?>