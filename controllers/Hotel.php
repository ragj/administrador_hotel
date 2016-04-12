<?php

class Hotel extends Luna\Controller {
    /**
    *   Funcion que registra un objeto de la clase hotel_images
    **/
    public function addImages($req,$res){
        if(isset($req->data["hotel"],$_FILES['imagen']['name'])){
            $dir="./assets/img/hotel/";
            if (file_exists("./assets/img/hotel/".$req->data["hotel"])==false) {
                     mkdir("./assets/img/hotel/".$req->data["hotel"]);
            }
           
            
            //definimos un array de tipos de datos permitidos
            $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
             if(!($_FILES['imagen']['error']>0)){
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['imagen']['type'], $permitidos)){
                        $ruta=$dir."/".$req->data["hotel"]."/".$_FILES['imagen']['name'];
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
                            if($resultado){
                                //Guardamos la experiencia en la base de datos
                                $file=$_FILES['imagen']['name'];
                                $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
                                $entity = $hotelImageMapper->build([
                                    'id_hotel' =>$req->data["hotel"],
                                    'url' =>$file
                                ]);
                                $result = $hotelImageMapper->insert($entity);
                                echo "<div class=exito><p>Image Uploaded.</p></div>";
                            }
                            else{ echo "<div class=error><p>There was a problem uploading the image</p></div>";}
                        }
                        else{ echo "<div class=error><p>An image already exists with that name.</p></div>";}
                    }
                    else{ echo "<div class=error><p>The file type is not allowed.</p></div>";}
                }
                else{ echo "<div class=error><p>The image was not uploaded.</p></div>";}
        }
        $hotelMapper=$this->spot->mapper("Entity\Hotel");
        $hotel=$hotelMapper->select();
        echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *   Funcion que muestra las imagenes por hotel
    **/
      public function showImages($req,$res){
         $hotelMapper = $this->spot->mapper("Entity\Hotel");
         $hotel = $hotelMapper->select()->with("images");
         echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *   Funcion que sirve para editar una imagen de un hotel
    **/
    public function editImages($req,$res){
        if($req->params["hotel"]!=null){
            //obtener imagen mediante el id
            $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
            $hotelImage= $hotelImageMapper->select()->where(["id" => $req->params["hotel"]])->first(); 
            //obtener el tour mediante el id que obtenemos de la imagen
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel=$hotelMapper->select()->where(["id" => $hotelImage->id_hotel])->first();
        }
        if(isset($_FILES['imagen']['name'])){
            $imagen="";
            //establecemos el formato en que se almacena la url en la base de datos
             if(strcmp($_FILES['imagen']['name'], $hotelImage->url)!==0 && $_FILES['imagen']['name']!=null)
            {
                //establecemos el directorio con el cual trabajaremos
                $dir="./assets/img/hotel/";
                //obtenemos la ruta de almacenamiento de la imagen
                $ruta=$dir.$hotel->id."/".$_FILES['imagen']['name'];
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
                                @unlink($dir.$hotelImage->id_hotel."/".$hotelImage->url);
                                //obtenemos la ruta del archivo
                                $imagen=$_FILES['imagen']['name'];                  
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
                $imagen=$hotelImage->url;
            }
            //actualizamos atributo de la entidad
            $hotelImage->url = $imagen;
            //actualizamos la entidad
            $hotelImageMapper->update($hotelImage);
        }
        echo $this->renderWiew(array_merge(["hotel" => $hotel,"image"=>$hotelImage]),$res);
    }
     /**
    *   Funcion que sirve para eliminar una imagen relacionada a un tour
    **/
    public function deleteImages($req,$res)
    {
        //Obtenemos el id, de la experiencia a eleminar
        $var=$req->params["hotel"];
        //Establecemos a spot con que entity class vamos a trabajar
        $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
        $hotelMapper=$this->spot->mapper("Entity\Hotel");
        //Seleccionamos la experiencia que este registrado para ese ide
        $hotelImage = $hotelImageMapper->select()->where(["id" => $req->params["hotel"]])->first();
        $ruta="./assets/img/hotel/".$hotelImage->id_hotel."/".$hotelImage->url;
        $hotel=$hotelMapper->select()->where(["id"=> $hotelImage->id_hotel])->first();
        //Eliminamos el registro del id seleccionado
        $hotelImage=null;
        $hotelImage = $hotelImageMapper->delete(['id ='=>(integer)$var]);

        //Establecemos a spot con que entity class vamos a trabajar
        @unlink($ruta);
        echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    * Funcion que sirve para registrar un hotel a la base de datos
    **/
    public function add($req, $res) {
    	if(isset($req->data["name"])){
    		//definimos donde se almacenara el thumbnail
    		$dir="./assets/img/hotel-thumb/";
    		//array de tipos de archivos permitidos
    		$permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
    		/*Obtencion y sanitizacion de parametros*/
    		$name=filter_var($req->data["name"], FILTER_SANITIZE_STRING);
    		$address=filter_var($req->data["address"], FILTER_SANITIZE_STRING);
    		$map=$req->data["map"];
    		$email=filter_var($req->data["email"], FILTER_SANITIZE_STRING);
    		$web=filter_var($req->data["website"], FILTER_SANITIZE_STRING);
    		$tel=filter_var($req->data["tel"], FILTER_SANITIZE_STRING);
    		$descripcion=filter_var($req->data["descripcion"], FILTER_SANITIZE_STRING);
            $descripcion_spa=filter_var($req->data["descripcion_spa"], FILTER_SANITIZE_STRING);

    		$thumb="";
    		$error=0;
    		//vemos si hubo imagen
    		if($_FILES['thumbnail']['name']!=null){
    			if(!($_FILES['thumbnail']['error']>0)){
    				if(in_array($_FILES['thumbnail']['type'],$permitidos)){
    					//definimos la ruta de la imagen a subir
    					$ruta=$dir."/".$_FILES['thumbnail']['name'];
    					//verificamos si el archivo existe
    					if(!file_exists($ruta)){
    						$resultado=@move_uploaded_file($_FILES['thumbnail']["tmp_name"],$ruta);
    						//si la imagen se sube exitosamente asignamos a thumb el nombre del archivo
    						if($resultado){
    							$thumb=$_FILES['thumbnail']['name'];
    						}
    					}
    					else{
    						$error=1;
    						echo "<div class=error><p>An image already exists with that name.</p></div>";
    					}
    				}
    				else{
    					$error=1;
    					echo "<div class=error><p>File type not allowed.</p></div>";
    				}
    			}
    			else{
    				$error=1;
    				echo "<div class=error><p>The image was not uploaded.</p></div>";
    			}
    		}
    		//seleccionamos entidad a maperar
    		$hotelMapper=$this->spot->mapper("Entity\Hotel");
    		//construimos la entidad
    		$entity = $hotelMapper->build([
                'name' => $name,
                'thumbnail' => $thumb,
                'description' =>$descripcion,
                'description_esp'=>$descripcion_spa,
                'address' =>$address,
                'website' => $web,
                'map' => $map,
                'tel' => $tel,
                'email' =>$email
            ]);
            //insertamos la entidad si no hubo ningun error
            if($error==0){
            	$result=$hotelMapper->insert($entity);
            	echo "<div class=exito><p>Hotel Registered.</p></div>";
            }
    	}
       
        echo $this->renderWiew([], $res);
    }
    /**
    *	Funcion que obtiene todos los registros de la tabla hotel.
    **/
    public function show($req,$res){
    	$hotelMapper=$this->spot->mapper("Entity\Hotel");
    	$hotel=$hotelMapper->select();
    	echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *	Funcion que actualiza un objeto de las clase hotel.
    **/
    public function edit($req,$res){
        if($req->params["hotel"]!=null){
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel = $hotelMapper->select()->with("images")->where(["id" => $req->params["hotel"]])->first();    
        }
        if(isset($req->data["name"])){
            //definimos donde se almacenara el thumbnail
            $dir="./assets/img/hotel-thumb/";
            //array de tipos de archivos permitidos
            $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
            //obtenemos el id de la experiencia
            $id=$req->params["hotel"];
            //obtenemos los elementos del formulario, verificamos que no sean nulos, en caso de ser nulo asignamos el valor que esta almacenado en dicho atributo en la base de datos.
            $name=$req->data["name"]!=null? filter_var($req->data["name"],FILTER_SANITIZE_STRING): $hotel->name;
            $address=$req->data["address"]!=null? filter_var($req->data["address"],FILTER_SANITIZE_STRING): $hotel->address;
            $map=$req->data["map"]!=null? $req->data["map"]: $hotel->map;
            $email=$req->data["email"]!=null?filter_var($req->data["email"],FILTER_SANITIZE_STRING):$hotel->email;
            $web=$req->data["website"]!=null?filter_var($req->data["website"],FILTER_SANITIZE_STRING):$hotel->website;
            $tel=$req->data["tel"]!=null?filter_var($req->data["tel"],FILTER_SANITIZE_STRING):$hotel->tel;
            $descripcion=$req->data["descripcion"]!=null?filter_var($req->data["descripcion"],FILTER_SANITIZE_STRING):$hotel->description;
            $description_es=$req->data["descripcion_spa"]!=null?filter_var($req->data["descripcion_spa"],FILTER_SANITIZE_STRING):$hotel->description_esp;
            $thumb="";
            $img="";
            $error=0;
            if(strcmp($_FILES['thumbnail']['name'],$hotel->thumbnail)!=0 && $_FILES['thumbnail']['name']!=null){
                if(!($_FILES['thumbnail']['error']>0)){
                    if(in_array($_FILES['thumbnail']['type'],$permitidos)){
                        //definimos la ruta de la imagen a subir
                        $ruta=$dir."/".$_FILES['thumbnail']['name'];
                        //verificamos si el archivo existe
                        if(!file_exists($ruta)){
                            $resultado=@move_uploaded_file($_FILES['thumbnail']["tmp_name"],$ruta);
                            //si la imagen se sube exitosamente asignamos a thumb el nombre del archivo
                            if($resultado){
                                if($hotel->thumbnail!=null){
                                    @unlink($dir."/".$hotel->thumbnail);
                                }
                                $thumb=$_FILES['thumbnail']['name'];
                            }
                        }
                        else{
                            $error=1;
                            echo "<div class=error><p>An image already exits with that name.</p></div>";
                        }
                    }
                    else{
                        $error=1;
                        echo "<div class=error><p>File type not allowed.</p></div>";
                    }
                }
                else{
                    $error=1;
                    echo "<div class=error><p>Image was not uploaded.</p></div>";
                }
            }
            else{
                $thumb=$hotel->thumbnail;
            }
            if($error==0){
                //actualizamos los valores del registro y actualizamos en base de datos
                $hotel->name=$name;
                $hotel->thumbnail=$thumb;
                $hotel->description=$descripcion;
                $hotel->description_esp=$description_es;
                $hotel->address=$address;
                $hotel->website=$web;
                $hotel->map=$map;
                $hotel->tel=$tel;
                $hotel->email=$email;
                $hotelMapper->update($hotel);
                echo "<div class=exito><p>Hotel updated</p></div>";

            }
        }
        
    	echo $this->renderWiew( array_merge(["hotel" => $hotel]), $res);
    }
    
    /**
    *	Funcion que elimina un registro de hotel y todas las imagenes relacionadas a esta 
    *	hotel.
    **/
    public function delete($req,$res){
	    	//Obtenemos el id, de la hotel a eleminar
	    	$var=$req->params["hotel"];
	    	//Establecemos a spot con que entity class vamos a trabajar
	    	$hotelMapper=$this->spot->mapper("Entity\Hotel");
	    	//Seleccionamos la hotel que este registrado para ese ide
	    	$hotel = $hotelMapper->select()->where(["id" => $req->params["hotel"]])->first();
	    	$ruta="./assets/img/hotel-thumb/".$hotel->thumbnail;
			//Eliminamos el registro del id seleccionado
			$hotel=null;
	    	$hotel = $hotelMapper->delete(['id ='=>(integer)$var]);
	    	//Establecemos a spot con que entity class vamos a trabajar
			$hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
			//Obtenemos todas las imagenes que esten relacionadas con el post
			//eliminamos el directorio donde se encontraban las imagenes
			//Obtenemos la ruta del thumbnail
		    
			@unlink($ruta);
			$this->eliminarFiles("./assets/img/hotel/".$var);
			$this->deleteDirectory("./assets/img/hotel/".$var);
			//obtenemos la ruta de cada imagen asociada y eliminamos el ficher
			$hotel=null;
			$hotel = $hotelImageMapper->delete(['id_hotel =' => (integer)$var]);
	    	echo $this->renderWiew( array_merge([]), $res);
    }
     /**
    *   Funcion que elimina todos los archivos del directorio y el directorio
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
    *   funcion que elemina el directorio
    *   @param $carpeta
    */
    private function deleteDirectory($dir) {
        system('rm -rf ' . escapeshellarg($dir), $retval);
        return $retval == 0; // UNIX commands return zero on success
    }

}
