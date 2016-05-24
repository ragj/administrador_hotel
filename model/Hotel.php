<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;
    /**
     *  Model for Hotel
     */
    class Hotel extends \Spot\Entity {

        protected static $table = 'hotel';

        public static function fields() {
            return [
                'idhotel' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'name' => ['type' => 'string', 'required' => true],
                'thumbnail' => ['type' => 'string'],
                'description' => ['type' => 'text'],
                'description_esp' => ['type' => 'text'],
                'address' => ['type' => 'string' ],
                'website' => ['type' => 'string' ],
                'map' => ['type' => 'text' ],
                'tel' => ['type' => 'string' ],
                'email' => ['type' => 'string' ],
                'uri' => ['type' => 'text' ],
                'uri_es' => ['type' => 'text' ],
                'oculto'=>['type'=>'boolean','value' => false],
                'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ],
                'zona_idzona'=> ['type' => 'integer', 'required' => true]
            ];
        }

        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'images' => $mapper->hasMany($entity, 'Entity\HotelImage', 'hotel_idhotel'),
                'zona' => $mapper->belongsTo($entity, 'Entity\Zona', 'zona_idzona')
            ];
        }

        public static function events(EventEmitter $eventEmitter)
        {
            $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
                $entity->uri = self::normalize( str_replace( " " , "-" , $entity->name ) );
                $entity->uri_es = self::normalize( str_replace( " " , "-" , $entity->name ) );
            });
            $eventEmitter->on('beforeUpdate', function (Entity $entity, Mapper $mapper) {
                $entity->uri = self::normalize( str_replace( " " , "-" , $entity->name ) );
                $entity->uri_es = self::normalize( str_replace( " " , "-" , $entity->name ) );
            });
        }
        static function normalize ($string){
            $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
            $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
            return utf8_encode(strtolower(strtr(utf8_decode($string), utf8_decode($a), $b) ));
        }
    }

?>