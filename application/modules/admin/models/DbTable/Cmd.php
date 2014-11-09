<?php

class Admin_Model_DbTable_Cmd extends Storm_Model_DbTable_Cmd
{

    public function getYearCmd(){

         $select=$this->select();
      $select->from($this,array("date_min"=>new Zend_Db_Expr("MIN(date_cmd)"),"date_max"=>new Zend_Db_Expr("MAX(date_cmd)")));
    //	echo $select;
      return $this->fetchRow($select);
    }

    public function getMonthCmdFromYear($year){

         $select=$this->select();
         $select->from($this,array("month"=>new Zend_Db_Expr("GROUP_CONCAT(DISTINCT(SUBSTR(date_cmd,6,2)) SEPARATOR',')")))
         ->where("SUBSTR(date_cmd,1,4) = '$year'");
         return $this->fetchRow($select);
    }
    public function getDayCmdFromYearMonth($year,$month){

         $select=$this->select();
         $select->from($this,array("day"=>new Zend_Db_Expr("GROUP_CONCAT(DISTINCT(SUBSTR(date_cmd,9,2)) SEPARATOR',')")))
         ->where("SUBSTR(date_cmd,1,7) = '$year-$month'");

         return $this->fetchRow($select);
    }


    public function getTotalFromDate($date){
          $select=$this->select();
      $select->from($this,array("total_jour"=>new Zend_Db_Expr("SUM(total_cmd)")))
      ->where("date_cmd LIKE ?",date("Y-m-d",strtotime($date))."%");
      return $this->fetchRow($select);
    }
    public function getCmdByDate($date){

      $select=$this->select();
      $select->from($this,array("id_cmd","type_cmd","total_cmd","id_client"))
      ->where("date_cmd LIKE ?",date("Y-m-d",strtotime($date))."%")
      ->where("statut_cmd ='O'")
      ->order("date_cmd DESC");

      return $this->fetchAll($select);
    }

    public function getRecapMonth($date){

          $select=$this->select();
     $select->from($this,array("date_cmd","total"=>new Zend_Db_Expr("SUM(total_cmd)")))
     ->where("date_cmd LIKE ?",date("Y-m",strtotime($date))."%")
     ->group("SUBSTR(date_cmd,1,10)");

     return $this->fetchAll($select);
    }

    public function getTotalYear($date){
         $select=$this->select();
     $select->from($this,array("total"=>new Zend_Db_Expr("SUM(total_cmd)")))
     ->where("date_cmd LIKE ?",date("Y",strtotime($date))."%");

     return number_format($this->fetchRow($select)->total,2,","," ");
    }

    public function getTotalMonth($date){
         $select=$this->select();
     $select->from($this,array("total"=>new Zend_Db_Expr("SUM(total_cmd)")))
     ->where("date_cmd LIKE ?",date("Y-m",strtotime($date))."%");

     return number_format($this->fetchRow($select)->total,2,","," ");
    }

     public function getTotalDay($date){
         $select=$this->select();
     $select->from($this,array("total"=>new Zend_Db_Expr("SUM(total_cmd)")))
     ->where("date_cmd LIKE ?",date("Y-m-d",strtotime($date))."%");

     return number_format($this->fetchRow($select)->total,2,","," ");
    }
}

