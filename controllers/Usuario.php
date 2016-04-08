<?php

/**
 *    Controlador de lugares
 *
 *
 */
//use CentroNotificaciones;




class Usuario extends Luna\Controller {

    public function login($req, $res) {
        echo $this->renderWiew($this->header("login"), $res);
    }
    public function logout($req, $res) {
        header("Location: http://" . $_SERVER["SERVER_NAME"] . "/login");
    }

    public function header( $menu ){

        $wmpr = $this->spot->mapper("Entity\Weather");
        $current_data = $wmpr->select()->order( ["updated"=>"DESC"] )->first();
        $duration = null;
        if( is_object($current_data) ){
            $duration = $current_data->updated->diff( new DateTime() );
        }
        if( (is_object($duration) && $duration->h > 0) || !is_object($current_data)){
            $data = file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=1277539&APPID=6e387a73833fca13ccd287b1e7e2aa50&units=metric");
            $wmpr->create([ "data" => $data , "updated" => new DateTime()]);
            $current_data = $wmpr->select()->order( ["updated"=>"DESC"] )->first();
        }

        $date_bali = new DateTime("now" ,new DateTimeZone( "Asia/Makassar" ) );
        $weather = json_decode( $current_data->data );

        return ["weather" => $weather->main , 
            "current_time" => $date_bali->format("l d F h:i a") ,
            $menu => true] ;

    }
    /**
    *   Función que sirve para añadir un usuario
    **/
    public function add($req,$res){
        if(isset($req->data["name"],$req->data["app"],$req->data["user"],$req->data["pass"],$req->data["pass2"])){
            $exito=true;
            //obtencion y sanitizacion de los datos
            $name=filter_var($req->data["name"], FILTER_SANITIZE_STRING);
            $app=filter_var($req->data["app"], FILTER_SANITIZE_STRING);
            $user1=filter_var($req->data["user"], FILTER_SANITIZE_STRING);
            $pass=filter_var($req->data["pass"], FILTER_SANITIZE_STRING);
            $pass2=filter_var($req->data["pass2"], FILTER_SANITIZE_STRING);
            $auser=array("nombre"=>$name,"app"=>$app,"user"=>$user1);
            //varificación de disponibilidad del usuario
            $usersMapper = $this->spot->mapper("Entity\Usuario");
            //buscamos el usuario
            $user = $usersMapper->where(["usuario" => $user1]);
            //si hay un registro, entonces el usuario no esta disponible
            if ($user->first()) {
                echo "
                        <script>
                            alert('Usuario no disponible, intente con otro usuario');
                        </script>
                    ";
                $exito=false;
            }
            //verificamos que las contraseñas coincidan
            else if($pass!=$pass2){
                echo "
                        <script>
                            alert('Las contraseñas no coinciden');
                        </script>
                    ";
                $exito=false;
            }
            //verificamos que la contraseña tenga al menos 6 caracteres
            else if(strlen($pass)<6){
                echo "
                        <script>
                            alert('La contraseña debe de tener al menos 6 caracter');
                        </script>
                    ";
                $exito=false;
            }
            //insertamos la entidad
            else{
                //creamos la entidad
                $entity = $usersMapper->build([
                    'nombre' => $name,
                    'papellido' => $app,
                    'usuario' =>$user1,
                    'password' =>$pass
                ]);
                //insertamos la entidad
                $result=$usersMapper->insert($entity);
               echo "
                        <script>
                            alert('Usuario Registrado');
                        </script>
                    ";
            }
            if($exito==true){
                echo $this->renderWiew([],$res);    
            }
            else{
                echo $this->renderWiew(array_merge(["user" => $auser]),$res);
            }
            
        }
        else{
            echo $this->renderWiew([],$res);
        }

    }
    /**
    *   Función que sirve para listar todos los usarios
    **/
    public function show($req,$res){
        $usersMapper=$this->spot->mapper("Entity\Usuario");
        $users=$usersMapper->select();
        echo $this->renderWiew(array_merge(["user"=>$users]),$res);        
    }
    /**
    *   Función que sirve para editar un usuario
    **/
    public function edit($req,$res){
        if($req->params["exper"]!=null){
            $userMapper=$this->spot->mapper("Entity\Usuario");
            $user = $userMapper->select()->where(["id" => $req->params["exper"]])->first();    
        }
        if(isset($req->data["name"],$req->data["app"],$req->data["user"])){
            $mensaje="";
            //obtencion y sanitizacion de datos
            $name = $req->data["name"]!=null? filter_var($req->data["name"], FILTER_SANITIZE_STRING) : $user->nombre;
            $app = $req->data["app"]!=null? filter_var($req->data["app"], FILTER_SANITIZE_STRING) : $user->papellido;
            $usr = $req->data["user"]!=null? filter_var($req->data["user"], FILTER_SANITIZE_STRING) : $user->usuario;
            //validacion de datos
            if($usr!=$user->usuario){
                $user2 = $userMapper->where(["usuario" => $usr]);
                if ($user2->first()==false) {
                    $user->usuario=$usr;
                }
                else{
                    $mensaje+="No se actualizo el usuario./nEl usuario no esta disponible./n";
                }
            }
            if($req->data["pass"]!=null && $req->data["pass2"]!=null){
                if($req->data["pass"]==$req->data["pass2"]&&strlen($req->data["pass"])>6){
                    $user->password=$req->data["pass"];
                }
                else{
                    $mensaje+="No se actualizo la contraseña./n Las contraseñas no coinciden ó tiene menos de 6 caracteres./n";
                }
            }
            if($mensaje!=""){
                echo "<script>alert(".$mensaje.");</script>";
            }
            else{
                echo "<script>alert(Registro Actualizado!);</script>";
            }
            $user->nombre=$name;
            $user->papellido=$app;
            $userMapper->update($user);
        }
        echo $this->renderWiew(array_merge(["user"=>$user]),$res);        
    }
    /**
    *   Función que sirve para eliminar un usuario
    **/
     public function delete($req,$res){
            //Obtenemos el id, de la experiencia a eleminar
            $var=$req->params["exper"];
            //Establecemos a spot con que entity class vamos a trabajar
            $userMapper=$this->spot->mapper("Entity\Usuario");
            //Seleccionamos la experiencia que este registrado para ese id
            $user = $userMapper->delete(['id ='=>(integer)$var]);  
            echo $this->renderWiew( array_merge([] , $this->header("/panel/user/show") ), $res);
            
    }
}

?>