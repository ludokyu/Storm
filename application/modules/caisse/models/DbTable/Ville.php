<?php

class Caisse_Model_DbTable_Ville extends Zend_Db_Table_Abstract
{

    protected $_name = 't_ville';

	public function getVille($id){
		
		$r=$this->find($id);
       	return $r->toArray();
	}
	public function VilleBycp($cp){
		$select=$this->select();
		$r=$select->from($this,array("id_ville","nom_ville"))
		->where("code_postal LIKE '$cp'");
       	return $this->fetchAll($select);
	}
}

