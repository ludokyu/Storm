<?php

class Caisse_Model_DbTable_PanierMenu extends Storm_Model_DbTable_PanierMenu
{

    protected $_name = 't_panier_menu';

	public function updatepanier($array){
		if($array["id_panier"]!=""){
			$this->truncate($array["id_panier"]);
			$id_panier=$array["id_panier"];
		}
		else{
			$id_panier=0;
		}
		for($i=0;$i<$array["count"];$i++){
			if(isset($data)) unset($data);
			$data["id_panier"]=$id_panier;
			$data["id_menu"]=$array["id_menu_$i"];
			$data["id_plat"]=$array["id_plat_".$i];
			if(isset($array["ingt_plus_$i"])){
				$data["ingt_plus"]=$array["ingt_plus_".$i];
				$data["ingt_moins"]=$array["ingt_moins_".$i];
			}
			if(isset($array["plat_2_$i"]))	$data["id_plat2"]=$array["plat_2_".$i];
			if(isset($array["base_$i"]))	$data["id_base"]=$array["base_".$i];

			$this->add($data);
		}
		
		
		
	}
	public function add($data){
		 
		$this->insert($data);
	}
	
	public function findsubpanier($id_panier){
		$select=$this->select();
		$select->where("id_panier=$id_panier AND id_panier<>0")
		->order("id_panier_menu");
		return $this->fetchAll($select);
		
	}
	public function truncate($id_panier=0){
		
		$this->delete("id_panier=$id_panier");
	}
	public function insertInpanier($id_panier){
		
		$data = array(   'id_panier'      => $id_panier);
 
		$where = $this->getAdapter()->quoteInto('id_panier = ?', 0);
		$this->update($data, $where);
	}
	
	public function deletePanier($id){
		$this->delete("id_panier=".$id);
	}
	
	
}

