<?php

class Hotel extends Luna\Controller {
    /**
    *   Metodo que registra un video a un hotel
    **/
    public function addVideo($req,$res){
        if(isset($req->data["hotel"],$req->data["video"])){
            $tipo="V";
            $hotel=$req->data["hotel"];
            $path=$req->data["video"];
            $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
            $entity=$hotelImageMapper->build([
                'path' =>$path,
                'tipo' =>$tipo,
                'hotel_idhotel' =>$hotel
            ]);
            $result=$hotelImageMapper->insert($entity);
        }
         $userZonaMapper=$this->spot->mapper("Entity\UsersZona");
        $zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
        $aux=array();
        foreach ($zones as $zone) {
            array_push($aux,$zone['zona_idzona']);
        }
        $hotelMapper=$this->spot->mapper("Entity\Hotel");
        $hotel=$hotelMapper->select()->where(['zona_idzona'=>$aux]);
        echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *   Metodo que registra un objeto de la clase hotel_images
    **/
    public function addImages($req,$res){
        $userZonaMapper=$this->spot->mapper("Entity\UsersZona");
        $zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
        $aux=array();
        foreach ($zones as $zone) {
            array_push($aux,$zone['zona_idzona']);
        }
        $hotelMapper=$this->spot->mapper("Entity\Hotel");
        $hotel=$hotelMapper->select()->where(['zona_idzona'=>$aux]);
        if(isset($req->data["hotel"],$_FILES['imagen']['name'])){
            $hAux=$hotelMapper->select()->where(["idhotel"=>$req->data["hotel"]])->first();
            if($hAux->zona_idzona==1){
                $dir="./assets/img/hotel/";    
            }
            else{
                $dir="../maldivas/assets/img/hotel/";
            }
            if (file_exists($dir.$req->data["hotel"])==false) {
                     mkdir($dir.$req->data["hotel"]);
            }
            //definimos un array de tipos de datos permitidos
            $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
             if(!($_FILES['imagen']['error']>0)){
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['imagen']['type'], $permitidos)){
                        $aux=explode('.',$_FILES['imagen']['name']);
                        $ruta=$dir.$req->data["hotel"]."/".$aux[0].substr(uniqid(),0,-3).$aux[1];
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
                            if($resultado){
                                //Guardamos la experiencia en la base de datos
                                $file=$_FILES['imagen']['name'];
                                $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
                                $entity = $hotelImageMapper->build([
                                    'hotel_idhotel' =>$req->data["hotel"],
                                    'tipo'=>"I",
                                    'path' =>$file
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
        echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *   Metodo que muestra las imagenes por hotel
    **/
      public function showImages($req,$res){
         $hotelMapper = $this->spot->mapper("Entity\Hotel");
         $hotel = $hotelMapper->select()->with("images");
         echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *   Metodo que sirve para editar una imagen de un hotel
    **/
    public function editImages($req,$res){
        if($req->params["hotel"]!=null){
            //obtener imagen mediante el id
            $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
            $hotelImage= $hotelImageMapper->select()->where(["idhotelImages" => $req->params["hotel"]])->first(); 
            //obtener el tour mediante el id que obtenemos de la imagen
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel=$hotelMapper->select()->where(["idhotel" => $hotelImage->hotel_idhotel])->first();
            if($hotel->zona_idzona!=1){
                $imagen="http://".$_SERVER['HTTP_HOST']."/maldivas/assets/img/";
            }
            else{
                $imagen="/bali/assets/img/";
            }
        }
        if(isset($_FILES['imagen']['name'])){
            $imagen="";
            //establecemos el formato en que se almacena la url en la base de datos
             if(strcmp($_FILES['imagen']['name'], $hotelImage->path)!==0 && $_FILES['imagen']['name']!=null)
            {
                //establecemos el directorio con el cual trabajaremos
                $dir="./assets/img/hotel/";
                $aux=explode('.',$_FILES['imagen']['name']);
                if($hotel->zona_idzona==1){
                    $ruta=$dir.$hotel->idhotel."/".$aux[0].substr(uniqid(),0,-3).$aux[1];
                }
                else{
                    $ruta="../maldivas/assets/img/hotel/".$hotel->idhotel."/".$aux[0].substr(uniqid(),0,-3).$aux[1];
                    $dir="../maldivas/assets/img/hotel/";
                }
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
                                @unlink($dir.$hotelImage->hotel_idhotel."/".$hotelImage->path);
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
                $imagen=$hotelImage->path;
            }
            //actualizamos atributo de la entidad
            $hotelImage->path = $imagen;
            //actualizamos la entidad
            $hotelImageMapper->update($hotelImage);
        }
        echo $this->renderWiew(array_merge(["hotel" => $hotel,"image"=>$hotelImage,"thumb"=>$imagen]),$res);
    }
    /**
    *   Metodo que sirve para editar un video relacionado a un hotel
    **/
    public function editVideo($req,$res){
        if(isset($req->params["hotel"])){
            $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
            $hotelImage= $hotelImageMapper->select()->with("hotel")->where(["idhotelImages" => $req->params["hotel"]])->first(); 
        }
        if(isset($req->data["video"])){
            $hotelImage->path=$req->data["video"];
            $hotelImageMapper->update($hotelImage);
        }
        echo $this->renderWiew(array_merge(["video"=>$hotelImage]),$res);
    }
    /**
    *   Metodo que sirve para eliminar una imagen relacionada a un hotel
    **/
    public function deleteImages($req,$res)
    {
        //Obtenemos el id, de la experiencia a eleminar
        $var=$req->params["hotel"];
        //Establecemos a spot con que entity class vamos a trabajar
        $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
        $hotelMapper=$this->spot->mapper("Entity\Hotel");
        //Seleccionamos la experiencia que este registrado para ese ide
        $hotelImage = $hotelImageMapper->select()->where(["idhotelImages" => $req->params["hotel"]])->first();
        $hotel=$hotelMapper->select()->where(["idhotel"=> $hotelImage->hotel_idhotel])->first();
        if($hotel->zona_idzona==1){
            $ruta="./assets/img/hotel/".$hotelImage->hotel_idhotel."/".$hotelImage->path;
        }
        else{
            $ruta="../maldivas/assets/img/hotel/".$hotelImage->hotel_idhotel."/".$hotelImage->path;
        }
        //Eliminamos el registro del id seleccionado
        $hotelImage=null;
        $hotelImage = $hotelImageMapper->delete(['idhotelImages'=>(integer)$var]);
        //Establecemos a spot con que entity class vamos a trabajar
        @unlink($ruta);
        echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *   Metodo que elimina un video de un hotel
    **/
    public function deleteVideo($req,$res){
        if(isset($req->params["hotel"])){
           $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
           $hotelImage=$hotelImageMapper->select()->where(["idhotelImages"=>$req->params["hotel"]])->first();
           $hotelMapper=$this->spot->mapper("Entity\Hotel");
           $hotel=$hotelMapper->select()->where(["idhotel"=> $hotelImage->hotel_idhotel])->first()->toArray();
           $hotelImage = $hotelImageMapper->delete(['idhotelImages'=>(integer)$req->params["hotel"]]);
           header("Location:/bali/panel/hotel/edit/".$hotel["idhotel"]);
        }
    }

    /**
    * Metodo que sirve para registrar un hotel a la base de datos
    **/
    public function add($req, $res) {
        $userZonaMapper=$this->spot->mapper("Entity\UsersZona");
        $zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
        $aux=array();
        foreach ($zones as $zone) {
            array_push($aux,$zone['zona_idzona']);
        }
        $zoneMapper=$this->spot->mapper("Entity\Zona");
        $zona=$zoneMapper->select()->where(["idzona"=>$aux]);
    	if(isset($req->data["name"],$req->data["zone"])){
    		//definimos donde se almacenara el thumbnail
    		$dir="/assets/img/hotel-thumb/";
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
            $zona=$req->data["zone"];

    		$thumb="";
    		$error=0;
    		//vemos si hubo imagen
    		if($_FILES['thumbnail']['name']!=null){
    			if(!($_FILES['thumbnail']['error']>0)){
    				if(in_array($_FILES['thumbnail']['type'],$permitidos)){
                        $aux=explode('.',$_FILES['imagen']['name']);
                        if($zona==1){
                            $ruta=$dir."/".$aux[0].substr(uniqid(),0,-3).$aux[1];
                        }
                        else{
                            $ruta="../maldivas/assets/img/hotel-thumb/".$aux[0].substr(uniqid(),0,-3).$aux[1];
                        }
    					//verificamos si el archivo existe
    					if(!file_exists($ruta)){
    						$resultado=@move_uploaded_file($_FILES['thumbnail']["tmp_name"],$ruta);
    						//si la imagen se sube exitosamente asignamos a thumb el nombre del archivo
    						if($resultado){
    							$thumb=$_FILES['thumbnail']['name'];
    						}
    					}
    					else{		
    						echo "<script>alert('An image already exists with that name.');</script>";
    					}
    				}
    				else{
    					$error=1;
    					echo "<script>alert('File type not allowed.');</script>";
    				}
    			}
    			else{
    				$error=1;
    				echo "<script>alert('The image was not uploaded.');</script>";
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
                'email' =>$email,
                'zona_idzona'=>$zona
            ]);
            //insertamos la entidad si no hubo ningun error
            if($error==0){
            	$result=$hotelMapper->insert($entity);
                header("location:/panel/hotel/add");
                exit;
            }
    	}
       
        echo $this->renderWiew(array_merge(["zones"=>$zona]), $res);
    }
    /**
    *	Metodo que obtiene todos los registros de la tabla hotel.
    **/
    public function show($req,$res){
        $userZonaMapper=$this->spot->mapper("Entity\UsersZona");
        $zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
        $aux=array();
        foreach ($zones as $zone) {
            array_push($aux,$zone['zona_idzona']);
        }
    	$hotelMapper=$this->spot->mapper("Entity\Hotel");
    	$hotel=$hotelMapper->select()->with("zona")->where(["zona_idzona"=>$aux]);
    	echo $this->renderWiew(array_merge(["hotel" => $hotel]),$res);
    }
    /**
    *	Metodo que actualiza un objeto de las clase hotel.
    **/
    public function edit($req,$res){
        if($req->params["hotel"]!=null){
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel = $hotelMapper->select()->with("zona")->where(["idhotel" => $req->params["hotel"]])->first();
            $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
            $images=$hotelImageMapper->select()->where(["hotel_idhotel"=>$req->params["hotel"],"tipo"=>"I"]);
            $videos=$hotelImageMapper->select()->where(["hotel_idhotel"=>$req->params["hotel"],"tipo"=>"V"]);
            $userZonaMapper=$this->spot->mapper("Entity\UsersZona");
            $zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
            $aux=array();
            foreach ($zones as $zone) {
                array_push($aux,$zone['zona_idzona']);
            }
            $zoneMapper=$this->spot->mapper("Entity\Zona");
            $zona=$zoneMapper->select()->where(["idzona"=>$aux]);  
            if($hotel->zona_idzona!=1){
                $imagen="http://".$_SERVER['HTTP_HOST']."/maldivas/assets/img/";
            }
            else{
                $imagen="/bali/assets/img/";
            }  
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
            $zona=$req->data["zone"]!=null?$req->data["zone"]:$hotel->zona_idzona;
            $thumb="";
            $img="";
            $error=0;
            if(strcmp($_FILES['thumbnail']['name'],$hotel->thumbnail)!=0 && $_FILES['thumbnail']['name']!=null){
                if(!($_FILES['thumbnail']['error']>0)){
                    if(in_array($_FILES['thumbnail']['type'],$permitidos)){
                        //definimos la ruta de la imagen a subir
                        $aux=explode('.',$_FILES['imagen']['name']);
                        $ruta=$dir."/".$aux[0].substr(uniqid(),0,-3).$aux[1];
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
        
    	echo $this->renderWiew( array_merge(["hotel" => $hotel,"zones"=>$zona,"images"=>$images,"thumb"=>$imagen,"videos"=>$videos]), $res);
    }
    
    /**
    *	Metodo que elimina un registro de hotel y todas las imagenes relacionadas a esta 
    *	hotel.
    **/
    public function delete($req,$res){
	    	//Obtenemos el id, de la hotel a eleminar
	    	$var=$req->params["hotel"];
	    	//Establecemos a spot con que entity class vamos a trabajar
	    	$hotelMapper=$this->spot->mapper("Entity\Hotel");
	    	//Seleccionamos la hotel que este registrado para ese ide
	    	$hotel = $hotelMapper->select()->where(["id" => $req->params["hotel"]])->first();
            $zona=$hotel->zona_idzona;
            if($zona==1){
                $ruta="./assets/img/hotel-thumb/".$hotel->thumbnail;
            }
            else{
                $ruta="../maldivas/assets/img/hotel-thumb/".$hotel->thumbnail;
            }
	    	
			//Eliminamos el registro del id seleccionado
			$hotel=null;
	    	$hotel = $hotelMapper->delete(['idhotel'=>(integer)$var]);
	    	//Establecemos a spot con que entity class vamos a trabajar
			$hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
			//Obtenemos todas las imagenes que esten relacionadas con el post
			//eliminamos el directorio donde se encontraban las imagenes
			//Obtenemos la ruta del thumbnail		    
			@unlink($ruta);
            if($zona==1){
                $this->eliminarFiles("./assets/img/hotel/".$var);
                $this->deleteDirectory("./assets/img/hotel/".$var);
            }
            else{
                $this->eliminarFiles("../maldivas/assets/img/hotel/".$var);
                $this->deleteDirectory("../maldivas/assets/img/hotel/".$var);
            }
			//obtenemos la ruta de cada imagen asociada y eliminamos el ficher
			$hotel=null;
			$hotel = $hotelImageMapper->delete(['hotel_idhotel' => (integer)$var]);
	    	echo $this->renderWiew( array_merge([]), $res);
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
