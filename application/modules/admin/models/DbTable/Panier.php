<?php

class Admin_Model_DbTable_Panier extends Storm_Model_DbTable_Panier
{

     protected $_name = 't_panier';

     public function getPanierCmd($id_cmd){
	$select=$this->select()->setIntegrityCheck(false);
	$select->from($this,array("id_plat_2","id_base","taille","qte_panier","id_panier","id_cat",
				'id_plat','plus_ingt','moins_ingt','prix_panier','rmq'))
	->join(array('c'=>'t_categorie'),'c.id_cat = '.$this->_name.'.id_cat',array("nom_cat","tab_taille"))
	->join(array('p'=>'t_plat'),'p.id_plat = '.$this->_name.'.id_plat',array("nom_plat"))
	->where("id_cmd=$id_cmd");
	
	return $this->fetchAll($select);
	
     }
     public function getPanierCmdforPrint($id_cmd){
	$select=$this->select()->setIntegrityCheck(false);
	$select->from($this,array("taille","qte_panier","id_panier","id_cat",'prix_panier','rmq','id_plat',"etat_panier",
		'plus_ingt','moins_ingt','nom_plus'=>'plus_ingt',"nom_moins"=>"moins_ingt","menu"=>"rmq"))
	->join(array('c'=>'t_categorie'),'c.id_cat = '.$this->_name.'.id_cat',array("nom_cat","tab_taille","afficher","big_on_ticket","is_menu"))
	->join(array('p'=>'t_plat'),'p.id_plat = '.$this->_name.'.id_plat',array("nom_plat"))
	->joinLeft(array('p2'=>'t_plat'),'p2.id_plat = '.$this->_name.'.id_plat_2',array("nom_plat2"=>"nom_plat"))
	->joinLeft(array('b'=>'t_base_pizza'),'b.id_base = '.$this->_name.'.id_base',array("nom_base"))
	->where("id_cmd=$id_cmd");
	
	return $this->fetchAll($select);
	
     }
     
     public function delPanierFromCmd($id_cmd){
     	$select=$this->select();
     	$select->from($this,array("id_panier"))
     		->where("id_cmd=".$id_cmd);
     	$rows=$this->fetchAll($select);
     	$Panier=new Admin_Model_DbTable_PanierMenu();
     	foreach($rows as $row){
     		$Panier->delete("id_panier=".$row->id_panier);
     	}
     	$this->delete("id_cmd=".$id_cmd);
     }
}

