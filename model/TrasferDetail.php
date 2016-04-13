<?php

//  EJEMPLO

    namespace Entity;

    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     *  TODO: Make it work
     */
    class TransferDetail extends \Spot\Entity {

        protected static $table = 'transfer_detail';

        public static function fields() {
            return [
                'td_id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'td_description' => ['type' => 'string'],
                'td_description_esp' => ['type' => 'string'],
                'td_innova_sub_1' => ['type' => 'decimal'],
                'td_innova_sub_2_4' => ['type' => 'decimal'],
                'td_hiace_val_5_7' => ['type' => 'decimal'],
                'td_hiace_val_8_10' => ['type' => 'decimal'],
                'td_alphard_mercy_1' => ['type' => 'decimal'],
                'td_alphard_mercy_2_4' => ['type' => 'decimal'],
                'tf_id' => ['type' => 'integer']
            ];
        }
    }

?>