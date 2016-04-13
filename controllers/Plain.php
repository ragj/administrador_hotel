<?php

/**
 *    Controlador de paginas estaticas
 *
 *     code by zebadua
 */


class Plain extends Luna\Controller {
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
    public function home($req , $res){
        $lang="es";
        $tourMapper=$this->spot->mapper("Entity\Tour");
        $tour=$tourMapper->select()->where(["home"=>true])->order(['type' => 'DESC']);;
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/home_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/home.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/home.mustache");
            break;
        }
        echo $this->renderWiew(array_merge(["tour"=>$tour],$this->header("home")), $res);
    }
    public function about($req , $res){
        $lang="es";
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/about_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/about.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/about.mustache");
            break;
        }
    	echo $this->renderWiew( $this->header("about"), $res);
    }

    public function aviso($req , $res){
        $lang="es";
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/aviso.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/aviso.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/aviso.mustache");
            break;
        }
        echo $this->renderWiew( $this->header("aviso"), $res);
    }
    public function hotel($req , $res){
        $lang="es";
        if(isset($req->params["hotel"] ) ){
            switch($lang){
                case "es":
                    $res->m = $res->mustache->loadTemplate("Plain/hotel-inner_esp.mustache");
                break;
                case "en":
                    $res->m = $res->mustache->loadTemplate("Plain/hotel-inner.mustache");
                break;
                default:
                    $res->m = $res->mustache->loadTemplate("Plain/hotel-inner.mustache");
                break;
            }
            $tourMapper = $this->spot->mapper("Entity\Hotel");
            $hotel = $tourMapper->select()->with("images")->where(["uri" => $req->params["hotel"]])->first();
            echo $this->renderWiew( array_merge(["hotel-data" => $hotel] , $this->header("hotel") ), $res);
        }else{
            switch($lang){
                case "es":
                    $res->m = $res->mustache->loadTemplate("Plain/hotel_esp.mustache");
                break;
                case "en":
                    $res->m = $res->mustache->loadTemplate("Plain/hotel.mustache");
                break;
                default:
                    $res->m = $res->mustache->loadTemplate("Plain/hotel.mustache");
                break;
            }
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel=$hotelMapper->select()->with("images");
            echo $this->renderWiew( array_merge(["hotel-data"=>$hotel], $this->header("hotel")), $res);
        }
        
    }

    public function experience($req , $res){
        $lang="es";
        if(isset($req->params["exper"] ) ){
            switch($lang){
                case "es":
                    $res->m = $res->mustache->loadTemplate("Plain/experience-inner_esp.mustache");
                break;
                case "en":
                    $res->m = $res->mustache->loadTemplate("Plain/experience-inner.mustache");
                break;
                default:
                    $res->m = $res->mustache->loadTemplate("Plain/experience-inner.mustache");
                break;
            }
            $tourMapper = $this->spot->mapper("Entity\Tour");
            $tour = $tourMapper->select()->with("images")->where(["uri" => $req->params["exper"]])->first();
            echo $this->renderWiew( array_merge(["tour" => $tour] , $this->header("experience") ), $res);
        }else{
            switch($lang){
                case "es":
                    $res->m = $res->mustache->loadTemplate("Plain/experience_esp.mustache");
                break;
                case "en":
                    $res->m = $res->mustache->loadTemplate("Plain/experience.mustache");
                break;
                default:
                    $res->m = $res->mustache->loadTemplate("Plain/experience.mustache");
                break;
            }
            $tourMapper = $this->spot->mapper("Entity\Tour");
            $tours = $tourMapper->select()->with("images");
            echo $this->renderWiew( array_merge(["tours" => $tours] , $this->header("experience") ), $res);
        }
    	
    }

    public function transfer($req , $res){
        $lang="es";
        $transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
        $transfer=$transferBlockMapper->select()->with("detail");
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/transfer_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/transfer.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/transfer.mustache");
            break;
        }
    	echo $this->renderWiew( array_merge(["transferBlock" => $transfer] , $this->header("transfer") ), $res);
    }

    public function contact($req , $res){
        $lang="es";
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/contact_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/contact.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/contact.mustache");
            break;
        }
        if(isset($req->data["name"],$req->data["email"],$req->data["message"])){
            $to      = 'pruebasti@denumeris.com ';
            $subject = 'contacto desde pagina';
            $message = "Nombre: ".$req->data["name"]."\r\n Mensaje:".$req->data["message"];
            $headers = 'From: pruebasti@denumeris.com ' . "\r\n" .
                'Reply-To:'.$req->data["email"]. "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($to, $subject, $message, $headers);
            $contactMapper=$this->spot->mapper("Entity\Contact");
            //construimos la entidad
            $entity = $contactMapper->build([
                'nombre' => $req->data["name"],
                'email' => $req->data["email"],
                'mensaje' =>$req->data["message"]
            ]);
            $result=$contactMapper->insert($entity);
        }
    	echo $this->renderWiew( $this->header("contact"), $res);
    }
    public function forgot($req , $res){
        $lang="es";
        if(isset($req->data["usuario"])){
            $usersMapper = $this->spot->mapper("Entity\Usuario");
            //buscamos el usuario
            $user = $usersMapper->where(["usuario" => $req->data["usuario"]]);
            if ($user->first()) {
                //Generamos el forgot password
                $forgotMapper=$this->spot->mapper("Entity\Forgot");
                $entity = $forgotMapper->build([
                    'userid'=>$user->first()->id,
                    'email' =>$req->data["usuario"]
                ]);
                //insertamos la entidad
                $result=$forgotMapper->insert($entity);
                //generamos mensaje en base al idioma
                switch($lang){
                    case "es":
                        $subject = 'Contraseña Olvidada';
                        $message = "Por favor haga click en el siguiente link para cambiar tu contraseña\r\n \r\n http://bali/bali/es/forgot/".$entity->uid;  
                    break;
                    case "en":
                        $subject = 'Forgotten Password';
                        $message = "Please click on the following link to change your password.\r\n \r\n http://bali/bali/en/forgot/".$entity->uid;
                    break;
                    default:
                        $subject = 'Forgotten Password';
                        $message = "Please click on the following link to change your password.\r\n \r\n http://bali/bali/en/forgot/".$entity->uid;
                    break;
                }
                $to      = $req->data["usuario"];
                $headers = 'From: pruebasti@denumeris.com ' . "\r\n" .
                'Reply-To:'.$req->data["usuario"]. "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                //mandamos mensaje
                mail($to, $subject, $message, $headers);
            }
            else{
                 switch($lang){
                    case "es":
                        echo "<script>alert('El usuario no existe.');</script>"; 
                    break;
                    case "en":
                        echo "<script>alert('The user does not exist.');</script>";
                    break;
                    default:
                        echo "<script>alert('The user does not exist.');</script>";
                    break;
                }
                
            }
        }
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/forgot_esp.mustache");
            break;
            case "en":
               $res->m = $res->mustache->loadTemplate("Plain/forgot.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/forgot.mustache");
            break;
        }
        echo $this->renderWiew($this->header("forgot"), $res);
    }
    public function change($req , $res){
        $lang="es";

        $id=$req->params["uid"];
        $forgotMapper=$this->spot->mapper("Entity\Forgot");
        $forgot=$forgotMapper->where(["uid"=>$id]);
        $userMapper=$this->spot->mapper("Entity\Usuario");
        $user=$userMapper->where(["id"=>$forgot->first()->userid]);
        //si existe un registro con ese uid
        if($forgot->first()){
            //si no ha sido cambiada esa password con esa peticion
            if($forgot->first()->usado==false){
                //si las contraseñas son iguales, actualizamos la contraseña y actualizamos el estado del forgot password
                if(isset($req->data["pass1"],$req->data["pass2"])){
                    if($req->data["pass1"]==$req->data["pass2"]){ 
                        ///seteamos entidades
                        $forgotMapper=$this->spot->mapper("Entity\Forgot");
                        $userMapper=$this->spot->mapper("Entity\Usuario");
                        //seleccionamos elementos
                        $forgot=$forgotMapper->where(["uid"=>$id])->first();
                        $user=$userMapper->where(["id"=>$forgot->userid])->first();
                        //cambiamos valores
                        $forgot->usado=true;
                        $user->password=$req->data["pass1"];
                        //actualizamos entidades
                        $forgotMapper->update($forgot);
                        $userMapper->update($user);
                        //logueamos
                        header('Location: http://bali/login?usuario='.$user->usuario.'&password='.$req->data["pass1"]);
                        exit;

                    }
                    else{
                        switch($lang){
                            case "es":
                                $res->m = $res->mustache->loadTemplate("Plain/change_esp.mustache");
                                echo "<script>alert('Las contraseñas no coinciden.');</script>";
                            break;
                            case "en":
                               $res->m = $res->mustache->loadTemplate("Plain/change.mustache");
                               echo "<script>alert('The passwords do not match.');</script>";
                            break;
                            default:
                                $res->m = $res->mustache->loadTemplate("Plain/change.mustache");
                                echo "<script>alert('The passwords do not match.');</script>";
                            break;
                        }
                        echo $this->renderWiew( array_merge(["user" => $user,"forgot"=>$forgot] , $this->header("change") ), $res);
                    }
                }
                else{
                    $userMapper=$this->spot->mapper("Entity\Usuario");
                    $user=$userMapper->where(["id"=>$forgot->first()->userid]);
                    switch($lang){
                        case "es":
                            $res->m = $res->mustache->loadTemplate("Plain/change_esp.mustache");
                        break;
                        case "en":
                           $res->m = $res->mustache->loadTemplate("Plain/change.mustache");
                        break;
                        default:
                            $res->m = $res->mustache->loadTemplate("Plain/change.mustache");
                        break;
                    }
                    echo $this->renderWiew( array_merge(["user" => $user,"forgot"=>$forgot] , $this->header("change") ), $res);
                }
            }
            else{
                switch($lang){
                    case "es":
                        echo "<script>alert('La contraseña ha sido cambiada previamente.');function Redirect() {window.location='/bali/login';}setTimeout('Redirect()', 0);</script>";
                    break;
                    case "en":
                       echo "<script>alert('The password has previously been updated');function Redirect() {window.location='/bali/login';}setTimeout('Redirect()', 0);</script>";
                    break;
                    default:
                        echo "<script>alert('The password has previously been updated');function Redirect() {window.location='/bali/login';}setTimeout('Redirect()', 0);</script>";
                    break;
                }
                
            }
        }
        
    }
    public function register($req,$res){
        $lang="es";
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/register_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/register.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/register.mustache");
            break;
        }
        if(isset($req->data["name"],$req->data["lname"],$req->data["mlname"],$req->data["user"],$req->data["pass"],$req->data["pass1"],$req->data["phone"],$req->data["iata"],$req->data["member"],$req->data["years"])){
            //verificacion de que el usuario no exista
            $userMapper=$this->spot->mapper("Entity\Usuario");
            $user=$userMapper->select()->where(["usuario"=>$req->data["user"]]);
            $exito=true;
            if($user->first()){
                switch($lang){
                    case "es":
                        echo "<script>alert('Usuario no disponible, por favor intente con otro.');</script>";
                    break;
                    case "en":
                        echo "<script>alert('User not available, please try another.');</script>";
                    break;
                    default:
                        echo "<script>alert('User not available, please try another.');</script>";
                    break;
                }
                $exito=false;
            }
            //verificamos que las contraseñas sean iguales
            else if($req->data["pass"]!=$req->data["pass1"]){
                switch($lang){
                    case "es":
                        echo "<script>alert('Las contraseñas no coinciden.');</script>";
                    break;
                    case "en":
                        echo "<script>alert('Passwords do not match.');</script>";
                    break;
                    default:
                        echo "<script>alert('Passwords do not match.');</script>";
                    break;
                }
                $exito=false;
            }
            //verificamos que la contraseña tenga al menos 6 caracteres
            else if(strlen($req->data["pass"])<6){
                switch($lang){
                    case "es":
                        echo "<script>alert('La contraseña debe tener al menos 6 caracteres.');</script>";
                    break;
                    case "en":
                        echo "<script>alert('The password must contain at least 6 characters.');</script>";
                    break;
                    default:
                        echo "<script>alert('The password must contain at least 6 characters.');</script>";
                    break;
                }
                $exito=false;
            }
            //si todo lo anterior quedo bien, entonces creamos la entidad e insertamos
            else{
                $entity = $usersMapper->build([
                    'nombre' => $req->data["name"],
                    'papellido' => $req->data["lname"],
                    'mapellido' => $req->data["mlname"],
                    'usuario' => $req->data["user"],
                    'password' => $req->data["pass"],
                    'telefono' => $req->data["phone"],
                    'iata' => $req->data["iata"],
                    'miembros' => $req->data["member"],
                    'años' => $req->data["years"]
                ]);
                $result=$usersMapper->insert($entity);
                switch($lang){
                    case "es":
                        echo "<script>alert('Te has registrado correctamente. Tendras que esperar a que validen tu cuenta.');</script>";
                    break;
                    case "en":
                        echo "<script>alert('You have successfully signed up. You'll have to wait until we validate your account.');</script>";
                    break;
                    default:
                        echo "<script>alert('You have successfully signed up. You'll have to wait until we validate your account.');</script>";
                    break;
                }
            }
            if($exito){
                echo $this->renderWiew($this->header("register"), $res);
            }
            else{
                $auser=array("nombre"=>$req->data["name"],"app"=>$req->data["lname"],"apm"=>$req->data["mlname"],"usr"=>$req->data["user"],"tel"=>$req->data["phone"],"iata"=>$req->data["iata"],"miembro"=>$req->data["member"],"anios"=>$req->data["years"]);
                echo $this->renderWiew(array_merge(["user" => $auser]),$res);
            }
        }
        echo $this->renderWiew($this->header("register"), $res);
    }    
}

?>