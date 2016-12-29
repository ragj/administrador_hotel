<?php
	class Rates extends Luna\Controller {
		/**
    	*	Funcion que obtiene todos los registros de la tab.
    	**/
    	public function exp($req,$res){
	    	$rateMapper=$this->spot->mapper("Entity\Texperience");
	    	$rate=$rateMapper->select()->first();
	    	if(isset($req->data["title"],$req->data["content"])){
	    		$rate->title=$req->data["title"];
	    		$rate->content=$req->data["content"];
	    		$rateMapper->update($rate);
	    	}
	    	else if(isset($req->data["title_es"],$req->data["content2"])){
	    		$rate->title_esp=$req->data["title_es"];
	    		$rate->content_esp=$req->data["content2"];
	    		$rateMapper->update($rate);
	    	}
	    	$rate=$rateMapper->select()->first();
	    	$res->m = $res->mustache->loadTemplate("Rates/exper.mustache");
	    	echo $this->renderWiew(array_merge(["rate" => $rate]),$res);
	    }
	    /**
	    *
	    **/
	    public function tra($req,$res){
	    	$rateMapper=$this->spot->mapper("Entity\Ratetransfer");
	    	$rate=$rateMapper->select()->first();
	    	if(isset($req->data["title"],$req->data["content"])){
	    		$rate->title=$req->data["title"];
	    		$rate->content=$req->data["content"];
	    		$rateMapper->update($rate);
	    	}
	    	else if(isset($req->data["title_es"],$req->data["content2"])){
	    		$rate->title_esp=$req->data["title_es"];
	    		$rate->content_esp=$req->data["content2"];
	    		$rateMapper->update($rate);
	    	}
	    	$rate=$rateMapper->select()->first();
	    	$res->m = $res->mustache->loadTemplate("Rates/trans.mustache");
	    	echo $this->renderWiew(array_merge(["rate" => $rate]),$res);
	    }
	}
?>