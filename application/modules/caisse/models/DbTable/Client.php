<?php

class Caisse_Model_DbTable_Client extends Storm_Model_DbTable_Client
{

    protected $_name = 't_client';
   	public function getById($id){
		$r=$this->find($id);
       	return $r->toArray();	
	}
	
	public function getInfo($id){
		$select=$this->select();
		$select->from($this,array("nom_client","societe","no_addr","type_rue","adresse_client","tel_client","code_postal","nom_ville"))
		->where("id_client=$id");
		return $this->fetchRow($select);
	}
	
	
	public function getByTel($tel){
		$select  = $this->select();
       		$select->where("tel_client LIKE '".$tel."'");
       	return $this->fetchRow($select);	
		
	}
	public function getAddress($type_rue,$rue){
		$select  = $this->select();
       		$select->from($this,"adresse_client")
       		->where("type_rue=".$type_rue)
       		->where("UPPER (adresse_client) LIKE '".strtoupper(addslashes($rue))."%'")
       		->group("adresse_client");
       	return $this->fetchAll($select);	
		
	}
	public function getClientByName($name){ //obtenir la liste des client par le debut de leur nom
		$select  = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
		 ->setIntegrityCheck(false);
       		$select->join(array('v'=>'t_ville'),
              'v.id_ville = '.$this->_name.'.id_ville')
       		->where("nom_client LIKE '".$name."%'")
       		->order("nom_client");
       	return $this->fetchAll($select);	
		
	}
	public function add($data){
			return $this->insert($data);
		
	}
	public function updateclient($data,$id){
			$this->update($data,"id_client=".(int)$id);
		
	}
	public function getMaxIdClient(){
		$select=$this->select();
		$select->from($this,array("id"=>"MAX(id_client)"));
		$return = $this->fetchRow($select);
		return $return->id;
	}
	
}

