<?php

class Admin_Model_DbTable_Contact extends Zend_Db_Table_Abstract
{

    protected $_name = 't_livreur';

    public function getAll($search=""){
        $select=$this->select();
        $select->where("nom_livreur LIKE ?","$search%")
        ->where("statut_livreur LIKE 'O'");

        return $this->fetchAll($select);
    }

     public function search($search="",$livreur=0){
        $select=$this->select();
        $select->where("nom_livreur LIKE ?","$search%")
              ->where("statut_livreur LIKE 'O'");

              if($livreur)
                  $select->where("is_liv = ?",$livreur);
        return $this->fetchAll($select);
    }

    public function get($id){
        $select=$this->select();
        $select->where("id_livreur = ?",(int)$id);
        return $this->fetchRow($select);
    }

}

