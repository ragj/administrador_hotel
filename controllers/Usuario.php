<?php

/**
 *    Controlador de lugares
 *
 *
 */
//use CentroNotificaciones;
class Usuario extends Luna\Controller {

    public function login($req, $res) {
        $lang=$req->lang;
        switch($lang){
            case 'es':
                $res->m = $res->mustache->loadTemplate("Usuario/login_esp.mustache");
            break;
            case 'en':
                $res->m = $res->mustache->loadTemplate("Usuario/login.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Usuario/longin.mustache");
            break;
        }
        echo $this->renderWiew($this->header("login",$lang), $res);
    }
    public function logout($req, $res) {
        header("Location: http://" . $_SERVER["SERVER_NAME"] . "/login");
    }

    public function header( $menu,$lang ){
        $wmpr = $this->spot->mapper("Entity\Weather");
        $current_data = $wmpr->select()->order( ["updated"=>"DESC"] )->first();
        $duration = null;
        if( is_object($current_data) ){
            $duration = $current_data->updated->diff( new DateTime() );
        }
        if( (is_object($duration) && $duration->h > 0) || !is_object($current_data)){
            $data = file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=1277539&APPID=6e387a73833fca13ccd287b1e7e2aa50&units=metric");
            $wmpr->create([ "data" => $data , "updated" => new DateTime(),"zona_idzona"=>1]);
            $current_data = $wmpr->select()->order( ["updated"=>"DESC"] )->first();
        }
        $date_bali = new DateTime("now" ,new DateTimeZone( "Asia/Makassar" ) );
        $dat=$date_bali->format("l d F h:i a");
        switch($lang){
            case "es":
                //months
                $dat = str_replace('January','Enero',$dat);
                $dat = str_replace('February','Febrero',$dat);
                $dat = str_replace('March','Marzo',$dat);
                $dat = str_replace('April','Abril',$dat);
                $dat = str_replace('May','Mayo',$dat);
                $dat = str_replace('June','Junio',$dat);
                $dat = str_replace('July','Julio',$dat);
                $dat = str_replace('August','Agosto',$dat);
                $dat = str_replace('September','Septiembre',$dat);
                $dat = str_replace('October','Octubre',$dat);
                $dat = str_replace('November','Noviembre',$dat);
                $dat = str_replace('December','Diciembre',$dat);
                //days
                $dat = str_replace('Monday','Lunes',$dat);
                $dat = str_replace('Tuesday','Martes',$dat);
                $dat = str_replace('Wednesday','Miércoles',$dat);
                $dat = str_replace('Thursday','Jueves',$dat);
                $dat = str_replace('Friday','Viernes',$dat);
                $dat = str_replace('Saturday','Sábado',$dat);
                $dat = str_replace('Sunday','Domingo',$dat);
            break;
            case "en":
                $dat=$date_bali->format("l d F h:i a");
            break;
            default:
                $dat=$date_bali->format("l d F h:i a");
            break;
        }
        $weather = json_decode( $current_data->data );

        return ["weather" => $weather->main , 
            "current_time" => $dat,
            $menu => true] ;
    }
    /**
    *   Metodo que sirve para añadir un usuario
    **/
    public function add($req,$res){
        $rolMapper=$this->spot->mapper("Entity\Rols");
        $rols=$rolMapper->select();
        $zoneMapper=$this->spot->mapper("Entity\Zona");
        $zones=$zoneMapper->select();
        if(isset($req->data["name"],$req->data["app"],$req->data["user"],$req->data["pass"],$req->data["pass2"],$req->data["apm"],$req->data["tel"],$req->data["iata"],$req->data["member"],$req->data["years"],$req->data["rol"],$req->data["zone"])){
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
            $rol=$req->data["rol"];
            $zone=$req->data["zone"];
            //varificación de disponibilidad del usuario
            $usersMapper = $this->spot->mapper("Entity\Users");
            //buscamos el usuario
            $user = $usersMapper->where(["usuario" => $user1]);
            //si hay un registro, entonces el usuario no esta disponible
            if ($user->first()!=null) {
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
                    'years' => $years,
                    'activo'=>true,
                    'rols_idrols'=>$rol
                ]);
                //insertamos la entidad
                $result=$usersMapper->insert($entity);
                $userZonesMapper=$this->spot->mapper("Entity\UsersZona");
                $entity1=$userZonesMapper->build([
                        'users_id'=>$entity->id,
                        'zona_idzona'=>$zone
                    ]);
                $result=$userZonesMapper->insert($entity1);
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
                    "usr"=>$user1,
                    "tel"=>$tel,
                    "iata"=>$iata,
                    "miembro"=>$member,
                    "anios"=>$years);
                echo $this->renderWiew(array_merge(["user" => $auser,"rols"=>$rols,"zones"=>$zones]),$res);
            }
            
        }
        else{
            echo $this->renderWiew(array_merge(["rols"=>$rols,"zones"=>$zones]),$res);
        }

    }
    /**
    *   Metodo que sirve para listar todos los usarios
    **/
    public function show($req,$res){
        $usersMapper=$this->spot->mapper("Entity\Users");
        $users=$usersMapper->select();
        echo $this->renderWiew(array_merge(["user"=>$users]),$res);        
    }
    /**
    *   Metodo que sirve para editar un usuario
    **/
    public function edit($req,$res){
        if($req->params["exper"]!=null){
            $userMapper=$this->spot->mapper("Entity\Users");
            $user = $userMapper->select()->where(["id" => $req->params["exper"]])->with("zonas")->first();
            $rolsMapper=$this->spot->mapper("Entity\Rols");
            $rols=$rolsMapper->select();    
            $zoneMapper=$this->spot->mapper("Entity\Zona");
            $zones=$zoneMapper->select();
        }
        if(isset($req->data["name"],$req->data["app"],$req->data["email"])){
            
            $mensaje="";
            //obtencion y sanitizacion de datos
            $name = $req->data["name"]!=null? filter_var($req->data["name"], FILTER_SANITIZE_STRING) : $user->nombre;
            $app = $req->data["app"]!=null? filter_var($req->data["app"], FILTER_SANITIZE_STRING) : $user->papellido;
            $apm = $req->data["apm"]!=null? filter_var($req->data["apm"], FILTER_SANITIZE_STRING) : $user->mapellido;
            $usr = $req->data["email"]!=null? filter_var($req->data["email"], FILTER_SANITIZE_EMAIL) : $user->usuario;
            $tel = $req->data["tel"]!=null? filter_var($req->data["tel"], FILTER_SANITIZE_STRING) : $user->telefono;
            $iata = $req->data["iata"]!=null? filter_var($req->data["iata"], FILTER_SANITIZE_STRING) : $user->iata;
            $member = $req->data["member"]!=null? filter_var($req->data["member"], FILTER_SANITIZE_STRING) : $user->miembros;
            $years = $req->data["years"]!=null? filter_var($req->data["years"], FILTER_SANITIZE_STRING) : $user->years;
            if(isset($req->data["rol"])){
                $user->rols_idrols=$req->data["rol"];
            }
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
            $user->mapellido=$apm;
            $user->telefono=$tel;
            $user->iata=$iata;
            $user->miembros=$member;
            $user->years=$years;
            $user->activo=$active;
            $userMapper->update($user);
        }
        echo $this->renderWiew(array_merge(["user1"=>$user,"rols"=>$rols,"zones"=>$zones]),$res);        
    }
    /**
    *   Metodo que sirve para añadir zona al usuario
    **/
    public function addZone($req,$res){
        if(isset($req->data["zone"])){
            $userZonesMapper=$this->spot->mapper("Entity\UsersZona");
            $entity=$userZonesMapper->build([
                'users_id'=>$req->data["userid"],
                'zona_idzona'=>$req->data["zone"]
            ]);
            $result=$userZonesMapper->insert($entity);
            $userMapper=$this->spot->mapper("Entity\Users");
            $user = $userMapper->select()->where(["id" => $req->data["userid"]])->with("zonas")->first();
            $rolsMapper=$this->spot->mapper("Entity\Rols");
            $rols=$rolsMapper->select();    
            $zoneMapper=$this->spot->mapper("Entity\Zona");
            $zones=$zoneMapper->select();
            $res->m = $res->mustache->loadTemplate("Usuario/edit.mustache");
            echo $this->renderWiew(array_merge(["user1"=>$user,"rols"=>$rols,"zones"=>$zones]),$res);  
        }
        else{
            $userMapper=$this->spot->mapper("Entity\Users");
            $user = $userMapper->select()->where(["id" => $req->data["userid"]])->with("zonas")->first();
            $rolsMapper=$this->spot->mapper("Entity\Rols");
            $rols=$rolsMapper->select();    
            $zoneMapper=$this->spot->mapper("Entity\Zona");
            $zones=$zoneMapper->select();
            $res->m = $res->mustache->loadTemplate("Usuario/edit.mustache");
            echo $this->renderWiew(array_merge(["user1"=>$user,"rols"=>$rols,"zones"=>$zones]),$res);  
        }

    }
    /**
    *   Metodo que sireve para quitar zona al usuario
    **/
    public function deleteZone($req,$res){
        if(isset($req->params["zona"],$req->params["user"])){
            //eliminamos zona
            $userZonesMapper=$this->spot->mapper("Entity\UsersZona");
            $userzona=$userZonesMapper->delete(['users_id'=>$req->params["user"],'zona_idzona'=>$req->params["zona"]]);
            //obtenemos datos usuario actual y renderizamos editUser
            $userMapper=$this->spot->mapper("Entity\Users");
            $user = $userMapper->select()->where(["id" => $req->params["user"]])->with("zonas")->first();
            $rolsMapper=$this->spot->mapper("Entity\Rols");
            $rols=$rolsMapper->select();    
            $zoneMapper=$this->spot->mapper("Entity\Zona");
            $zones=$zoneMapper->select();
            $res->m = $res->mustache->loadTemplate("Usuario/edit.mustache");
            echo $this->renderWiew(array_merge(["user1"=>$user,"rols"=>$rols,"zones"=>$zones]),$res);  
        }
        else{
            ///mandamos para otro lado
            header("Location:/bali/panel/user/show");
        }
    }
    /**
    *   Metodo que sirve para eliminar un usuario
    **/
     public function delete($req,$res){
        //Obtenemos el id, del usuario a eleminar
        $var=$req->params["exper"];
        //Establecemos a spot con que entity class vamos a trabajar
        $userMapper=$this->spot->mapper("Entity\Users");
        $userZoneMapper=$this->spot->mapper("Entity\UsersZona");
        $forgotMapper=$this->spot->mapper("Entity\Forgot");
        //Eliminamos las zonas que el usuario tenga registradas
        $userzona=$userZoneMapper->delete(['users_id'=>$req->params["exper"]]);
        //Eliminamos los forgots echos por el usuario
        $forgot=$forgotMapper->delete(['users_id'=>$req->params["exper"]]);
        //Eliminamos el usuario que este registrado para ese id
        $user = $userMapper->delete(['id ='=>(integer)$var]);  
        echo $this->renderWiew( array_merge([]), $res);
            
    }
    /**
    *   Metodo que sirve para activar o desactivar un usuario
    **/
    public function active($req,$res){
        $userMapper=$this->spot->mapper("Entity\Users");
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