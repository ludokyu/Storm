<?php

class Admin_Model_DbTable_Menu extends Zend_Db_Table_Abstract{

  protected $_name='t_menu';

  public function getMenu($id_plat){
    $select=$this->select()
            ->setIntegrityCheck(false);
    $select->from($this)
            ->join(array("c"=>"t_categorie"), "c.id_cat=".$this->_name.".id_cat", array("tab_taille"))
            ->where("statut_menu='O' AND id_plat=?", (int) $id_plat);
    return $this->fetchAll($select);
  }

}

