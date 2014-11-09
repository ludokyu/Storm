<?php

class Admin_Model_DbTable_Categorie extends Storm_Model_DbTable_Categorie{

  protected $_name='t_categorie';

  public function listCategorie(){
    $select=$this->select();
    $select->from($this, array("nom_cat", "id_cat", "is_default"))
            ->where("statut_cat=1")
            ->order("nom_cat");
    $r=$this->fetchAll($select);
    return $r;
  }

  public function listCategorieNotMenu(){
    $select=$this->select();
    $select->from($this, array("nom_cat", "id_cat", "is_taille"))
            ->where("is_menu=0")
            ->where("statut_cat=1")
            ->order("nom_cat");
    $r=$this->fetchAll($select);
    return $r;
  }

  public function get($id){
    $select=$this->select();
    $select->where("id_cat=?", (int) $id);
    $r=$this->fetchRow($select);
    return $r;
  }

  public function listCatInventaire(){
    $select=$this->select();
    $select->from($this, array("id_cat", "nom_cat"))
            ->where("is_compo=0")
            ->where("is_menu=0")
            ->order("nom_cat");
    $r=$this->fetchAll($select);
    return $r;
  }
  
  public function deleteCat($id){
    $this->update(array("statut_cat"=>0),"id_cat=$id");
    
  }

}

