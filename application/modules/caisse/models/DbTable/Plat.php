<?php

class Caisse_Model_DbTable_Plat extends Zend_Db_Table_Abstract{

  protected $_name='t_plat';

  public function listplat($id_cat, $list_plat=NULL){
    $select=$this->select();
    $select->from($this, array("id_plat", "nom_plat"))
            ->where("id_cat=?", $id_cat)
            ->where("statut_plat=1")
            ->order("nom_plat");
    if(!is_null($list_plat)&&$list_plat!=""){
      $select->where("id_plat IN ($list_plat)");
    }
    return $this->fetchAll($select);
  }

}

