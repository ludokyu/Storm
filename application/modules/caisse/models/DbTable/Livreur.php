<?php

class Caisse_Model_DbTable_Livreur extends Zend_Db_Table_Abstract
{

    protected $_name = 't_livreur';

    public function getAllLivreur(){
    	$select=$this->select();
    	$select->from($this,array("id_livreur","nom_livreur"))
    		->where("is_liv=1")
    		->where("is_fav=1")
    		->order("nom_livreur");
    		
    		return $this->fetchAll($select);
    }
}

