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
	    	if(isset($req->data["car"],$req->data["description"],$req->data["spanish"])){
	    		$vehicle=$vehicleMapper->build([
	    			'name'=>$req->data["car"],
	    			'description'=>$req->data["description"],
	    			'description_esp'=>$req->data["spanish"]
	    		]);
	    		$vehicleMapper->insert($vehicle);
	    		header("Location:/bali/panel/vehicles/edit/".$vehicle->idVehicle);
	    		exit;
	    	}
	    	echo $this->renderWiew([],$res);
	    }
	    /**
	    *	Metodo que edita un automovil
	    **/
	    public function edit($req,$res){
	    	$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    	if(isset($req->params["car"])){
	    		$vehicle=$vehicleMapper->select()->where(["idVehicle"=>$req->params["car"]])->with("images")->with("passengers")->first();

	    		if(isset($req->data["car"],$req->data["description"],$req->data["spanish"])){
	    			$vehicle->name=$req->data["car"];
	    			$vehicle->description=$req->data["description"];
	    			$vehicle->description_esp=$req->data["spanish"];
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
        		header("Location: /bali/panel/vehicles/show");
        		exit;
	    	}
	    }
	    /**
	    *	Metodo que sirve para agregar una imagen
	    **/
	    public function addImages($req,$res){
	    	$vehicleMapper=$this->spot->mapper("Entity\Vehicle");
	    	$vehicle=$vehicleMapper->select();
	    	if(isset($req->data["car"],$_FILES['imagen']['name'])){

	    		$permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
	    		$dir="./assets/img/car/"; 
	    		if (file_exists($dir.$req->data["car"])==false) {
                     mkdir($dir.$req->data["car"]);
            	}
	    		if(!($_FILES['imagen']['error']>0)){
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['imagen']['type'], $permitidos)){
                        $aux2=explode('/',$_FILES['imagen']['type']);
                        $aux=basename($_FILES['imagen']['name'],".".$aux2[1]);
                        $file=$aux.substr(uniqid(),0,-3).".".$aux2[1];
                        $ruta=$dir.$req->data["car"]."/".$file;
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
                            if($resultado){
                                //Guardamos la experiencia en la base de datos
                                $hotelImageMapper=$this->spot->mapper("Entity\VehicleImage");
                                $entity = $hotelImageMapper->build([
                                    'vehicle_idVehicle' =>$req->data["car"],
                                    'path' =>$file
                                ]);
                                $result = $hotelImageMapper->insert($entity);
                                echo "<script> alert'Image Uploaded.');</script>";
                            }
                        }
                    }
                }
	    	}

	    	echo $this->renderWiew(array_merge(["car" => $vehicle]),$res);
	    }
	    /**
    *   Metodo que sirve para editar una imagen de un hotel
    **/
    public function editImages($req,$res){
        if($req->params["car"]!=null){
            //obtener imagen mediante el id
            $vehicleImageMapper=$this->spot->mapper("Entity\VehicleImage");
            $vehicleImage= $vehicleImageMapper->select()->where(["idvehicleImages" => $req->params["car"]])->first(); 
            $imagene="/bali/assets/img/car/".$vehicleImage->vehicle_idVehicle."/";
        }
        if(isset($_FILES['imagen']['name'])){
            $imagen="";
            //establecemos el formato en que se almacena la url en la base de datos
             if(strcmp($_FILES['imagen']['name'], $vehicleImage->path)!==0 && $_FILES['imagen']['name']!=null)
            {
                //establecemos el directorio con el cual trabajaremos
                $dir="./assets/img/car/";
                $aux2=explode('/',$_FILES['imagen']['type']);
                $aux=basename($_FILES['imagen']['name'],".".$aux2[1]);
                $file=$aux.substr(uniqid(),0,-3).".".$aux2[1];
                $ruta=$dir.$vehicleImage->vehicle_idVehicle."/".$file;
                //array con tipos de archivos 
                $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
                //Obtenemos y sanitizamos los parametros obtenidos por el metodo  post.
                //validamos si se subio la imagen
                if(!($_FILES['imagen']['error']>0))
                {
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['imagen']['type'], $permitidos)){
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
                            if($resultado){
                                //eliminamos fichero anterior
                                @unlink($dir.$vehicleImage->vehicle_idVehicle."/".$vehicleImage->path);
                                //obtenemos la ruta del archivo
                                $imagen=$file;                  
                            }
                            else{ echo "<div class=error><p>There was a problem uploading the image.</p></div>";}
                        }
                        else{ echo "<div class=error><p>An image already exists with that name.</p></div>";}
                    }
                    else{ echo "<div class=error><p>The file type is not allowed.</p></div>";}
                }
                else{ echo "<div class=error><p>The image was not uploaded.</p></div>";}
            }
            else{
                $imagen=$vehicleImage->path;
            }
            //actualizamos atributo de la entidad
            $vehicleImage->path = $imagen;
            //actualizamos la entidad
            $vehicleImageMapper->update($vehicleImage);
        }
        echo $this->renderWiew(array_merge(["image"=>$vehicleImage,"thumb"=>$imagene]),$res);
    }

	    /**
	    *   Metodo que sirve para eliminar una imagen relacionada a un hotel
	    **/
	    public function deleteImages($req,$res)
	    {
	        //Obtenemos el id, de la experiencia a eleminar
	        $var=$req->params["car"];
	        //Establecemos a spot con que entity class vamos a trabajar
	        $vehicleImageMapper=$this->spot->mapper("Entity\VehicleImage");
	        //Seleccionamos la experiencia que este registrado para ese ide
	        $vehicleImage = $vehicleImageMapper->select()->where(["idvehicleImages" =>$var])->first();
	        $ruta="./assets/img/car/".$vehicleImage->vehicle_idVehicle."/".$vehicleImage->path;
	        //Eliminamos el registro del id seleccionado
	        $id=$vehicleImage->vehicle_idVehicle;
	        $vehicleImage = $vehicleImageMapper->delete(['idvehicleImages'=>(integer)$var]);
	        //Establecemos a spot con que entity class vamos a trabajar
	        @unlink($ruta);
	        header("Location:/bali/panel/vehicles/edit/".$id);
	        exit;
	    }

	    /***
	    *	Metodo que sirve para agregar detalle de numero de pasajeros a coche
	    *
	    **/
	    public function addPass($req,$res){
	    	if(isset($req->params["car"],$req->data["min"])){
	    		$passengerMapper=$this->spot->mapper("Entity\VehiclePassengers");
	    		if(isset($req->data["max"]))
	    		{
	    			$passenger=$passengerMapper->build([
		    			"np_initial"=>$req->data["min"],
		    			"np_final"=>$req->data["max"],
		    			"vehicle_idVehicle"=>$req->params["car"]
	    			]);
	    		}
	    		else{
	    			$passenger=$passengerMapper->build([
		    			"np_initial"=>$req->data["min"],
		    			"np_final"=>0,
		    			"vehicle_idVehicle"=>$req->params["car"]
	    			]);
	    		}
	    		$passengerMapper->insert($passenger);
	    		header("Location: /bali/panel/vehicles/edit/".$req->params["car"]);
	    	}
	    }
	    /**
	    *	Metodo que sirve para editar un detalle de numero de pasajero
	    **/
	    public function editPass($req,$res){
	    	if(isset($req->params["car"])){
	    		$passengerMapper=$this->spot->mapper("Entity\VehiclePassengers");	
	    		if(isset($req->data["min"],$req->data["max"])){
	    			$passenger=$passengerMapper->select()->where(["id"=>$req->params["car"]])->with("vehicle")->first();
	    			$passenger->np_initial=$req->data["min"];
	    			$passenger->np_final=$req->data["max"];
	    			$passengerMapper->update($passenger);
	    		}
	    		$passenger=$passengerMapper->select()->where(["id"=>$req->params["car"]])->with("vehicle")->first();
	    		echo $this->renderWiew(array_merge(["passenger"=>$passenger]),$res);
	    	}
	    }
	    /**
	    *	Metodo que elimina un detalle de un numero de pasajero
	    **/
	    public function deletePass($req,$res){
	    	if(isset($req->params["car"])){
	    		$passengerMapper=$this->spot->mapper("Entity\VehiclePassengers");
	    		$passenger=$passengerMapper->select()->where(["id"=>$req->params["car"]])->first();
	    		$aux=$passenger->vehicle_idVehicle;
	    		$passenger = $passengerMapper->delete(['id'=>$req->params["car"]]);
	    		header("Location: /bali/panel/vehicles/edit/".$aux);
	    	}
	    }
	    /**
	    *   Metodo que elimina todos los archivos del directorio y el directorio
	    *   @param $carpeta
	    */
	    private function eliminarFiles($carpeta)
	    {
	        foreach(glob($carpeta . "/*") as $archivos_carpeta)
	        {
	            if (is_dir($archivos_carpeta))
	            {
	                 eliminarDir($archivos_carpeta);
	            }
	            else
	            {
	                @unlink($archivos_carpeta);
	            }
	        }
	    }
	    /**
	    *   Metodo que elemina el directorio
	    *   @param $carpeta
	    */
	    private function deleteDirectory($dir) {
	        system('rm -rf ' . escapeshellarg($dir), $retval);
	        return $retval == 0; // UNIX commands return zero on success
	    }
	}
?>