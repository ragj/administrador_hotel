<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;

    /**
     *  Model for Contacto
     */
    class Texperience extends \Spot\Entity {

        protected static $table = 'texperience';

        public static function fields() {
            return [
                'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'title' => ['type' => 'text'],
                'title_esp' => ['type' => 'text'],
                'content' => ['type' => 'text'],
                'content_esp' => ['type' => 'text']
            ];
        }
    }

?>