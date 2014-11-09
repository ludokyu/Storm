<?php

class Caisse_Model_DbTable_Ingt extends Storm_Model_DbTable_Ingt
{

    protected $_name = 't_ingt';

    public function CalculSupp($cat,$taille,$plus,$moins){
        $total=0;



        $db_plus=$this->listAll("id_ingt IN ($plus)");
        $db_moins=$this->listAll("id_ingt IN ($moins)");
        foreach($db_plus as $p){

            $prix_supp=unserialize($p->prix_sup);
            $total+=$prix_supp[$cat][$taille];

        }
        foreach($db_moins as $p){
            $prix_supp=unserialize($p->prix_sup);
            $total-=$prix_supp[$cat][$taille];

        }
        if($total<0)$total=0;
        return $total;

    }


}

