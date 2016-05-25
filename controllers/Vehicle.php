<?php
	class Vehicle extends Luna\Controller {
		/**
    	*	Metodo que obtiene todos los registros de la tabla vehicle.
    	**/
    	public function show($req,$res){
	    	$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    	$vehicle=$vehicleMapper->select();
	    	echo $this->renderWiew(array_merge(["vehicle" => $vehicle]),$res);
	    }
	    /**
	    *	Metodo que agrega un vehiculo
	    **/
	    public function add($req,$res){
	    	$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    	if(isset($req->data["car"])){
	    		$vehicle=$vehicleMapper->build([
	    			'name'=>$req->data["car"]
	    		]);
	    		$vehicleMapper->insert($vehicle);
	    	}
	    	echo $this->renderWiew([],$res);
	    }
	    /**
	    *	Metodo que edita un automovil
	    **/
	    public function edit($req,$res){
	    	$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    	if(isset($req->params["car"])){
	    		$vehicle=$vehicleMapper->select()->where(["idVehicle"=>$req->params["car"]])->first();
	    		if(isset($req->data["car"])){
	    			$vehicle->name=$req->data["car"];
	    			$vehicleMapper->update($vehicle);
	    		}
	    	}
	    	echo $this->renderWiew(array_merge(["car" => $vehicle]),$res);
	    }
	    /**
	    *	Metodo que elimina un carro
	    **/
	    public function delete($req,$res){
	    	if(isset($req->params["car"])){
	    		$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    		//Eliminamos el vehiculo que este registrado para ese id
        		$vehicle = $vehicleMapper->delete(['idVehicle ='=>(integer)$req->params["car"]]); 
        		header("Location: /panel/vehicles/show");
        		exit;
	    	}
	    }
	}
?>