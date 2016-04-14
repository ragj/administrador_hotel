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
    *   Metodo que sirve para añadir un usuario
    **/
    public function add($req,$res){
        if(isset($req->data["name"],$req->data["app"],$req->data["user"],$req->data["pass"],$req->data["pass2"],$req->data["apm"],$req->data["tel"],$req->data["iata"],$req->data["member"],$req->data["years"])){
            $exito=true;
            //obtencion y sanitizacion de los datos
            $name=filter_var($req->data["name"], FILTER_SANITIZE_STRING);
            $app=filter_var($req->data["app"], FILTER_SANITIZE_STRING);
            $apm=filter_var($req->data["apm"], FILTER_SANITIZE_STRING);
            $user1=filter_var($req->data["user"], FILTER_SANITIZE_EMAIL);
            $pass=filter_var($req->data["pass"], FILTER_SANITIZE_STRING);
            $pass2=filter_var($req->data["pass2"], FILTER_SANITIZE_STRING);
            $tel=filter_var($req->data["tel"], FILTER_SANITIZE_STRING);
            $iata=filter_var($req->data["iata"], FILTER_SANITIZE_STRING);
            $member=filter_var($req->data["member"], FILTER_SANITIZE_STRING);
            $years=filter_var($req->data["years"], FILTER_SANITIZE_NUMBER_INT);
            //varificación de disponibilidad del usuario
            $usersMapper = $this->spot->mapper("Entity\Usuario");
            //buscamos el usuario
            $user = $usersMapper->where(["usuario" => $user1]);
            //si hay un registro, entonces el usuario no esta disponible
            if ($user->first()) {
                echo "
                        <script>
                            alert('User not available, please try another.');
                        </script>
                    ";
                $exito=false;
            }
            //verificamos que las contraseñas coincidan
            else if($pass!=$pass2){
                echo "
                        <script>
                            alert('Password do not match');
                        </script>
                    ";
                $exito=false;
            }
            //verificamos que la contraseña tenga al menos 6 caracteres
            else if(strlen($pass)<6){
                echo "
                        <script>
                            alert('The password must contain at least 6 characters.');
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
                    'mapellido' => $apm,
                    'usuario' => $user1,
                    'password' => $pass,
                    'telefono' => $tel,
                    'iata' => $iata,
                    'miembros' => $member,
                    'años' => $years,
                    'activo'=>true
                ]);
                //insertamos la entidad
                $result=$usersMapper->insert($entity);
               echo "
                        <script>
                            alert('user registered');
                        </script>
                    ";
            }
            if($exito==true){
                echo $this->renderWiew([],$res);    
            }
            else{
                $auser=array(
                    "nombres"=>$name,
                    "app"=>$app,
                    "apm"=>$apm,
                    "usr"=>$usr,
                    "tel"=>$tel,
                    "iata"=>$iata,
                    "miembro"=>$member,
                    "anios"=>$years);
                echo $this->renderWiew(array_merge(["user" => $auser]),$res);
            }
            
        }
        else{
            echo $this->renderWiew([],$res);
        }

    }
    /**
    *   Metodo que sirve para listar todos los usarios
    **/
    public function show($req,$res){
        $usersMapper=$this->spot->mapper("Entity\Usuario");
        $users=$usersMapper->select();
        echo $this->renderWiew(array_merge(["user"=>$users]),$res);        
    }
    /**
    *   Metodo que sirve para editar un usuario
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
            $apm = $req->data["apm"]!=null? filter_var($req->data["apm"], FILTER_SANITIZE_STRING) : $user->lapellido;
            $usr = $req->data["user"]!=null? filter_var($req->data["user"], FILTER_SANITIZE_EMAIL) : $user->usuario;
            $tel = $req->data["tel"]!=null? filter_var($req->data["tel"], FILTER_SANITIZE_STRING) : $user->telefono;
            $iata = $req->data["iata"]!=null? filter_var($req->data["iata"], FILTER_SANITIZE_STRING) : $user->iata;
            $member = $req->data["member"]!=null? filter_var($req->data["member"], FILTER_SANITIZE_STRING) : $user->miembros;
            $years = $req->data["years"]!=null? filter_var($req->data["years"], FILTER_SANITIZE_STRING) : $user->años;
            if(isset($req->data["esActivo"])){
                $active = true;
            }else{
                $active = false;#default value
            }
            //validacion de datos
            if($usr!=$user->usuario){
                $user2 = $userMapper->where(["usuario" => $usr]);
                if ($user2->first()==false) {
                    $user->usuario=$usr;
                }
                else{
                    $mensaje+="User was not uploaded./nThe user is not available./n";
                }
            }
            if($req->data["pass"]!=null && $req->data["pass2"]!=null){
                if($req->data["pass"]==$req->data["pass2"]&&strlen($req->data["pass"])>6){
                    $user->password=$req->data["pass"];
                }
                else{
                    $mensaje+="The password was not updated./n The passwords do not match or is less than 6 characters./n";
                }
            }
            if($mensaje!=""){
                echo "<script>alert(".$mensaje.");</script>";
            }
            else{
                echo "<script>alert(User Updated!);</script>";
            }
            $user->nombre=$name;
            $user->papellido=$app;
            $user->lapellido=$apm;
            $user->telefono=$tel;
            $user->iata=$iata;
            $user->miembros=$member;
            $user->años=$years;
            $user->activo=$active;
            $userMapper->update($user);
        }
        echo $this->renderWiew(array_merge(["user"=>$user]),$res);        
    }
    /**
    *   Metodo que sirve para eliminar un usuario
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
    /**
    *   Metodo que sirve para activar o desactivar un usuario
    **/
    public function active($req,$res){
        $userMapper=$this->spot->mapper("Entity\Usuario");
        if(isset($req->params["exper"])){
            $user=$userMapper->select()->where(["id"=>$req->params["exper"]])->first();
            if($user->activo==true){
                $user->activo=false;
            }
            else{
                $user->activo=true;
                $req->data["emailto"]=$user->usuario;
                $req->data["subject"]="Active Accout / Cuenta Activa";
                $req->data["nombre"]=$user->nombre;
                $req->data["papellido"]=$user->papellido;
                $template="Mail/active.mustache";
                $this->mailer( $res , $req , $template);
            }
            $userMapper->update($user);

        }
        $users=$userMapper->select();
        $res->m = $res->mustache->loadTemplate("Usuario/show.mustache");
        echo $this->renderWiew(array_merge(["user"=>$users]),$res);  
    }
}

?>