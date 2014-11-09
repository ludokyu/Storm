<?php

class Admin_Model_DbTable_Cmd extends Storm_Model_DbTable_Cmd{

  public function getYearCmd(){

    $select=$this->select();
    $select->from($this, array("date_min"=>new Zend_Db_Expr("MIN(date_cmd)"), "date_max"=>new Zend_Db_Expr("MAX(date_cmd)")))
            ->where("statut_cmd ='O'")
    ;
    //	echo $select;
    return $this->fetchRow($select);
  }

  public function getMonthCmdFromYear($year){

    $select=$this->select();
    $select->from($this, array("month"=>new Zend_Db_Expr("GROUP_CONCAT(DISTINCT(SUBSTR(date_cmd,6,2)) SEPARATOR',')")))
            ->where("SUBSTR(date_cmd,1,4) = '$year'")
            ->where("statut_cmd ='O'")
    ;
    return $this->fetchRow($select);
  }

  public function getDayCmdFromYearMonth($year, $month){

    $select=$this->select();
    $select->from($this, array("day"=>new Zend_Db_Expr("GROUP_CONCAT(DISTINCT(SUBSTR(date_cmd,9,2)) SEPARATOR',')")))
            ->where("SUBSTR(date_cmd,1,7) = '$year-$month'")
            ->where("statut_cmd ='O'")
    ;

    return $this->fetchRow($select);
  }

  public function getTotalFromDate($date){
    $select=$this->select();
    $select->from($this, array("total_jour"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y-m-d", strtotime($date))."%")
            ->where("statut_cmd ='O'")
    ;
    return $this->fetchRow($select);
  }

  public function getRecapPaiementDay($date){
    $select=$this->select();
    $select->setIntegrityCheck(false)
            ->from($this, array("etat_paiment", "total"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y-m-d", strtotime($date))."%")
            ->where("type_cmd <>3")
            ->where("statut_cmd ='O'")
            ->group("etat_paiment");
    $return=array();
    $data=$this->fetchAll($select);
    foreach($data as $r){
      $return[$r->etat_paiment]=$r->total;
    }
    $select2=$this->select()
            ->setIntegrityCheck(false)
            ->from("enliv_encaissement_livreur", array("enliv_modpaiment", "total"=>new Zend_Db_Expr("SUM(enliv_montant)")))
            ->where("enliv_date LIKE ?", date("Y-m-d", strtotime($date))."%")
            ->group("enliv_modpaiment");

    $data2=$this->fetchAll($select2);
    foreach($data2 as $r){
      @$return[$r->enliv_modpaiment]+=$r->total;
    }

    return $return;
  }

  public function getCmdByDate($date){

    $select=$this->select();
    $select->from($this, array("id_cmd", "type_cmd", "total_cmd", "id_client"))
            ->where("date_cmd LIKE ?", date("Y-m-d", strtotime($date))."%")
            ->where("statut_cmd ='O'")
            ->order("date_cmd DESC");

    return $this->fetchAll($select);
  }

  public function getRecapMonth($date){

    $select=$this->select();
    $select->from($this, array("date_cmd", "total"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y-m", strtotime($date))."%")
            ->where("statut_cmd ='O'")
            ->group("SUBSTR(date_cmd,1,10)");

    return $this->fetchAll($select);
  }

  public function getRecapPaiementMonth($date){

    $select=$this->select();
    $select->setIntegrityCheck(false)
            ->from($this, array("etat_paiment", "total"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y-m", strtotime($date))."%")
            ->where("type_cmd <>3")
            ->where("statut_cmd ='O'")
            ->group("etat_paiment");
    $return=array();
    $data=$this->fetchAll($select);
    foreach($data as $r){
      $return[$r->etat_paiment]=$r->total;
    }
    $select2=$this->select()
            ->setIntegrityCheck(false)
            ->from("enliv_encaissement_livreur", array("enliv_modpaiment", "total"=>new Zend_Db_Expr("SUM(enliv_montant)")))
            ->where("enliv_date LIKE ?", date("Y-m", strtotime($date))."%")
            ->group("enliv_modpaiment");

    $data2=$this->fetchAll($select2);
    foreach($data2 as $r){
      @$return[$r->enliv_modpaiment]+=$r->total;
    }

    return $return;
  }

  public function getTotalYear($date){
    $select=$this->select();
    $select->from($this, array("total"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y", strtotime($date))."%")
            ->where("statut_cmd ='O'")
    ;

    return number_format($this->fetchRow($select)->total, 2, ",", " ");
  }

  public function getTotalMonth($date){
    $select=$this->select();
    $select->from($this, array("total"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y-m", strtotime($date))."%")
            ->where("statut_cmd ='O'")
    ;

    return number_format($this->fetchRow($select)->total, 2, ",", " ");
  }

  public function getTotalDay($date){
    $select=$this->select();
    $select->from($this, array("total"=>new Zend_Db_Expr("SUM(total_cmd)")))
            ->where("date_cmd LIKE ?", date("Y-m-d", strtotime($date))."%")
            ->where("statut_cmd ='O'")
    ;

    return number_format($this->fetchRow($select)->total, 2, ",", " ");
  }

}

