<?php
	class Request extends Luna\Controller {
		/**
    	*	Metodo que obtiene todos los registros de la tabla contacto.
    	**/
    	public function show($req,$res){
	    	$requestMapper=$this->spot->mapper("Entity\Request");
	    	$request=$requestMapper->select()->with("user");
	    	echo $this->renderWiew(array_merge(["request" => $request]),$res);
	    }
	    /**
	    *	Metodo que obtiene los datos detalle de un request
	    **/
	    public function showDet($req,$res){
	    	$requestMapper=$this->spot->mapper("Entity\Request");
	    	$request=$requestMapper->select()->with("user")->with("rhotels")->with("rexperience")->with("rtransfer")->where(["idrequest"=>$req->params["req"]]);
	    	
	    	echo $this->renderWiew(array_merge(["request" => $request->first()]),$res);
	    }
	}
?>