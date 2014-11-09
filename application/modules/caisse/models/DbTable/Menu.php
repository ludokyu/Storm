<?php

class Caisse_Model_DbTable_Menu extends Zend_Db_Table_Abstract
{

    protected $_name = 't_menu';

	public function verif_nec($id_plat){
		$s=$this->select();
		$s->where("id_plat=$id_plat")
		->where("is_nec=1");
		$r=$this->fetchAll($s);
		return count($r);
	}
	
	
}

