<?php

class Storm_Model_DbTable_Cmd extends Zend_Db_Table_Abstract
{

    protected $_name = 't_cmd';
    public function getCmd($id_cmd){
        $select=$this->select()
        ->setIntegrityCheck(false);
        $select->from($this,array("id_cmd","no_cmd","type_cmd","id_client","date_cmd","total_cmd"))
        ->join(array("t"=>"t_type_cmd"),$this->_name.".type_cmd=t.id_type_cmd",array("nom_type_cmd"))
        ->joinLeft(array("c"=>"t_client"),$this->_name.".id_client=c.id_client")
        ->joinLeft(array("l"=>"t_livreur"),$this->_name.".id_livreur=l.id_livreur","nom_livreur")
        ->joinLeft(array("t_reglement"),$this->_name.".etat_paiment=code_reglement",array("code_reglement","nom_reglement"))

        ->where("id_cmd=?",$id_cmd);
          $row= $this->fetchRow($select);
          return $row;


    }

}

