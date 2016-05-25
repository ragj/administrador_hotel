<?php
	class Vehicle extends Luna\Controller {
		/**
    	*	Funcion que obtiene todos los registros de la tabla vehicle.
    	**/
    	public function show($req,$res){
	    	$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    	$vehicle=$vehicleMapper->select();
	    	echo $this->renderWiew(array_merge(["vehicle" => $vehicle]),$res);
	    }
	}
?>