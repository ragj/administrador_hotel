<?php
	class Contact extends Luna\Controller {
		/**
    	*	Funcion que obtiene todos los registros de la tabla contacto.
    	**/
    	public function show($req,$res){
	    	$contactMapper=$this->spot->mapper("Entity\Contact");
	    	$contacto=$contactMapper->select();
	    	echo $this->renderWiew(array_merge(["contact" => $contacto]),$res);
	    }
	}
?>