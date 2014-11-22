<?php

class Admin_Model_DbTable_Client extends Storm_Model_DbTable_Client
{

    protected $_name = 't_client';
    public function searchClient($name){
      $select=$this->select()
              ->where("nom_client LIKE '$name%'")
              ->orWhere("societe LIKE '$name%'")
              ->order("nom_client");
      
      return $this->fetchAll($select);
    }

}

