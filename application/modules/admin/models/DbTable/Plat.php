<?php

class Admin_Model_DbTable_Plat extends Storm_Model_DbTable_Plat{

  protected $_name='t_plat';
  protected $_primary='id_plat';

  public function getListPlatOrderByCat(){
    $select=$this->select()
            ->setIntegrityCheck(false);
   $select->from($this, array("id_plat", "nom_plat"))
            ->joinRight("t_categorie", $this->_name.".id_cat=t_categorie.id_cat AND statut_cat=1", array("nom_cat","id_cat","statut_cat"))
            ->where("statut_plat=1")
            ->orWhere("statut_plat IS NULL")
           ->having("statut_cat=1")
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
  
  public function consommationCat($date_deb,$date_fin){
    $sql="SELECT  SUM(qte_panier) AS qte,nom_cat,pa.id_cat
      FROM t_cmd AS c
      INNER JOIN t_panier AS pa ON pa.id_cmd=c.id_cmd AND etat_panier=1
      INNER JOIN t_categorie AS cat ON cat.id_cat=pa.id_cat
      WHERE date_cmd >= '$date_deb' AND date_cmd <='$date_fin' AND statut_cmd='O'
        GROUP BY pa.id_cat
        UNION ALL
        SELECT SUM(qte_panier) AS qte,nom_cat,p.id_cat
         FROM t_cmd AS c
      INNER JOIN t_panier AS pa ON pa.id_cmd=c.id_cmd   AND etat_panier=1
      INNER JOIN t_panier_menu AS pm ON pa.id_panier=pm.id_panier
       INNER JOIN t_plat AS p ON pm.id_plat=p.id_plat
          INNER JOIN t_categorie AS cat ON cat.id_cat=p.id_cat
           WHERE date_cmd >= '$date_deb' AND date_cmd <='$date_fin' AND statut_cmd='O'
        GROUP BY p.id_cat
     
            ";
    $data=array();
   $rows=$this->getAdapter()->fetchAll($sql);
   foreach($rows as $v){
     if(!isset($data[$v["id_cat"]]))
       $data[$v["id_cat"]]=$v;
     else{
       $data[$v["id_cat"]]["qte"]+=$v["qte"];
     }
   }
   return $data;
  }

  
  public function consommationPlat($id_cat,$date_deb,$date_fin){
      $sql="SELECT  SUM(qte_panier) AS qte,nom_plat,pa.id_plat
      FROM t_cmd AS c
      INNER JOIN t_panier AS pa ON pa.id_cmd=c.id_cmd AND etat_panier=1 
      INNER JOIN t_plat AS p ON p.id_plat=pa.id_plat AND p.id_cat=".$id_cat."
      WHERE date_cmd >= '$date_deb' AND date_cmd <='$date_fin' AND statut_cmd='O'
        GROUP BY pa.id_plat
        UNION ALL
        SELECT SUM(qte_panier) AS qte,nom_plat,pm.id_plat
         FROM t_cmd AS c
      INNER JOIN t_panier AS pa ON pa.id_cmd=c.id_cmd   AND etat_panier=1
      INNER JOIN t_panier_menu AS pm ON pa.id_panier=pm.id_panier
       INNER JOIN t_plat AS p ON pm.id_plat=p.id_plat AND p.id_cat=".$id_cat."
           WHERE date_cmd >= '$date_deb' AND date_cmd <='$date_fin' AND statut_cmd='O'
        GROUP BY pm.id_plat
     
            ";
    $data=array();
   $rows=$this->getAdapter()->fetchAll($sql);
   foreach($rows as $v){
     if(!isset($data[$v["id_plat"]]))
       $data[$v["id_plat"]]=$v;
     else{
       $data[$v["id_plat"]]["qte"]+=$v["qte"];
     }
   }
  
   return $data;
   
  }
  
}


