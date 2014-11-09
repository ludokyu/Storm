<?php

class Caisse_Model_DbTable_Reglement extends Zend_Db_Table_Abstract
{

    protected $_name = 't_reglement';

    public function getAll(){
        $select=$this->select()
            ->from($this,array("code_reglement","nom_reglement"))
            ->where("statut_reglement =1");
        return $this->fetchAll($select);

    }
}

