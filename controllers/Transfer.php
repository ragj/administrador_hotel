<?php
	class Transfer extends Luna\Controller {
		/**
    	*	Funcion que agrega un bloque a la tabla TransferBlocks.
    	**/
    	public function addBlock($req,$res){
    		if(isset($req->data["name"])){
    			//seleccionamos entidad a maperar
    			$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
    			//construimos la entidad
    			$entity = $transferBlockMapper->build([
                	'TransferBlockTitle' => $req->data["name"],
                	'TransferBlockTitle_esp' =>$req->data["name1"]
            	]);
            	$result=$transferBlockMapper->insert($entity);
            	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
    		}
	    	echo $this->renderWiew([],$res);
	    }
	    /**
	    *	Funcion que obtiene los transfer blocks y agrega el detalle del transfer block
	    **/
	    public function addDetail($req,$res){
	    	if(isset($req->data["tb"],$req->data["desc"],$req->data["descesp"],$req->data["num1"],$req->data["num2"],$req->data["num3"],$req->data["num4"],$req->data["num5"],$req->data["num6"])){
	    		$num1=number_format((float)$req->data["num1"], 2, '.', '');
	    		$num2=number_format((float)$req->data["num2"], 2, '.', '');
	    		$num3=number_format((float)$req->data["num3"], 2, '.', '');
	    		$num4=number_format((float)$req->data["num4"], 2, '.', '');
	    		$num5=number_format((float)$req->data["num5"], 2, '.', '');
	    		$num6=number_format((float)$req->data["num6"], 2, '.', '');
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$entity=$transferDetailMapper->build([
		    		'td_description' => $req->data["desc"],
	                'td_description_esp' => $req->data["descesp"],
	                'td_innova_sub_1' => $num1,
	                'td_innova_sub_2_4' => $num2,
	                'td_hiace_val_5_7' => $num3,
	                'td_hiace_val_8_10' => $num4,
	                'td_alphard_mercy_1' => $num5,
	                'td_alphard_mercy_2_4' =>$num6,
	                'tf_id' => $req->data["tb"]
	    		]);
	    		$result=$transferDetailMapper->insert($entity);
	    		$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    		echo $this->renderWiew([],$res);
	    	}
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);

	    }
	    /**
	    *	Funcion que obtiene un listado de los transfers blocks
	    **/
	    public function listBlock($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }
	    /**
	    *	Funcion que obtiene los datos de un bloque y permite editarlo
	    **/
	    public function editBlock($req,$res){
	    	if(isset($req->params["block"])){
	    		$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
            	$transfer = $transferBlockMapper->select()->where(["idTransferBlock" => $req->params["block"]])->with("detail")->first();
            	echo $this->renderWiew(array_merge(["transfer"=>$transfer]),$res);
	    	}
	    	echo $this->renderWiew([],$res);

	    }
	    /**
	    *	Funcion que sirve para eliminar un bloque y sus detalles
	    **/
	    public function deleteBlock($req,$res){
	    	if(isset($req->params["block"])){
	    		$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
            	$t = $transferDetailMapper->delete(['tf_id ='=>$req->params["block"]]);
            	$tf = $transferDetailMapper->delete(['idTransferBlock ='=>$req->params["block"]]);
	    	}
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }
	    /**
	    *	Funcion que obtiene los datos de un detalle y permite actualizarlos 
	    **/
	    public function editDetail($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	if(isset($req->params["detail"])){
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$transfer = $transferDetailMapper->select()->where(["td_id" => $req->params["detail"]])->first();
	    		$transferb=$transferBlockMapper->select()->where(["idTransferBlock" => $transfer->tf_id])->first();
	    		///Obtenemos y validamos si el campo es diferente de vacio, en caso de estar vacio obtenemos el valor de la base de datos
	    		if(isset($req->data["desc"])){
		    		$description = $req->data["desc"]!=null? filter_var($req->data["desc"], FILTER_SANITIZE_STRING) : $transfer->td_description;
		    		$description_esp = $req->data["descesp"]!=null? filter_var($req->data["descesp"], FILTER_SANITIZE_STRING) : $transfer->td_description_esp;
		    		$num1 = $req->data["num1"]!=null? number_format((float)$req->data["num1"], 2, '.', '') : $transfer->td_innova_sub_1;
		    		$num2 = $req->data["num2"]!=null? number_format((float)$req->data["num2"], 2, '.', '') : $transfer->td_innova_sub_2_4;
		    		$num3 = $req->data["num3"]!=null? number_format((float)$req->data["num3"], 2, '.', '') : $transfer->td_hiace_val_5_7;
		    		$num4 = $req->data["num4"]!=null? number_format((float)$req->data["num4"], 2, '.', '') : $transfer->td_hiace_val_8_10;
		    		$num5 = $req->data["num5"]!=null? number_format((float)$req->data["num5"], 2, '.', '') : $transfer->td_alphard_mercy_1;
		    		$num6 = $req->data["num6"]!=null? number_format((float)$req->data["num6"], 2, '.', '') : $transfer->td_alphard_mercy_2_4;
		    		$num7 = $req->data["tb"]!=null? $req->data["tb"]: $transfer->tf_id;
		    		//actualizamos valores de la entidad
		    		$transfer->td_description=$description;
		    		$transfer->td_description_esp=$description_esp;
		    		$transfer->td_innova_sub_1=$num1;
		    		$transfer->td_innova_sub_2_4=$num2;
		    		$transfer->td_hiace_val_5_7=$num3;
		    		$transfer->td_hiace_val_8_10=$num4;
		    		$transfer->td_alphard_mercy_1=$num5;
		    		$transfer->td_alphard_mercy_2_4=$num6;
		    		$transfer->tf_id=$num7;
		    		//actualizamos la entidad y volvemos a consultar la base
		    		$transferDetailMapper->update($transfer);
	            	$transfer = $transferDetailMapper->select()->where(["td_id" => $req->params["detail"]])->first();
            	}
	    		echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock],["transferDetail"=>$transfer],["tactual"=>$transferb]),$res);
	    	}
	    	
	    	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }
	    /**
	    *	Funcion que sirve para eliminar un detalle
	    **/
	    public function deleteDetail($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	if(isset($req->params["detail"])){
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$t=$transferDetailMapper->select()->where(["td_id"=>$req->params["detail"]])->first();
	    		$aux=$t->tf_id;
            	$t = $transferDetailMapper->delete(['td_id ='=>$req->params["detail"]]);
	    	}
	    	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);

	    }
	}
?>