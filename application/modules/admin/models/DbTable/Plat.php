<?php

class Admin_Model_DbTable_Plat extends Storm_Model_DbTable_Plat{

  protected $_name='t_plat';
  protected $_primary='id_plat';

  public function getListPlatOrderByCat(){
    $select=$this->select()
            ->setIntegrityCheck(false);
    $select->from($this, array("id_plat", "nom_plat"))
            ->joinRight("t_categorie", $this->_name.".id_cat=t_categorie.id_cat AND statut_cat=1", array("nom_cat","id_cat"))
            ->where("statut_plat=1")
            ->orWhere("statut_plat IS NULL")
            ->order("nom_cat")
            ->order("nom_plat");

    return $this->fetchAll($select);
  }

  public function getPlat($id_plat){
    $select=$this->select()
            ->setIntegrityCheck(false);
    $select->from($this, array("nom_plat", "id_plat", "place", "go", "liv", "base_pizza", "list_ingt", "id_cat"))
            ->joinNatural("t_categorie")
            ->where("id_plat=?", (int) $id_plat)
            ->where("statut_plat=1")
            ->where("statut_cat=1");

    return $this->fetchRow($select);
  }

  public function listplat($id_cat){
    $select=$this->select();
    $select->from($this, array("id_plat", "nom_plat", "prix_inv"))
            ->where("id_cat=$id_cat")
            ->where("statut_plat=1")
            ->order("nom_plat");

    return $this->fetchAll($select);
  }

  public function deletePlat($id){
    $this->update(array("statut_plat"=>0), "id_plat=$id");
  }

  public function insert(array $data){
    parent::insert($data);
    return $this->getAdapter()->lastInsertId();
  }

}

