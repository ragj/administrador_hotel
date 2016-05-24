<?php
	class Transfer extends Luna\Controller {

		/**
    	*	Metodo que agrega un bloque a la tabla TransferBlocks.
    	**/
    	public function addBlock($req,$res){
    		$userZonaMapper=$this->spot->mapper("Entity\UsersZona");
        	$zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
        	$aux=array();
        	foreach ($zones as $zone) {
            	array_push($aux,$zone['zona_idzona']);
        	}
        	$zoneMapper=$this->spot->mapper("Entity\Zona");
        	$zona=$zoneMapper->select()->where(["idzona"=>$aux]);
    		if(isset($req->data["name"])){
    			//seleccionamos entidad a maperar
    			$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
    			//construimos la entidad
    			$entity = $transferBlockMapper->build([
                	'TransferBlockTitle' => $req->data["name"],
                	'TransferBlockTitle_es' =>$req->data["name1"],
                	'zona_idzona'=>$req->data["zone"]
            	]);
            	$result=$transferBlockMapper->insert($entity);
            	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
    		}
	    	echo $this->renderWiew(array_merge(["zones"=>$zona]),$res);
	    }

	    /**
	    *	Metodo que obtiene los transfer blocks y agrega el detalle del transfer block
	    **/
	    public function addDetail($req,$res){
	    	if(isset($req->data["tb"],$req->data["desc"],$req->data["descesp"])){	
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$entity=$transferDetailMapper->build([
		    		'description' => $req->data["desc"],
	                'description_esp' => $req->data["descesp"],
	                'transferBlock_idtransferBlock' => $req->data["tb"]
	    		]);
	    		$result=$transferDetailMapper->insert($entity);
	    	}
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);

	    }

	    /**
	    *	Metodo que obtiene los transfer detail y agrega el valor del transfer detail
	    **/
	    public function addValue($req,$res){
	    	$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    	$details=$transferDetailMapper->select();
	    	if(isset($req->data["tb"],$req->data["desc"])){
	    		$transferValuesMapper=$this->spot->mapper("Entity\TransferValue");
	    		$entity=$transferValuesMapper->build([
	    			'val' => $req->data["desc"],
                	'transferDetail_idtransferDetail'=> $req->data["tb"]
	    		]);
	    		$result=$transferValuesMapper->insert($entity);
	    	}
	    	echo $this->renderWiew(array_merge(["details"=>$details]),$res);
	    }

	    /**
	    *	Metodo que obtiene un listado de los transfers blocks
	    **/
	    public function listBlock($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select()->with("zona");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }

	    /**
	    *	Metodo que obtiene los datos de un bloque y permite editarlo
	    **/
	    public function editBlock($req,$res){
	    	if(isset($req->params["block"])){
	    		$userZonaMapper=$this->spot->mapper("Entity\UsersZona");
	        	$zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
	        	$aux=array();
	        	foreach ($zones as $zone) {
	            	array_push($aux,$zone['zona_idzona']);
	        	}
	        	$zoneMapper=$this->spot->mapper("Entity\Zona");
	        	$zonaa=$zoneMapper->select()->where(["idzona"=>$aux]);
	    		$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
            	$transfer = $transferBlockMapper->select()->where(["idtransferBlock" => $req->params["block"]])->with("detail")->first();
            	if(isset($req->data["title"],$req->data["titleesp"],$req->data["zone"])){
            		
		    		$title=$req->data["title"]!=null? filter_var($req->data["title"],FILTER_SANITIZE_STRING): $transfer->TransferBlockTitle;
	            	$titleesp=$req->data["titleesp"]!=null? filter_var($req->data["titleesp"],FILTER_SANITIZE_STRING): $transfer->TransferBlockTitle_es;
	            	$zona=$req->data["zone"]!=null? $req->data["zone"]: $transfer->zona_idzona;
	            	$transfer->TransferBlockTitle=$title;
	            	$transfer->TransferBlockTitle_es=$titleesp;
	            	$transfer->zona_idzona=$zona;
	            	$transferBlockMapper->update($transfer);
	    		}
            	echo $this->renderWiew(array_merge(["transfer"=>$transfer,"zones"=>$zonaa]),$res);
	    	}
	    }
 		/**
	    *	Metodo que obtiene los datos de un detalle y permite actualizarlos 
	    **/
	    public function editDetail($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	if(isset($req->params["detail"])){
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$transfer = $transferDetailMapper->select()->with("transferValue")->where(["idtransferDetail" => $req->params["detail"]])->first();
	    		///Obtenemos y validamos si el campo es diferente de vacio, en caso de estar vacio obtenemos el valor de la base de datos
	    		if(isset($req->data["desc"])){
	    			$transfer->description=$req->data["desc"]!=null? filter_var($req->data["desc"],FILTER_SANITIZE_STRING): $transfer->description;
	            	$transfer->description_esp=$req->data["descesp"]!=null? filter_var($req->data["descesp"],FILTER_SANITIZE_STRING): $transfer->description_esp;
	            	$transfer->transferBlock_idtransferBlock=$req->data["tb"]!=null? $req->data["tb"]: $transfer->transferBlock_idtransferBlock;
		    		//actualizamos la entidad y volvemos a consultar la base
		    		$transferDetailMapper->update($transfer);
	            	$transfer = $transferDetailMapper->select()->where(["idtransferDetail" => $req->params["detail"]])->first();
            	}
	    		echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock],["transferDetail"=>$transfer]),$res);
	    	}
	    }

	    /**
	    *	Metodo que edita un value
	    **/
	    public function editValue($req,$res){
	    	if(isset($req->params["value"])){
	    		$transferValuesMapper=$this->spot->mapper("Entity\TransferValue");
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$details=$transferDetailMapper->select();
	    		$values=$transferValuesMapper->select()->with("transferDetail")->where(["idtransferValues"=>$req->params["value"]])->first();
	    		if(isset($req->data["tb"])||isset($req->data["desc"])){
	    			$tb=$req->data["tb"]!=null?$req->data["tb"]: $values->transferDetail_idtransferDetail;
	            	$val=$req->data["desc"]!=null? $req->data["desc"]: $values->val;
	            	$values->transferDetail_idtransferDetail=$tb;
	            	$values->val=$val;
	            	$transferValuesMapper->update($values);
	    		}
	    		echo $this->renderWiew(array_merge(["value"=>$values,"details"=>$details]),$res);
	    	}
	    }

	    /**
	    *	Metodo que sirve para eliminar un bloque y sus detalles
	    **/
	    public function deleteBlock($req,$res){
	    	if(isset($req->params["block"])){
	    		$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$transferValuesMapper=$this->spot->mapper("Entity\TransferValue");
	    		$detail=$transferDetailMapper->select()->where(["transferBlock_idtransferBlock"=>$req->params["block"]]);
	    		$aux=array();
	    		foreach ($detail as $a) {
	    			array_push($aux,$a->idtransferDetail);
	    		}
	    		$td=$transferValuesMapper->delete(['transferDetail_idtransferDetail'=>$aux]);
            	$t = $transferDetailMapper->delete(['transferBlock_idtransferBlock ='=>$req->params["block"]]);
            	$tf = $transferBlockMapper->delete(['idtransferBlock ='=>$req->params["block"]]);
	    	}
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }

	    /**
	    *	Metodo que sirve para eliminar un detalle
	    **/
	    public function deleteDetail($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	if(isset($req->params["detail"])){
	    		$transferDetailMapper=$this->spot->mapper("Entity\TransferDetail");
	    		$transferValuesMapper=$this->spot->mapper("Entity\TransferValue");
            	$t = $transferValuesMapper->delete(['transferDetail_idtransferDetail'=>$req->params["detail"]]);
            	$t = $transferDetailMapper->delete(['idtransferDetail'=>$req->params["detail"]]);
	    	}
	    	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }
	    /**
	    *   Metodo que sirve para ocultar o mostrar el elemento
	    **/
	    public function hide($req,$res){
	        $detailMapper=$this->spot->mapper("Entity\TransferDetail");
	        if(isset($req->params["detail"])){
	            $detail=$detailMapper->select()->where(["idtransferDetail"=>$req->params["detail"]])->first();
	            if($detail->oculto==true){
	                $detail->oculto=false;
	            }
	            else{
	                $detail->oculto=true;
	            }
	            $detailMapper->update($detail);
	        }
	        $transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
            $transfer = $transferBlockMapper->select()->where(["idtransferBlock" => $detail->transferBlock_idtransferBlock])->with("detail")->first();
            $userZonaMapper=$this->spot->mapper("Entity\UsersZona");
            $zones=$userZonaMapper->select()->where(["users_id"=>$req->user["id"]])->toArray();
        	$aux=array();
        	foreach ($zones as $zone) {
            	array_push($aux,$zone['zona_idzona']);
        	}
        	$zoneMapper=$this->spot->mapper("Entity\Zona");
        	$zonaa=$zoneMapper->select()->where(["idzona"=>$aux]);
        	$res->m = $res->mustache->loadTemplate("Transfer/editBlock.mustache");
        	echo $this->renderWiew(array_merge(["transfer"=>$transfer,"zones"=>$zonaa]),$res);
	    }

	    /**
	    *	Metodo que sirve para eliminar un valor
	    **/
	    public function deleteValue($req,$res){
	    	$transferBlockMapper=$this->spot->mapper("Entity\TransferBlock");
	    	$transferBlock = $transferBlockMapper->select();
	    	if(isset($req->params["value"])){
	    		$transferValuesMapper=$this->spot->mapper("Entity\TransferValue");
            	$t = $transferValuesMapper->delete(['idtransferValues'=>$req->params["value"]]);
	    	}
	    	$res->m = $res->mustache->loadTemplate("Transfer/listBlock.mustache");
	    	echo $this->renderWiew(array_merge(["transferBlock"=>$transferBlock]),$res);
	    }
	    /**
	    *	Metodo que obtiene los transfer detail y agrega el valor del transfer detail
	    **/
	    public function addHotel($req,$res){
	    	$hotelMapper=$this->spot->mapper("Entity\Hotel");
	    	$hotels=$hotelMapper->select()->where(["zona_idzona"=>1]);
	    	if(isset($req->data["hotel"],$req->data["content"])){

	    		//insertamos este nuevo elemento
	    		$hotelTransferMapper=$this->spot->mapper("Entity\HotelTransfer");
	    		$htransfer=$hotelTransferMapper->build([
	    			'content'=>$req->data["content"],
	    			'hotel_idhotel'=>$req->data["hotel"]
	    		]);
	    		$hotelTransferMapper->insert($htransfer);
	    	}	    	
	    	echo $this->renderWiew(array_merge(["hotel"=>$hotels]),$res);
	    }
	    /**
	    *	Metodo que obtiene los transfer detail y agrega el valor del transfer detail
	    **/
	    public function editHotel($req,$res){
	    	$hotelTransferMapper=$this->spot->mapper("Entity\HotelTransfer");
	    	$tHotel=$hotelTransferMapper->select()->where(["idhotelTransfer"=>$req->params["hTrans"]]);
	    	if(isset($req->data["content"])){
	    		if(isset($req->data["hotel"])){
	    			$tHotel->hotel_idhotel=$req->data["hotel"];
	    		}
	    		$tHotel->content=$req->data["content"];
	    		//$hotelTransferMapper->update($tHotel); aqui hay un problema
	    	}	    	
	    	echo $this->renderWiew(array_merge(["tHotel"=>$tHotel]),$res);
	    }
	     /**
	    *	Metodo que obtiene los transfer detail y agrega el valor del transfer detail
	    **/
	    public function deleteHotel($req,$res){
	    	if(isset($req->params["hTrans"])){
	    		$hotelTransferMapper=$this->spot->mapper("Entity\HotelTransfer");
            	$t = $hotelTransferMapper->delete(['idhotelTransfer'=>$req->params["hTrans"]]);
	    	}
	    	header("Location:/panel/transfer/showHotel");
	    	exit;   
	    }

	     /**
	    *	Metodo que obtiene los transfer detail y agrega el valor del transfer detail
	    **/
	    public function showHotel($req,$res){
	    	$hotelTransferMapper=$this->spot->mapper("Entity\HotelTransfer");
	    	$tHotel=$hotelTransferMapper->select()->with("hotel");    	
	    	echo $this->renderWiew(array_merge(["tHotel"=>$tHotel]),$res);
	    }

	}
?>