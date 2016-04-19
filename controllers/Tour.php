<?php

class Tour extends Luna\Controller {

    /**
    *	Funcion que registra un objeto de las clase tour_images.
    **/
    public function addImages($req, $res) 
    {   
        if(isset($req->data["exper"],$_FILES['imagen']['name']))
        {
                //definimos la ruta donde se creara el directorio
                $dir="./assets/img/experience/";
                //creamos el directorio que lleva el nombre del id
                if(!file_exists("./assets/img/experience/".$req->data["exper"]))
                {
                    mkdir("./assets/img/experience/".$req->data["exper"]);
                }
                //definimos un array de tipos de datos permitidos
                $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
                //Obtenemos y sanitizamos los parametros obtenidos por el metodo  post.
                //validamos si se subio la imagen
                if(!($_FILES['imagen']['error']>0))
                {
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['imagen']['type'], $permitidos)){
                        $ruta=$dir."/".$req->data["exper"]."/".$_FILES['imagen']['name'];
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
                            if($resultado){
                                //Guardamos la experiencia en la base de datos
                                $file=$_FILES['imagen']['name'];
                                $tourMapper=$this->spot->mapper("Entity\TourImage");
                                $img=(string)$req->data["exper"]."/".(string)$_FILES['imagen']['name'];
                                $entity = $tourMapper->build([
                                    'id_tour' =>$req->data["exper"],
                                    'url' =>$img
                                ]);
                                $result = $tourMapper->insert($entity);
                                echo "<div class=exito><p>The image was uploaded</p></div>";
                            }
                            else{ echo "<div class=error><p>There was a problem uploading de image.</p></div>";}
                        }
                        else{ echo "<div class=error><p>An image already exists with that name.</p></div>";}
                    }
                    else{ echo "<div class=error><p>File type not allowed.</p></div>";}
                }
                else{ echo "<div class=error><p>The image was not uploaded.</p></div>";}
        }
        $tourMapper=$this->spot->mapper("Entity\Tour");
        $tour=$tourMapper->select();
        echo $this->renderWiew(array_merge(["tour" => $tour]),$res);
    }
    /**
    *   Funcion que obtiene todos los registros de la tabla tour_images.
    **/
    public function showImages($req,$res){
         $tourMapper = $this->spot->mapper("Entity\Tour");
         $tour = $tourMapper->select()->with("images");
         echo $this->renderWiew(array_merge(["tour" => $tour]),$res);

    }
    /**
    *   Funcion que permite editar una imagen de un tour
    **/
    public function editImages($req,$res){
        if($req->params["exper"]!=null){
            //obtener imagen mediante el id
            $tourImageMapper=$this->spot->mapper("Entity\TourImage");
            $tourImage= $tourImageMapper->select()->where(["id" => $req->params["exper"]])->first(); 
            //obtener el tour mediante el id que obtenemos de la imagen
            $tourMapper=$this->spot->mapper("Entity\Tour");
            $tour=$tourMapper->select()->where(["id" => $tourImage->id_tour])->first();
        }
        if(isset($_FILES['imagen']['name'])){
            $imagen="";
            //establecemos el formato en que se almacena la url en la base de datos
            $aux=$tour->id."/".$_FILES['imagen']['name'];
             if(strcmp($aux, $tourImage->url)!==0 && $_FILES['imagen']['name']!=null)
            {
                //establecemos el directorio con el cual trabajaremos
                $dir="./assets/img/experience/";
                //obtenemos la ruta de almacenamiento de la imagen
                $ruta=$dir.$aux;
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
                                @unlink($dir."/".$tourImage->url);
                                //obtenemos la ruta del archivo
                                $imagen=$aux;                  
                            }
                            else{ echo "<div class=error><p>There was a problem uploading the image.</p></div>";}
                        }
                        else{ echo "<div class=error><p>An image already exits with that name.</p></div>";}
                    }
                    else{ echo "<div class=error><p>File type not allowed.</p></div>";}
                }
                else{ echo "<div class=error><p>The image was not uploaded.</p></div>";}
            }
            else{
                $imagen=$tourImage->url;
            }

            
            //actualizamos atributo de la entidad
            $tourImage->url = $imagen;
            //actualizamos la entidad
            $tourImageMapper->update($tourImage);
        }
        echo $this->renderWiew(array_merge(["tour" => $tour,"image"=>$tourImage]),$res);
    }
    /**
    *   Funcion que sirve para eliminar una imagen relacionada a un tour
    **/
    public function deleteImages($req,$res)
    {
        //Obtenemos el id, de la experiencia a eleminar
        $var=$req->params["exper"];
        //Establecemos a spot con que entity class vamos a trabajar
        $tourMapper=$this->spot->mapper("Entity\TourImage");
        $tMapper=$this->spot->mapper("Entity\Tour");
        //Seleccionamos la experiencia que este registrado para ese ide
        $tour = $tourMapper->select()->where(["id" => $req->params["exper"]])->first();
        $tou=$tMapper->select()->where(["id"=>$tour->id_tour])->first();
        $ruta="./assets/img/experience/".$tour->url;
        //Eliminamos el registro del id seleccionado
        $tour=null;
        $tour = $tourMapper->delete(['id ='=>(integer)$var]);
        //Establecemos a spot con que entity class vamos a trabajar
        @unlink($ruta);
        echo $this->renderWiew(array_merge(["tour" => $tou]),$res);
    }
    /**
    *   Funcion que registra un objeto de las clase tour.
    **/
    public function add($req, $res) 
    {
        if(isset($req->data["titulo"],$req->data["tipo"],$req->data["duracion"],$req->data["horas"],$req->data["descripcion"],$_FILES['thumbnail']['name']))
            {
                //definimos la ruta donde se almacenara el thumbnail
                $dir="./assets/img/experience/indo/";
                $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
                //Obtenemos y sanitizamos los parametros obtenidos por el metodo  post.
                $titulo =filter_var($req->data["titulo"], FILTER_SANITIZE_STRING);
                $tipo = filter_var($req->data["tipo"], FILTER_SANITIZE_STRING);
                $duracion = filter_var($req->data["duracion"] , FILTER_SANITIZE_STRING);
                $duracion_spa="";
                switch($duracion){
                    case "HALF DAY":
                        $duracion_spa="MEDIO DIA";
                    break;
                    case "FULL DAY";
                        $duracion_spa="DIA COMPLETO";
                    break;
                    default:
                        $duracion_spa="Falta Traduccion";
                    break;
                }
                $horas = filter_var($req->data["horas"], FILTER_SANITIZE_NUMBER_INT);
                $descripcion = filter_var($req->data["descripcion"], FILTER_SANITIZE_STRING);
                $descripcion_spa=filter_var($req->data["descripcion_spa"], FILTER_SANITIZE_STRING);
                $Transfer = filter_var($req->data["transfer"], FILTER_SANITIZE_STRING);
                $Transfer_spa = filter_var($req->data["transfer_spa"], FILTER_SANITIZE_STRING);
                if(isset($req->data["esPrincipal"])){
                    $home = true;
                }else{
                    $home = false;#default value
                }
                //validamos si se subio la imagen
                if(!($_FILES['thumbnail']['error']>0))
                {
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['thumbnail']['type'], $permitidos)){
                        $ruta=$dir."/".$_FILES['thumbnail']['name'];
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $ruta);
                            if($resultado){
                                //Guardamos la experiencia en la base de datos
                                $file=$_FILES['thumbnail']['name'];
                                $tourMapper=$this->spot->mapper("Entity\Tour");
                                $entity = $tourMapper->build([
                                    'title' => $titulo,
                                    'thumbnail' => $file,
                                    'type' =>$tipo,
                                    'duration' => $duracion."/".(String)$horas,
                                    'duration_esp' => $duracion_spa."/".(String)$horas,
                                    'description' => $descripcion,
                                    'description_esp' => $descripcion_spa,
                                    'transfer' => $Transfer,
                                    'transfer_esp' => $Transfer_spa,
                                    'home'=>$home,
                                ]);
                                $result = $tourMapper->insert($entity);
                               // header("Location: /panel/hotel/edit/".$entity->id);
                               // exit;
                                echo "<div class=exito><p>Tour Registered.</p></div>";
                            }
                            else{ echo "<div class=error><p>There was a problem uploading the image.</p></div>";}
                        }
                        else{ echo "<div class=error><p>An image already exists with that name.</p></div>";}
                    }
                    else{ echo "<p class=error>File type not allowed.</p></div>";}
                }
                else{ echo "<p class=error>The image was not uploaded.</p></div>";}
            }
        echo $this->renderWiew([], $res);
    }
    /**
    *	Funcion que obtiene todos los registros de la tabla tour.
    **/
    public function show($req,$res){
    	$tourMapper=$this->spot->mapper("Entity\Tour");
    	$tour=$tourMapper->select();
    	echo $this->renderWiew(array_merge(["tour" => $tour]),$res);
    }
    /**
    *	Funcion que actualiza un objeto de las clase tour.
    **/
    public function edit($req,$res){
        if($req->params["exper"]!=null){
            $tourMapper=$this->spot->mapper("Entity\Tour");
            $tour = $tourMapper->select()->where(["id" => $req->params["exper"]])->first(); 
            $home="nada";   
        }
        if(isset($req->data["name"])){
            //obtenemos el id de la experiencia
            $id=$req->params["exper"]; 
            //obtenemos los elementos del formulario, verificamos que no sean nulos, en caso de ser nulo asignamos el valor que esta almacenado en dicho atributo en la base de datos.
            $titulo = $req->data["name"]!=null? filter_var($req->data["name"], FILTER_SANITIZE_STRING) : $tour->title;
            $tipo = $req->data["tipo"]!=null? filter_var($req->data["tipo"], FILTER_SANITIZE_STRING) : $tour->type;
            $duracion = ($req->data["duracion"]!=null)&&($req->data["horas"]!=null)? filter_var($req->data["duracion"],FILTER_SANITIZE_STRING)."/".filter_var($req->data["horas"],FILTER_SANITIZE_NUMBER_INT): $tour->duration;
            $duracion_spa="";
                switch($req->data["duracion"]){
                    case "HALF DAY":
                        $duracion_spa="MEDIO DIA"."/".filter_var($req->data["horas"]);
                    break;
                    case "FULL DAY":
                        $duracion_spa="DIA COMPLETO"."/".filter_var($req->data["horas"]);
                    break;
                    default:
                        $duracion_spa="Falta Traduccion";
                    break;
                }
            $descripcion = $req->data["descripcion"]!=null? filter_var($req->data["descripcion"], FILTER_SANITIZE_STRING) : $tour->description;
            $descripcion_spa = $req->data["descripcion_esp"]!=null? filter_var($req->data["descripcion_esp"], FILTER_SANITIZE_STRING) : $tour->description_esp;
            $transfer = $req->data["Transfer"]!=null? filter_var($req->data["Transfer"], FILTER_SANITIZE_STRING) : $tour->transfer;
            $transfer_spa = $req->data["Transfer_esp"]!=null? filter_var($req->data["Transfer_esp"], FILTER_SANITIZE_STRING) : $tour->transfer_esp;
            $thumbnail="";
            if(isset($req->data["esPrincipal"])){
                $home = true;
            }else{
                $home = false;#default value
            }
            //comparamos el nombre de la imagen a subir y la imagen en la base de datos, en caso de ser diferentes subimos el archivo y borramos el archivo anterior
            if(strcmp($_FILES['thumbnail']['name'], $tour->thumbnail)!==0 && $_FILES['thumbnail']['name']!=null)
            {
                $dir="./assets/img/experience/indo/";
                $ruta=$dir."/".$_FILES['thumbnail']['name'];
                $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
                //Obtenemos y sanitizamos los parametros obtenidos por el metodo  post.
                //validamos si se subio la imagen
                if(!($_FILES['thumbnail']['error']>0))
                {
                    //validamos que la imagen sea de los tipos establecidos
                    if(in_array($_FILES['thumbnail']['type'], $permitidos)){
                        //verificamos que no exista una imagen que se llame igual
                        if(!file_exists($ruta)){
                            //subimos la imagen al servidor
                            $resultado=@move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $ruta);
                            if($resultado){
                                //eliminamos fichero anterior
                                @unlink($dir."/".$tour->thumbnail);
                                //obtenemos la ruta del archivo
                                $thumbnail=$_FILES['thumbnail']['name'];
                                
                            }
                            else{ echo "<div class=error><p>There was a problem uploading the image.</p></div>";}
                        }
                        else{ echo "<div class=error><p>An image already exists with that name.</p></div>";}
                    }
                    else{ echo "<p class=error>File type not allowed.</p></div>";}
                }
                else{ echo "<p class=error>The image was not uploaded.</p></div>";}
            }
            else{
                $thumbnail=$tour->thumbnail;
            }
            //actualizamos los valores de la entidad
            $tour->title = $titulo;
            $tour->thumbnail = $thumbnail;
            $tour->type =  $tipo;
            $tour->duration = $duracion;
            $tour->duration_esp = $duracion_spa;
            $tour->description = $descripcion;
            $tour->description_esp = $descripcion_spa;
            $tour->transfer = $transfer;
            $tour->transfer_esp = $transfer_spa;
            $tour->home=$home;
            //actualizamos la entidad
            $tourMapper->update($tour);
            $tour = $tourMapper->select()->where(["id" => $req->params["exper"]])->first();
        }
        
    	echo $this->renderWiew( array_merge(["tour" => $tour]), $res);
    }
    
    /**
    *	Funcion que elimina un registro de experiencia y todas las imagenes relacionadas a esta 
    *	experiencia.
    **/
    public function delete($req,$res){
	    	//Obtenemos el id, de la experiencia a eleminar
	    	$var=$req->params["exper"];
	    	//Establecemos a spot con que entity class vamos a trabajar
	    	$tourMapper=$this->spot->mapper("Entity\Tour");
	    	//Seleccionamos la experiencia que este registrado para ese ide
	    	$tour = $tourMapper->select()->where(["id" => $req->params["exper"]])->first();
	    	$ruta="./assets/img/experience/indo/".$tour->thumbnail;
			//Eliminamos el registro del id seleccionado
			$tour=null;
	    	$tour = $tourMapper->delete(['id ='=>(integer)$var]);
	    	//Establecemos a spot con que entity class vamos a trabajar
			$tourMapper=$this->spot->mapper("Entity\TourImage");
			//Obtenemos todas las imagenes que esten relacionadas con el post
			//eliminamos el directorio donde se encontraban las imagenes
			//Obtenemos la ruta del thumbnail
		    
			@unlink($ruta);
			$this->eliminarFiles("./assets/img/experience/".$var);
			$this->deleteDirectory("./assets/img/experience/".$var);
			//obtenemos la ruta de cada imagen asociada y eliminamos el ficher
			$tour=null;
			$tour = $tourMapper->delete(['id_tour =' => (integer)$var]);
	    	echo $this->renderWiew( array_merge([] , $this->header("/panel/tour/show") ), $res);
    }
    /**
    *	Funcion que elimina todos los archivos del directorio y el directorio
    *	@param $carpeta
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
	*	funcion que elemina el directorio
	*	@param $carpeta
	*/
	private function deleteDirectory($dir) {
    	system('rm -rf ' . escapeshellarg($dir), $retval);
    	return $retval == 0; // UNIX commands return zero on success
    }
}
