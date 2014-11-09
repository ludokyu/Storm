<?php

class Admin_Model_DbTable_Base extends Storm_Model_DbTable_Base
{


     public function get($id){
        $select=$this->select();
        $select->from($this,array("nom_base","id_base"))
        ->where("id_base=?",(int)$id);
        return $this->fetchRow($select);
    }
}

