<?php

class Storm_Model_DbTable_Ingt extends Zend_Db_Table_Abstract
{

    protected $_name = 't_ingt';

    public function getListIngtPlus($list){
        if(is_array($list)) $list=implode(",",$list);
        $select=$this->select();
        $select->from($this,new Zend_Db_Expr("GROUP_CONCAT(nom_ingt SEPARATOR '\n+') AS result"))
        ->where("id_ingt IN ( $list )");
        $res=$this->fetchRow($select);

        return utf8_encode(html_entity_decode($res->result));
    }

    public function getListIngtMoins($list){
        if(is_array($list)) $list=implode(",",$list);
        $select=$this->select();
        $select->from($this,new Zend_Db_Expr("GROUP_CONCAT(nom_ingt SEPARATOR '\n-') AS result"))
        ->where("id_ingt IN ( $list )");

        $res=$this->fetchRow($select);
        return utf8_encode(html_entity_decode($res->result));
    }

    public function listAll($where=""){

        $select=$this->select();
        $select->from($this,array("nom_ingt","id_ingt","prix_sup","prix_inv"));
        if($where!="")
        $select->where($where);
        $select->order("nom_ingt");

        return $this->fetchAll($select);
    }
}

