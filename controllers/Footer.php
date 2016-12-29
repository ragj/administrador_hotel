<?php
	class Footer extends Luna\Controller {
		/**
    	*	Funcion que obtiene todos los registros de la tabla contacto.
    	**/
    	public function edit($req,$res){
	    	$footerMapper=$this->spot->mapper("Entity\Footer");
	    	$footer=$footerMapper->select()->first();
	    	if(isset($req->data["content1"])){
	    		$footer->content=$req->data["content1"];
	    		$footerMapper->update($footer);
	    	}
	    	if(isset($req->data["content2"])){
	    		$footer->content_esp=$req->data["content2"];
	    		$footerMapper->update($footer);
	    	}
	    	$footer=$footerMapper->select()->first();
	    	echo $this->renderWiew(array_merge(["footer" => $footer]),$res);
	    }
	}
?>