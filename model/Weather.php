<?php
	namespace Entity;
	use Spot\EntityInterface as Entity;
	use Spot\MapperInterface as Mapper;
	/**
	 *  Model for Weather
	 */
	class Weather extends \Spot\Entity {
	    protected static $table = 'weather';
	    public static function fields() {
	        return [
	            'id' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
	            'data' => ['type' => 'text', 'required' => true],
	            'updated' => ['type' => 'datetime', 'required' => true],
	            'zona_idzona'      => ['type' => 'integer', 'required' => true]
	        ];
	    }
	    public static function relations(Mapper $mapper, Entity $entity)
	    {
	        return ['zona' => $mapper->belongsTo($entity, 'Entity\Zona', 'zona_idzona')];
	    }    
	}

?>