<?php

class Admin_Form_Parametre extends Storm_Form_Default
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init("parametre");
        $this->setName("parametre");

        $this->NewElement("text","PIZZA","Nom");
        $this->NewElement("text","TEL","Téléphone");
        $this->NewElement("text","ADDR","Adresse");

        $this->NewElement("text","siret","SIRET");


        $this->NewElement("text","web","Site web");


        $this->NewElement("radio","os","Système d'exploitation",array("options"=>array("win"=>"Windows","unix"=>"Unix"),
                                                                      "attribs"=>array("style"=>"float:none;margin-top:0px",
                                                                                       "label_style"=>"clear:none;width:100px;")));

        $this->NewElement("radio","module_print","Module Impression",array("options"=>array("Non","Oui"),
                                                                           "attribs"=>array("style"=>"float:none;margin-top:0px",
                                                                                            "label_style"=>"clear:none;width:100px;")));
        $this->NewElement("text","printer","Imprimante");
        $this->NewElement("radio","module_map","Module Map",array("options"=>array("Non","Oui"),
                                                                  "attribs"=>array("style"=>"float:none;margin-top:0px",
                                                                                   "label_style"=>"clear:none;width:100px;")));


        $this->NewElement("text","lat","latitude");
        $this->NewElement("text","lng","longitude");

        $this->NewElement("text","lat_flag","latitude du drapeau");
        $this->NewElement("text","lng_flag","longitude du drapeau");
        if(APPLICATION_ENV=="development")
          $this->NewElement("radio","sauvegarde_xml","Sauvegarde xml",array("options"=>array("Non","Oui"),
                                                                            "attribs"=>array("style"=>"float:none;margin-top:0px",
                                                                                             "label_style"=>"clear:none;width:100px;")));
        else
          $this->NewElement("radio","maj_bdd","Mise à jour de la BDD",array("options"=>array("Non","Oui"),
                                                                            "attribs"=>array("style"=>"float:none;margin-top:0px",
                                                                                             "label_style"=>"clear:none;width:100px;")));
        $this->NewElement("submit","submit","Valider",array("attribs"=>array("style"=>"clear:both;margin-left:200px;")));

    }


}

