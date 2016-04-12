<?php

/**
 *    Controlador de paginas estaticas
 *
 *     code by zebadua
 */


class Plain extends Luna\Controller {

    public function home($req , $res){

        $tourMapper=$this->spot->mapper("Entity\Tour");
        $tour=$tourMapper->select()->where(["home"=>true])->order(['type' => 'DESC']);;
        echo $this->renderWiew(array_merge(["tour"=>$tour],$this->header("home")), $res);
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
    public function about($req , $res){
    	echo $this->renderWiew( $this->header("about"), $res);
    }

    public function aviso($req , $res){
        echo $this->renderWiew( $this->header("aviso"), $res);
    }
    public function hotel($req , $res){
        if(isset($req->params["hotel"] ) ){

            $tourMapper = $this->spot->mapper("Entity\Hotel");
            $hotel = $tourMapper->select()->with("images")->where(["uri" => $req->params["hotel"]])->first();
            $res->m = $res->mustache->loadTemplate("Plain/hotel-inner.mustache");
            echo $this->renderWiew( array_merge(["hotel-data" => $hotel] , $this->header("hotel") ), $res);
        }else{
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel=$hotelMapper->select()->with("images");
            echo $this->renderWiew( array_merge(["hotel-data"=>$hotel], $this->header("hotel")), $res);
        }
        
    }

    public function experience($req , $res){
        if(isset($req->params["exper"] ) ){

            $tourMapper = $this->spot->mapper("Entity\Tour");
            $tour = $tourMapper->select()->with("images")->where(["uri" => $req->params["exper"]])->first();
            $res->m = $res->mustache->loadTemplate("Plain/experience-inner.mustache");

            echo $this->renderWiew( array_merge(["tour" => $tour] , $this->header("experience") ), $res);
        }else{
            $tourMapper = $this->spot->mapper("Entity\Tour");
            $tours = $tourMapper->select()->with("images");
            echo $this->renderWiew( array_merge(["tours" => $tours] , $this->header("experience") ), $res);
        }
    	
    }

    public function transfer($req , $res){
    	echo $this->renderWiew( $this->header("transfer"), $res);
    }

    public function contact($req , $res){
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
        if(isset($req->data["usuario"],$req->data["email"])){
            $usersMapper = $this->spot->mapper("Entity\Usuario");
            //buscamos el usuario
            $user = $usersMapper->where(["usuario" => $req->data["usuario"]]);
            if ($user->first()) {
                //Generamos el forgot password
                $forgotMapper=$this->spot->mapper("Entity\Forgot");
                $entity = $forgotMapper->build([
                    'userid'=>$user->first()->id,
                    'email' =>$req->data["email"]
                ]);
                //insertamos la entidad
                $result=$forgotMapper->insert($entity);
                $to      = $req->data["email"];
                $subject = 'forgotten password';
                $message = "Please click on the following link to change your password.\r\n \r\n http://bali/bali/forgot/".$entity->uid;
                $headers = 'From: pruebasti@denumeris.com ' . "\r\n" .
                'Reply-To:'.$req->data["email"]. "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                 mail($to, $subject, $message, $headers);
            }
            else{
                echo "
                        <script>
                            alert('The user does not exist.');
                        </script>
                    ";
            }
        }
        echo $this->renderWiew($this->header("forgot"), $res);
    }
    public function change($req , $res){
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
                        echo "<script>alert('The passwords do not match.');</script>";
                        echo $this->renderWiew( array_merge(["user" => $user,"forgot"=>$forgot] , $this->header("change") ), $res);
                    }

                }
                else{
                    $userMapper=$this->spot->mapper("Entity\Usuario");
                    $user=$userMapper->where(["id"=>$forgot->first()->userid]);
                    echo $this->renderWiew( array_merge(["user" => $user,"forgot"=>$forgot] , $this->header("change") ), $res);
                }
            }
            else{
                echo "<script>
                    alert('The password has previously been updated');
                    function Redirect() {
                        window.location='/bali/login';
                    }
                    setTimeout('Redirect()', 0);
                </script>";
            }
        }
        
    }

    



    
}

?>