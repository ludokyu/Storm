<?php

class Caisse_Model_DbTable_Ville extends Storm_Model_DbTable_Ville{

  protected $_name='t_ville';

  public function VilleBycp($cp){
    $select=$this->select();
    $r=$select->from($this, array("id_ville", "nom_ville"))
            ->where("code_postal LIKE '$cp'");
    return $this->fetchAll($select);
  }

}

