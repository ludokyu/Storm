<?php

class Caisse_Model_DbTable_Categorie extends Storm_Model_DbTable_Categorie
{

    protected $_name = 't_categorie';

    public function listCategorie(){
      
        $r=$this->fetchAll("statut_cat=1","nom_cat");
        
         return $r->toArray();
    }

    public function is_menu($id_cat){
        $select=$this->select();
        $select->from($this,"is_menu")
        ->where("id_cat=$id_cat");
        return $this->fetchRow($select);

    }
}

