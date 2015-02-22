<?php

class Admin_Form_Livreur extends Storm_Form_Default
{
    protected $_is_liv;
    public function __construct($is_liv=0){

        $this->_is_liv=$is_liv;
    parent::__construct();
    }
    public function init($name="livreur")
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init($name);
        $this->NewElement("hidden","id_livreur","");
        $this->NewElement("hidden","is_liv","",array("attribs"=>array("value"=>0)));

        $this->NewElement("text","nom_livreur","Nom du livreur");
        $this->NewElement("text","tel_livreur","Téléphone du livreur");
        $this->NewElement("checkbox","is_fav","Afficher dans la liste des livreurs",array("attribs"=>array("value"=>0)));

         $this->NewElement("button","cancel","Annuler",array("attribs"=>array("style"=>"clear:both",
             "onclick"=>"location.href='". $this->baseurl->url(array("action"=>(($this->_is_liv==1)?"livreur":"index"),"controller"=>"contact","module"=>"admin"),null,"default")."'")));
        $this->NewElement("submit","submit","Valider",array("attribs"=>array("style"=>"clear:none")));
    }


}

