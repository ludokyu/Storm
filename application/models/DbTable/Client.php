<?php

class Storm_Model_DbTable_Client extends Zend_Db_Table_Abstract
{

    protected $_name = 't_client';
     public $type_rue=array("1"=>"Rue","  ","Allée","Avenue","Boulevard","Chaussée","Chemin","Cité","Clos",
			"Esplanade","Faubourg","Impasse","Passage","Place","Pont","Promenoir","Quai", "Rampe",
			"Résidence","Route","Ruelle","Square","Tour","Voie","Z.I","Lotissement","Villa",
			"Careffour","Côte","Cour anglaise","Cours","Degré","Liaison","Mail","Montée","Placette",
			"Gaffe","Rond-Point","Rang","Traboule","Traverse","Venelle","Berge","Cul-de-sac","Escalier",
			"Giratoire","Jardin","Parvis","Passerelle","Rua");

     public function getType_rue($id=0){
		if($id!=0){
			$type_rue=$this->type_rue;
			return $type_rue[$id];
		}
		else
			return $this->type_rue;	
	}
}

