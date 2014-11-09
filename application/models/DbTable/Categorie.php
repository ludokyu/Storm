<?php

class Storm_Model_DbTable_Categorie extends Zend_Db_Table_Abstract
{

    protected $_name = 't_categorie';


    public function getById($id_cat){
        $r=$this->find($id_cat);
        return $r;

    }

}

