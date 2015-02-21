<?php

class Caisse_Model_DbTable_Cmd extends Storm_Model_DbTable_Cmd{

  public function fetchToday(){
    $this->_name='v_cmd';
    $this->_primary="id_cmd";
    $select=$this->select();

    return $this->fetchAll($select);
  }

  public function recetteToday(){
    $this->_name='v_cmd';
    $this->_primary="id_cmd";
    $select=$this->select()
            ->from($this, array('total'=>'ROUND(SUM(total_cmd),2)'));

    return parent::fetchRow($select);
  }

  public function recetteEmporter(){
    $this->_name='v_cmd';
    $this->_primary="id_cmd";
    $select=$this->select()
            ->from($this, array('total'=>'ROUND(SUM(total_cmd),2)'))
            ->where("type_cmd!=3");
    return parent::fetchRow($select);
  }

  public function recetteLiv(){
    $this->_name='v_cmd';
    $this->_primary="id_cmd";

    $select=$this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);
    $select->join(array('l'=>'t_livreur'), 'l.id_livreur = '.$this->_name.'.id_livreur', array('l.nom_livreur', 'total'=>'ROUND(SUM(total_cmd),2)'))
            ->group("l.id_livreur");

    return parent::fetchAll($select);
  }

  public function getEncaissementLiv($id_livreur, $id_reglement){
     $this->_name='enliv_encaissement_livreur';
     $sql="SELECT enliv_montant FROM enliv_encaissement_livreur WHERE enliv_date= CURDATE() AND en_id_livreur=$id_livreur AND  enliv_modpaiment = $id_reglement";
    $sql= $this->select()
            ->from($this->_name,"enliv_montant")
             ->where("enliv_date= CURDATE() AND en_id_livreur=?",$id_livreur)
             ->where("enliv_modpaiment =?",$id_reglement);
     $result=$this->fetchRow($sql);
  
      $this->_name='v_cmd';
      
   if($result)
      return $result->enliv_montant;
  }

  public function Totalclient($id_client){
    $select=$this->select()
            ->from($this, array('total'=>'ROUND(SUM(total_cmd),2)', 'nb'=>'COUNT(id_client)'))
            ->where("id_client =?", $id_client)
            ->group("id_client");

    $result=$this->fetchRow($select);

    return $result;
  }

  public function updateDateModif($id_cmd){

    $this->update(array("date_modif"=>"NOW()"), "id_cmd=".$id_cmd);
  }

  public function getMaxNoCmd(){
    $this->_name='v_cmd';
    $this->_primary="id_cmd";
    $select=$this->select();
    $select->from($this, array("last"=>"MAX(no_cmd)"));
    $return=$this->fetchRow($select);
    return $return->last;
  }

  public function affecterLivreurToCmd($id_cmd, $id_livreur){

    $this->update(array("id_livreur"=>$id_livreur), "id_cmd=".$id_cmd);
  }

  public function affecterPaiementToCmd($id_cmd, $id_reglement){

    $this->update(array("etat_paiment"=>$id_reglement), "id_cmd=".$id_cmd);
  }

  public function getPaiementToday(){
    $this->_name='v_cmd';
    $this->_primary="id_cmd";

    $selectT=$this->select()
            ->setIntegrityCheck(false);

    $selectT->from($this, array("total"=>"SUM(total_cmd)", new Zend_Db_Expr("'ALL'")));
    $selectT->join("t_reglement", 'code_reglement = etat_paiment', "nom_reglement");
    $selectT->group("id_reglement");

    $selectL=$this->select()
            ->setIntegrityCheck(false);
    $selectL->from($this, array("total"=>"SUM(total_cmd)", 'id_livreur'));
    $selectL->join("t_reglement", 'code_reglement = etat_paiment', "nom_reglement");

    $selectL->group("id_livreur");
    $selectL->group("etat_paiment");
    $select=$this->select();
    $select->union(array($selectL, $selectT));

    return parent::fetchAll($select);
  }

  public function encaissement($data){
    $sql="INSERT INTO enliv_encaissement_livreur (en_id_livreur, enliv_date, enliv_modpaiment,enliv_montant)
          VALUES (".$data["en_id_livreur"].", CURDATE(), ".$data["enliv_modpaiment"].",'".$data["enliv_montant"]."')
          ON DUPLICATE KEY UPDATE enliv_montant='".$data["enliv_montant"]."'";
    
    $this->_db->query($sql);
  }

}

