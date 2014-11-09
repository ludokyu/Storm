<?php

class Admin_Model_DbTable_Ingt extends Storm_Model_DbTable_Ingt
{

    protected $_name = 't_ingt';

    public function get($id){
        $select=$this->select();
        $select->from($this,array("nom_ingt","id_ingt"))
        ->where("id_ingt=?",(int)$id);
        return $this->fetchRow($select);
    }
}

