<?php

class Storm_Model_DbTable_PanierMenu extends Zend_Db_Table_Abstract
{

    protected $_name = 't_panier_menu';

    public function getPanierMenu($panier){
		$select=$this->select()->setIntegrityCheck(false);
		$select->from($this)
		->join("t_plat","t_plat.id_plat=".$this->_name.".id_plat",array("nom_plat"))
			->join("t_categorie","t_categorie.id_cat=t_plat.id_cat ",array("nom_cat","afficher","big_on_ticket","tab_taille"))
			->join("t_menu","t_menu.id_cat=t_categorie.id_cat ",array("taille"))
			->joinLeft("t_base_pizza","".$this->_name.".id_base=t_base_pizza.id_base ",array("nom_base"))
			->joinLeft(array("p"=>"t_plat"),"".$this->_name.".id_plat2=p.id_plat ",array("nom_plat2"=>"nom_plat"))
			->where("id_panier=?",$panier->id_panier)
			->where("t_menu.id_plat=?",$panier->id_plat)
			->group("id_panier_menu");
			
		return $this->fetchAll($select);	
    }	
}

