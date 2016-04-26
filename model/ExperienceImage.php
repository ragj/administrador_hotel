<?php
	namespace Entity;

	use Spot\EntityInterface as Entity;
	use Spot\MapperInterface as Mapper;

	/**
	 *  Model for ExperienceImage
	 */
	class ExperienceImage extends \Spot\Entity {

	    protected static $table = 'experienceImages';

	    public static function fields() {
	        return [
	            'idexperienceImages' => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
	            'path' => ['type' => 'string','required'=>true],
	            'experience_idexperience' => ['type' => 'integer', 'required' => true]
	        ];
	    }
	    public static function relations(Mapper $mapper, Entity $entity)
	    {
	        return ['experience' => $mapper->belongsTo($entity, 'Entity\Experience', 'experience_idexperience')];
	    }
	}

?>