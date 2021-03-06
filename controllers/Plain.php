<?php

/**
 *    Controlador de paginas estaticas
 *
 *     code by zebadua
 */

;
class Plain extends Luna\Controller {
    public function notFound($req,$res){
     
        $lang=$req->lang;
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/404_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/404.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/404.mustache");
            break;
        }
        
        echo $this->renderWiew($this->header("notFound",$lang), $res);
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

    public function home($req , $res){
        $lang=$req->lang;
        $tourMapper=$this->spot->mapper("Entity\Experience");
        $tour=$tourMapper->select()->where(["home"=>true])->where(["oculto"=>false])->with("type")->order(['type_idtype' => 'DESC']);
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
        echo $this->renderWiew(array_merge(["tour"=>$tour],$this->header("home",$lang)), $res);
    }
    public function about($req , $res){
        $lang=$req->lang;
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
    	echo $this->renderWiew( $this->header("about",$lang), $res);
    }
    public function transfers($req , $res){
        $lang=$req->lang;
        $vehicleMapper=$this->spot->mapper("Entity\Vehicle");
        $vehicles=$vehicleMapper->select()->with("images");
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/tranfers_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/tranfers.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/tranfers.mustache");
            break;
        }
        echo $this->renderWiew( array_merge(["cars"=>$vehicles],$this->header("transfers",$lang)), $res);
    }
    public function aviso($req , $res){
        $lang=$req->lang;
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
        echo $this->renderWiew( $this->header("aviso",$lang), $res);
    }

    public function hotel($req , $res){
        $lang=$req->lang;
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
            $hotelImageMapper=$this->spot->mapper("Entity\HotelImage");
            $params = [($req->lang=="es"?"uri_es":"uri") => $req->params["hotel"]];
            $hotel = $tourMapper->select()->where($params)->first();
            $aux=$hotel->toArray();
            $images=$hotelImageMapper->select()->where(['hotel_idhotel'=>$aux['idhotel'],'tipo'=>"I"]);
            $video=$hotelImageMapper->select()->where(['hotel_idhotel'=>$aux['idhotel'],'tipo'=>"V"]);
            $videoID=array();
            foreach ($video->toArray() as $v) {
                $parse=explode('/embed/',$v["path"]);
                $arrayName = array('videoID' =>substr($parse[1],0, strpos($parse[1],'"')),'path'=>$v["path"]);
                array_push($videoID,$arrayName);
            }
            // $uriHandler => array ( uri , mapper => "{param}" )
            if( $hotel ){
                $this->hotel = $hotel;

                $translate = new stdClass();
                $translate->call = function( $lang ){
                    $uri = $lang=="es"?$this->hotel->uri_es:$this->hotel->uri;
                    return Luna\Translate::to( $lang , ["uri"=> $uri, "mapper"=>"{hotel}"]);
                };

                echo $this->renderWiew( array_merge(["translate"=>$translate,"hotel-data" => $hotel,"images"=>$images,"videos"=>$videoID] , $this->header("hotel",$lang) ), $res);    
            }
            else{
                header('Location:'.Luna\Translate::url("/holtel-collection"));
            }
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
            $hotel=$hotelMapper->select()->where(["zona_idzona"=>1])->where(["oculto"=>false]);
            echo $this->renderWiew( array_merge(["hotel-data"=>$hotel], $this->header("hotel",$lang)), $res);
        }
        
    }

    public function experience($req , $res){
        $lang=$req->lang;
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
            $texperienceMapper=$this->spot->mapper("Entity\Texperience");
            $rate=$texperienceMapper->select()->first();
            $tourMapper = $this->spot->mapper("Entity\Experience");
            $params = [($req->lang=="es"?"uri_es":"uri") => $req->params["exper"]];
            $tour = $tourMapper->select()->with("images")->where($params)->first();
            // $uriHandler => array ( uri , mapper => "{param}" )
            if( $tour ){
                $this->tour = $tour;
                $translate = new stdClass();
                $translate->call = function( $lang ){
                    $uri = $lang=="es"?$this->tour->uri_es:$this->tour->uri; 
                    return Luna\Translate::to( $lang , ["uri"=> $uri, "mapper"=>"{exper}"]);
                };
                echo $this->renderWiew( array_merge(["translate"=>$translate,"tour" => $tour,"rate"=>$rate] , $this->header("experience",$lang) ), $res);    
            }
            else{
                header('Location:'.Luna\Translate::url("/experience"));
            }
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
            $tourMapper = $this->spot->mapper("Entity\Experience");
            $tours = $tourMapper->select()->with("type")->where(["zona_idzona"=>1])->where(["oculto"=>false]);
            echo $this->renderWiew( array_merge(["tours" => $tours] , $this->header("experience",$lang) ), $res);
        }
    	
    }

    public function transfer($req , $res){
        $lang=$req->lang;
        //obtencion de hoteles
        $hotelMapper=$this->spot->mapper("Entity\hotelTransfer");
        $hotelTransfer=$hotelMapper->select()->with("hotel");
        //obtencion de vehiculos
        $carMapper=$this->spot->mapper("Entity\Vehicle");
        $car=$carMapper->select()->with("passengers");
        $aux=$car->toArray();
        $cars=array();
        foreach ($aux as $c) {
            $aux2=array('size' => sizeof($c["passengers"]));
            $aux2=array_merge($c,$aux2);
            array_push($cars,$aux2);
        }
        //obtencion de transfers de traslados
        $transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
        $transfer=$transferBlockMapper->select()->with(["detail"])->where(["zona_idzona"=>1])->where(["tipo"=>"T"]);
        $aux=$transfer->toArray();
        //obtencion de valores de Transfers Transfers
        $Tdetails=array();
        foreach ($aux as $value) {
            foreach ($value["detail"] as $val) {
                $id=$val["idtransferDetail"];
                $den=$val["description"];
                $des=$val["description_esp"];
                $valuesMapper=$this->spot->mapper("Entity\TransferValue");
                $values=$valuesMapper->select()->where(["transferDetail_idtransferDetail"=>$id])->order(['vp_id'=>'ASC'])->toArray();
                $aux2 = array('den' =>$den ,'des'=>$des,'values'=>$values);
                array_push($Tdetails,$aux2);
            }
        }
        $transfer=$transferBlockMapper->select()->with(["detail"])->where(["zona_idzona"=>1])->where(["tipo"=>"E"]);
        $aux=$transfer->toArray();
        //obtencion de valores de transfers Experiencias
        $Edetails=array();
        foreach ($aux as $value) {
            foreach ($value["detail"] as $val) {
                $id=$val["idtransferDetail"];
                $den=$val["description"];
                $des=$val["description_esp"];
                $valuesMapper=$this->spot->mapper("Entity\TransferValue");
                $values=$valuesMapper->select()->where(["transferDetail_idtransferDetail"=>$id])->order(['vp_id'=>'ASC'])->toArray();
                $aux2 = array('den' =>$den ,'des'=>$des,'values'=>$values);
                array_push($Edetails,$aux2);
            }
        }
        
        
        /*//obtencion de detalles con valores de los ids de los transfers blocks
        $detailMapper=$this->spot->mapper("Entity\TransferDetail");
        $detailValues=$detailMapper->select()->with(["transferValue"])->where(["transferBlock_idtransferBlock"=>$ids[0]])->where(["oculto"=>false])->order(['description' => 'DESC'])->toArray();
        //construccion array perzonalizado para la vista necesaria
        $pers=Array();
        
        $title["en"]=$aux[0]["TransferBlockTitle"];
        $title["es"]=$aux[0]["TransferBlockTitle_es"];
        $title["tipo"]=$aux[0]["tipo"];
        $title["detalle"]=array();
        foreach ($detailValues as $detail) {
            if($aux[0]["idtransferBlock"]==$detail["transferBlock_idtransferBlock"]){
                $subtitle["en"]=$detail["description"];
                $subtitle["es"]=$detail["description_esp"];
                $subtitle["vals"]=$detail["transferValue"];
                array_push($title["detalle"],$subtitle);
            }    
        }
        array_push($pers,$title);
        $detailValues=$detailMapper->select()->with(["transferValue"])->where(["transferBlock_idtransferBlock"=>$ids[1]])->where(["oculto"=>false])->order(['description' => 'DESC'])->toArray();
        $exps=array();
        $title["en"]=$aux[1]["TransferBlockTitle"];
        $title["es"]=$aux[1]["TransferBlockTitle_es"];
        $title["tipo"]=$aux[1]["tipo"];
        $title["detalle"]=array();
        foreach ($detailValues as $detail) {
            if($aux[1]["idtransferBlock"]==$detail["transferBlock_idtransferBlock"]){
                $subtitle["en"]=$detail["description"];
                $subtitle["es"]=$detail["description_esp"];
                $subtitle["vals"]=$detail["transferValue"];
                array_push($title["detalle"],$subtitle);
            }    
        }
        array_push($exps,$title);
        */
        $texperienceMapper=$this->spot->mapper("Entity\Texperience");
        $rate=$texperienceMapper->select()->first();
        $tMapper=$this->spot->mapper("Entity\Ratetransfer");
        $trate=$tMapper->select()->first();
    
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
    	echo $this->renderWiew( array_merge(["car"=>$cars,"ttransfer"=>$Tdetails,"texperience"=>$Edetails,"experRate"=>$rate,"tRate"=>$trate,"hotelBlock"=>$hotelTransfer], $this->header("transfer",$lang) ), $res);
    }

    public function contact($req , $res){
        $lang=$req->lang;
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
            $template="Mail/contacto.mustache";
            
            $req->data["subject"]="Contacto desde pagina";
            //Enviamos el primer correo
            $req->data["emailto"]=$this->cmail;
            $this->mailer( $res , $req , $template);
            //Enviamos el segundo correo
            $req->data["emailto"]=$this->ccmail;
            $this->mailer( $res , $req , $template);
            $contactMapper=$this->spot->mapper("Entity\Contact");
            //construimos la entidad
            $entity = $contactMapper->build([
                'nombre' => $req->data["name"],
                'email' => $req->data["email"],
                'mensaje' =>$req->data["message"],
                'zona_idzona'=>1
            ]);
            $result=$contactMapper->insert($entity);                
        }
    	echo $this->renderWiew( $this->header("contact",$lang), $res);
    }
    public function inquiere($req , $res){
        $lang=$req->lang;
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/request_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/request.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/request.mustache");
            break;
        }
        $hotelMapper=$this->spot->mapper("Entity\Hotel");
        $hotels=$hotelMapper->select()->where(["zona_idzona"=>1])->where(["oculto"=>false]);
        $transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
        $transfers=$transferBlockMapper->select()->with("detail")->where(["zona_idzona"=>1]);
        $experienceMapper=$this->spot->mapper("Entity\Experience");
        $experiences=$experienceMapper->select()->where(["zona_idzona"=>1])->where(["oculto"=>false]);
            

        if(isset($req->data["hotel"]) || isset($req->data["transfer"])|| isset($req->data["exper"]) ){
            $TransferDetailBlock=$this->spot->mapper("Entity\TransferDetail");
            //verificamos si hay algo en comments de lo contrario lo ponemos en nulo
            $comments=isset($req->data["comments"])?$req->data["comments"]:null;
            //creamos entidad request
            $requestMapper=$this->spot->mapper("Entity\Request");
            $request=$requestMapper->build([
                'comment'=>$comments,
                'users_usersid'=>$req->user["id"]
            ]);
            $requestMapper->insert($request);
            //obtenemos el id del request actual insertado
            $idr=$request->idrequest;
            //si hotel fue añadido procedemos a la obtencion de sus datos e insercion en la base de datos, de lo contrario no hacemos nada            
            $requested_hotels=array();
            if(isset($req->data["hotel"])){
                $requestHotelMapper=$this->spot->mapper("Entity\RequestHotel");
                foreach ($req->data["hotel"] as $hotel) {
                    $rhotel=$requestHotelMapper->build([
                        'arrival_date'=>$hotel["aaday"],
                        'departure_date'=>$hotel["dday"],
                        'request_idrequest'=>$idr,
                        'hotel_idhotel'=>$hotel["idhotel"]
                    ]);
                    $requestHotelMapper->insert($rhotel);
                    $hot=$hotelMapper->select()->where(["idhotel"=>$hotel["idhotel"]])->first();
                    $arr=$hotel["aaday"];
                    $dday=$hotel["dday"];
                    $aux = array('name' =>$hot->name ,'arrival'=>$arr,'departure'=>$dday);
                    array_push($requested_hotels,$aux);
                }
            }
            else{
                $requested_hotels=null;
            }
            $requested_transfers=array();
           
            if(isset($req->data["transfer"])){
                $requestTransferDMapper=$this->spot->mapper("Entity\RequestTransferD");
                $vehicleMapper=$this->spot->mapper("Entity\Vehicle");
                foreach($req->data["transfer"] as $trans){
                    if($trans['idtransferDetail']!=''){
                        $vehicle=$vehicleMapper->select()->where(["name"=>$trans['vehicle']])->first();
                        $rtransfer=$requestTransferDMapper->build([
                            'wanted_date'=>$trans['tday'],
                            'peaple'=>$trans['passanger'],
                            'request_idrequest'=>$idr,
                            'transferDetail_idtransferDetail'=>$trans['idtransferDetail'],
                            'vehicle_idvehicle'=>$vehicle->idVehicle
                        ]);
                        $requestTransferDMapper->insert($rtransfer);
                        $transferDetail=$TransferDetailBlock->select()->where(["idtransferDetail"=>$trans['idtransferDetail']])->first();
                        $aux=array('transfer' =>$transferDetail->description,'vehicle'=>$trans['vehicle'],'passenger'=>$trans['passanger'],'fecha'=>$trans['tday']);
                        array_push($requested_transfers,$aux);
                    }
                }  
            }
            else{
                $requested_transfers=null;
            }
            $requested_experiences=array();
            if(isset($req->data["exper"])){
                $requestExperience=$this->spot->mapper("Entity\RequestExperience");
                foreach ($req->data["exper"] as $exp) {
                    if($exp!=''){
                        $rexperience=$requestExperience->build([
                            'wanted_date'=>$exp['date'],
                            'peaple'=>$exp['passanger'],
                            'request_idrequest'=>$idr,
                            'experience_idexperience'=>$exp['idexperience']
                        ]);
                        $requestExperience->insert($rexperience);
                        $expe=$experienceMapper->select()->where(["idexperience"=>$exp['idexperience']])->first();
                        $aux=array('experience'=>$expe->title,"tipo"=>$expe->duration,"fecha"=>$exp['date']);
                        array_push($requested_experiences, $aux);
                    }
                }
            }

            else{
                $requested_experiences=null;
            }
            switch($lang){
                    case "es":
                        $req->data["subject"] = 'Contacto Travel Agent';
                        $template="Mail/tagent_esp.mustache";
                        $des="Location:/bali/es/travelAgent";
                    break;
                    case "en":
                        $req->data["subject"] = 'Contact Travel Agent';
                        $template="Mail/tagent.mustache";
                        $des="Location:/bali/en/travelAgent";
                    break;
                    default:
                        $req->data["subject"] = 'Contact Travel Agent';
                        $template="Mail/tagent.mustache"; 
                        $des="Location:/bali/en/travelAgent";              
                    break;
            }
            
            $req->data["hotel"]=$requested_hotels;
            $req->data["transfer"]=$requested_transfers;
            $req->data["experience"]=$requested_experiences;

            
            $req->data["comments"]=($comments );
            $req->data["agent"]=$req->user;
            $req->data["emailto"]=$this->cmail;
            //mandamos mensaje
            $this->mailer( $res , $req , $template);
            $req->data["emailto"]=$this->ccmail;
            $this->mailer( $res , $req , $template);
            //header($des);
            switch($lang){
                case "es":
                    $res->m = $res->mustache->loadTemplate("Plain/requesta_esp.mustache");
                break;
                case "en":
                    $res->m = $res->mustache->loadTemplate("Plain/requesta.mustache");
                break;
                default:
                    $res->m = $res->mustache->loadTemplate("Plain/requesta.mustache");
                break;
            }

        }
        echo $this->renderWiew(array_merge(["hoteles"=>$hotels,"transfersd"=>$transfers,"experiences"=>$experiences],$this->header("transfer",$lang)), $res);
    }
    public function forgot($req , $res){
        $lang=$req->lang;
        if(isset($req->data["usuario"])){
            $usersMapper = $this->spot->mapper("Entity\Users");
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
                        $req->data["subject"] = 'Contraseña Olvidada';
                        $req->data["message"] = "http:".$_SERVER['HTTP_HOST']."/bali/es/forgot/".$entity->uid;
                        $template="Mail/forgot_esp.mustache";
                    break;
                    case "en":
                        $req->data["subject"] = 'Forgotten Password';
                        $req->data["message"] = "http:".$_SERVER['HTTP_HOST']."/bali/en/forgot/".$entity->uid;
                        $template="Mail/forgot.mustache";
                    break;
                    default:
                        $req->data["subject"] = 'Forgotten Password';
                        $req->data["message"] = "http:".$_SERVER['HTTP_HOST']."/bali/en/forgot/".$entity->uid;
                        $template="Mail/forgot.mustache";               
                    break;
                }
                $req->data["emailto"]=$req->data["usuario"];
                $req->data["nombre"]=$user->first()->nombre." ".$user->first()->papellido;
                //mandamos mensaje
                $this->mailer( $res , $req , $template);
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
        echo $this->renderWiew($this->header("forgot",$lang), $res);
    }
    public function change($req , $res){
        $lang=$req->lang;

        $id=$req->params["uid"];
        $forgotMapper=$this->spot->mapper("Entity\Forgot");
        $forgot=$forgotMapper->where(["uid"=>$id]);
        $userMapper=$this->spot->mapper("Entity\Users");
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
                        $userMapper=$this->spot->mapper("Entity\Users");
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
                        header('Location: http://admin_lozano/login?usuario='.$user->usuario.'&password='.$req->data["pass1"]);
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
                        echo $this->renderWiew( array_merge(["user" => $user,"forgot"=>$forgot] , $this->header("change",$lang) ), $res);
                    }
                }
                else{
                    $userMapper=$this->spot->mapper("Entity\Users");
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
                    echo $this->renderWiew( array_merge(["user" => $user,"forgot"=>$forgot] , $this->header("change",$lang) ), $res);
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
        $lang=$req->lang;
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
            $usersMapper=$this->spot->mapper("Entity\Users");
            $user=$usersMapper->select()->where(["usuario"=>$req->data["user"]]);
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
                    'rols_idrols' => '1',
                    'years' => $req->data["years"]
                ]);
                $result=$usersMapper->insert($entity);
                
                $usersZonesMapper=$this->spot->mapper("Entity\UsersZona");
                $zona=$usersZonesMapper->build([
                    'users_id'=>$entity->id,
                    'zona_idzona'=>1
                    ]);
                $result1=$usersZonesMapper->insert($zona);
                switch($lang){
                    case "es":
                        $template="Mail/welcome_esp.mustache";
                        $req->data["subject"]="Bienvenido a Lozano Travel";
                        echo "<script>alert('Te has registrado correctamente. Tendras que esperar a que validen tu cuenta.');</script>";
                    break;
                    case "en":
                        $template="Mail/welcome.mustache";
                        $req->data["subject"]="Welcome to Lozano Travel";
                        echo "<script>alert('You have successfully signed up. You'll have to wait until we validate your account.');</script>";
                    break;
                    default:
                        $template="Mail/welcome.mustache";
                        $req->data["subject"]="Welcome to Lozano Travel";
                        echo "<script>alert('You have successfully signed up. You'll have to wait until we validate your account.');</script>";
                    break;
                }
            }
            if($exito){
                $req->data["emailto"]=$req->data["user"];
                $this->mailer( $res , $req , $template);
                //Enviamos el primer correo
                $req->data["emailto"]=$this->cmail;
                $this->mailer( $res , $req , $template);
                //Enviamos el segundo correo
                $req->data["emailto"]=$this->ccmail;
                $this->mailer( $res , $req , $template);
                echo $this->renderWiew($this->header("register",$lang), $res);
            }
            else{
                $auser=array("nombre"=>$req->data["name"],"app"=>$req->data["lname"],"apm"=>$req->data["mlname"],"usr"=>$req->data["user"],"tel"=>$req->data["phone"],"iata"=>$req->data["iata"],"miembro"=>$req->data["member"],"anios"=>$req->data["years"]);
                echo $this->renderWiew(array_merge(["user" => $auser]),$res);
            }
        }
        echo $this->renderWiew($this->header("register",$lang), $res);
    }
    /*
    public function hotelTransfer($req , $res){
        $uid=$req->params["uid"];
        switch($uid){
            case "four-seasons-resort-bali-at-sayan":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/sayan.mustache");
            break;
            case "four-seasons-resort-bali-at-jimbaran-bay":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/jimbaran.mustache");
            break;
            case "amanusa":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/a.mustache");
            break;
            case "como-shambhala-estate":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/b.mustache");
            break;
            case "uma-by-como,-ubud":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/c.mustache");
            break;
            case "viceroy-bali":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/d.mustache");
            break;
            case "the-st.-regis-bali-resort":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/e.mustache");
            break;
            case "bvlgari-bali":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/g.mustache");
            break;
            case "alila-villas-uluwatu":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/f.mustache");
            break;

            case "mandapa,-a-ritz-carlton-reserve":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/h.mustache");
            break;

        }
        $lang=$req->lang;
        echo $this->renderWiew( $this->header("transfer",$lang), $res);
    }
    */

     public function hotelTransfer($req , $res){
        $uid=$req->params["uid"];
        $hotelTransferMapper=$this->spot->mapper("Entity\hotelTransfer");
        $trans=$hotelTransferMapper->select()->where(["uri"=>$uid])->with("hotel")->first();
        $lang=$req->lang;
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/hTransfer_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("hotelTransfer/hTransfer.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("hotelTransfer/hTransfer.mustache");
            break;
        }
        if( $trans ){
            $this->trans = $trans;
            $translate = new stdClass();
            $translate->call = function( $lang ){
                $uri = $lang=="es"?$this->trans->uri_es:$this->trans->uri;
                return Luna\Translate::to( $lang , ["uri"=> $uri, "mapper"=>"{uid}"]);
            };
            echo $this->renderWiew( array_merge(["translate"=>$translate,"trans" => $trans] , $this->header("transfer",$lang) ), $res);   
        }
        else{
            header('Location:'.Luna\Translate::url("/travelAgent"));
        }
    }
    public function profile($req,$res){
        $lang=$req->lang;
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/profile_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/profile.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/profile.mustache");
            break;
        }
        echo $this->renderWiew($this->header("profile",$lang),$res); 
    }
    public function editProfile($req,$res){
        $lang=$req->lang;
        switch($lang){
            case "es":
                $res->m = $res->mustache->loadTemplate("Plain/eprofile_esp.mustache");
            break;
            case "en":
                $res->m = $res->mustache->loadTemplate("Plain/eprofile.mustache");
            break;
            default:
                $res->m = $res->mustache->loadTemplate("Plain/eprofile.mustache");
            break;
        }
        if(isset($req->data["name"])){
            global $session_handle;
            $mensaje="";
            $ses=$session_handle->getSegment('Luna\Session');
            $userMapper=$this->spot->mapper("Entity\Users");
            $user=$userMapper->select()->where(["id"=>$ses->get("user",false)["id"]])->first();
            $name = $req->data["name"]!=null? filter_var($req->data["name"], FILTER_SANITIZE_STRING) : $user->nombre;
            $app = $req->data["app"]!=null? filter_var($req->data["app"], FILTER_SANITIZE_STRING) : $user->papellido;
            $apm = $req->data["apm"]!=null? filter_var($req->data["apm"], FILTER_SANITIZE_STRING) : $user->mapellido;
            $tel = $req->data["tel"]!=null? filter_var($req->data["tel"], FILTER_SANITIZE_STRING) : $user->telefono;
            $iata = $req->data["iata"]!=null? filter_var($req->data["iata"], FILTER_SANITIZE_STRING) : $user->iata;
            $member = $req->data["member"]!=null? filter_var($req->data["member"], FILTER_SANITIZE_STRING) : $user->miembros;
            $years = $req->data["years"]!=null? filter_var($req->data["years"], FILTER_SANITIZE_STRING) : $user->years;
            if($req->data["pass"]!=null && $req->data["pass1"]!=null){
                if($req->data["pass"]==$req->data["pass1"]&&strlen($req->data["pass"])>6){
                    $user->password=$req->data["pass"];
                }
                else{
                    if($lang=="en"){
                        $mensaje+="The password was not updated./n The passwords do not match or is less than 6 characters./n";  
                    }
                    else{
                        $mensaje+="La contraseña no fue actualizado./n Las contraseñas no coinciden o es menor a 6 caracteres./n"; 
                    }
                    
                }
            }
            if($mensaje!=""){
                    echo "<script>alert(".$mensaje.");</script>";
            }
            else{
                if($lang=="en"){
                   echo "<script>alert(User Updated!);</script>"; 
                }
                else{
                    echo "<script>alert(Usuario Actualizado!);</script>"; 
                }     
            }
            $user->nombre=$name;
            $user->papellido=$app;
            $user->mapellido=$apm;
            $user->telefono=$tel;
            $user->iata=$iata;
            $user->miembros=$member;
            $user->years=$years;
            $userMapper->update($user);
            $ses->set("user", $user->toArray());
        }
        echo $this->renderWiew($this->header("profile",$lang),$res); 
    }         
}

?>