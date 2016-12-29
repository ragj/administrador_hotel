<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    /**
     *  Model for Footer
     */
    class Footer extends \Spot\Entity {
        protected static $table = 'footer';

        public static function fields() {
            return [
                'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'content' => ['type' => 'text'],
                'content_esp' => ['type' => 'text'],
            ];
        }
    }

?>