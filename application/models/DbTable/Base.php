<?php

class Storm_Model_DbTable_Base extends Zend_Db_Table_Abstract
{

    protected $_name = 't_base_pizza';
    public function listbase(){

        return $this->fetchAll(NULL,"nom_base");
    }

}

