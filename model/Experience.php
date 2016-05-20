<?php
    namespace Entity;
    use Spot\EntityInterface as Entity;
    use Spot\MapperInterface as Mapper;
    use Spot\EventEmitter as EventEmitter;
    /**
     *  Model for Experience
     */
    class Experience extends \Spot\Entity {

        protected static $table = 'experience';

        public static function fields() {
            return [
                'idexperience' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
                'title' => ['type' => 'string','required' => true],
                'thumbnail' => ['type' => 'string'],
                'duration' => ['type' => 'string','required' => true],
                'duration_esp' => ['type' => 'string','required' => true],
                'description' => ['type' => 'text','required' => true],
                'description_esp' => ['type' => 'text','required' => true],
                'uri' => ['type' => 'text','required' => true],
                'uri_es' => ['type' => 'text','required' => true],
                'transfer' => ['type' => 'text' ],
                'transfer_esp' => ['type' => 'text' ],
                'home' => ['type' => 'boolean' ],
                'created' => ['type' => 'datetime', 'required' => true , "value" => new \DateTime() ],
                'zona_idzona'=> ['type' => 'integer', 'required' => true],
                'type_idtype'=> ['type' => 'integer', 'required' => true],
                'hotel_idhotel'=> ['type' => 'integer']
            ];
        }
        
        public static function relations(Mapper $mapper, Entity $entity)
        {
            return [
                'images' => $mapper->hasMany($entity, 'Entity\ExperienceImage', 'experience_idexperience'),
                'zona' => $mapper->belongsTo($entity, 'Entity\Zona', 'zona_idzona'),
                'type' => $mapper->belongsTo($entity, 'Entity\Type', 'type_idtype')
            ];
        }
        
        public static function events(EventEmitter $eventEmitter)
        {
            $eventEmitter->on('beforeInsert', function (Entity $entity, Mapper $mapper) {
                $entity->uri = self::normalize( str_replace( " " , "-" , $entity->title ) );
                $entity->uri_es = self::normalize( str_replace( " " , "-" , $entity->title ) );
            });
            // $eventEmitter->on('beforeUpdate', function (Entity $entity, Mapper $mapper) {
            //     $entity->uri = self::normalize( str_replace( " " , "-" , $entity->title ) );
            //     $entity->uri_es = self::normalize( str_replace( " " , "-" , $entity->title ) );
            // });
        }

        static function normalize ($string){
            $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
            $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
            return utf8_encode(strtolower(strtr(utf8_decode($string), utf8_decode($a), $b) ));
        }
    }

?>