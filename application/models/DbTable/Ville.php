<?php

class Storm_Model_DbTable_Ville extends Zend_Db_Table_Abstract
{

    protected $_name = 't_ville';

	public function getVille($id){
		
		$r=$this->find($id);
       	return $r->toArray();
	}
	
}

