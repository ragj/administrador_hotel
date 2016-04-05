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

    public function hotel($req , $res){
        if(isset($req->params["hotel"] ) ){

            $tourMapper = $this->spot->mapper("Entity\Hotel");
            $hotel = $tourMapper->select()->with("images")->where(["uri" => $req->params["hotel"]])->first();

            

            $res->m = $res->mustache->loadTemplate("Plain/hotel-inner.mustache");
            echo $this->renderWiew( array_merge(["hotel-data" => $hotel] , $this->header("hotel") ), $res);
        }else{
            $hotelMapper=$this->spot->mapper("Entity\Hotel");
            $hotel=$hotelMapper->select()->with("images");
            echo $this->renderWiew( array_merge(["hotel"=>$hotel]), $res);
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
    	echo $this->renderWiew( $this->header("contact"), $res);
    }


    



    
}

?>