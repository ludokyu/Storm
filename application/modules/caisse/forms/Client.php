<?php

class Caisse_Form_Client extends Storm_Form_Default
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init("form_client");

        $this->setAction("/caisse/client");

        $param_type_cmd=array("options"=>array(1=>"Sur Place",2=>"Emporter",3=>"Livraison"),
                            "value"=>3,
                            "separator"=>" ",
                            "order"=>1,
                            "attribs"=>array("tabindex"=>'1',
                            "onKeyup"=>"if(\$(this).val()==3){suivant(this,event)}else{start_cmd(event)}"));
        $this->NewElement("radio","type_cmd","",$param_type_cmd);

        $this->NewElement("hidden","id_client","");

        $param_tel=array("required"=>true,
                            "validators"=>array(array("stringLength",false,array(10))),
                            "ErrorMessage"=>"le n° ''%value%' ne contient pas 10 chiffres",
                            "attribs"=>array("tabindex"=>"2",
                                            "onkeyup"=>"suivant(this,event)",
                                            "onBlur"=>"if(\$(this).val()!=''){setTimeout('Searchtel(\$(\'input[name=tel_client]\').val())',100);}" .
                "				else{setTimeout('if(\$(\'input[name=type_cmd]:focus\').val()==undefined){" .
                "					\$(\'input[name=tel_client]\').focus();}',100);" .
                "				}"),"decorators"=>array("Label"=>array("style"=>"clear:both")));

        $this->NewElement("text","tel_client","Téléphone",$param_tel);

        $param_soc=array("attribs"=>array("tabindex"=>"3","onkeyup"=>"suivant(this,event)"),"ErrorMessage"=>"un nom est nécessaire");
        $this->NewElement("text","societe","Société",$param_soc);

        $param_name=array("required"=>true,"attribs"=>array("tabindex"=>'4',"style"=>'width:140px',"onKeyup"=>"suivant(this,event)"),
                    "ErrorMessage"=>"un nom est nécessaire");
        $this->NewElement("text","nom_client","Nom",$param_name);

        $param_no=array("attribs"=>array("tabindex"=>'5',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","no_addr","Adresse",$param_no);


        $c=new Caisse_Model_DbTable_Client();
        $rue=$c->getType_rue();
        asort($rue);
        $param_typerue=array("value"=>1,"attribs"=>array("tabindex"=>6,"onKeydown"=>"setTimeout(function (){suivant($(\"#type_rue\"),event)},100);"),
            "options"=>$rue,"RegisterInArrayValidator"=>false);
        $this->NewElement("select","type_rue","",$param_typerue);

        $param_addr=array("description"=>"null",
                                "required"=>true,
                                "ErrorMessage"=>"une adresse est nécessaire",
                          //      "datalist"=>array("test","des francs bourgeois"),
                                "decorators"=>array("Description"=>array("tag"=>"span","id"=>"liste_rue")),
                                "attribs"=>array("onKeyup"=>"suivant(this,event);","onfocus"=>"Searchrue_new()","tabindex"=>'7',"style"=>'width:160px','list'=>'datalist_adresse'));
        $this->NewElement("text","adresse_client","",$param_addr);

        $param_code=array("required"=>true,
                                "ErrorMessage"=>"un code postal est nécessaire",
                                "attribs"=>array("tabindex"=>'8',"onKeyup"=>"suivant(this,event)","onblur"=>"search_ville(\$(this).val())"));
        $this->NewElement("text","cp","Code Postal",$param_code);

        $param_ville=array("required"=>true,
                                "description"=>"null","ErrorMessage"=>"une ville est nécessaire",

                                "attribs"=>array("tabindex"=>'9',"onKeyup"=>"suivant_ville(this,event);",
                                                 "list"=>"datalist_ville",
                                                "onfocus"=>"search_ville(\$(\"#cp\").val())",
                                                "onblur"=>'setTimeout("$(\"#nom_ville+p\").css(\"display\",\"none\")",10)'));

        $this->NewElement("text","nom_ville","",$param_ville);


        $param_bat=array("attribs"=>array("tabindex"=>'10',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","bat","Bât.",$param_bat);

        $param_ent=array("attribs"=>array("tabindex"=>'11',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","entree","Entrée.",$param_ent);

        $param_et=array("attribs"=>array("tabindex"=>'12',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","etage","Etage.",$param_et);

        $param_appt=array("attribs"=>array("tabindex"=>'13',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","appt","Appt.",$param_appt);

        $param_p=array("attribs"=>array("tabindex"=>'14',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","porte","Porte",$param_p);

        $param_digit=array("attribs"=>array("tabindex"=>'15',"onKeyup"=>"suivant(this,event)"));
        $this->NewElement("text","digicode","Digicode",$param_digit);


        $param_rmq=array("attribs"=>array("tabindex"=>'16',"onKeyup"=>"valid_client(event)",
                                            "cols"=>38,"rows"=>"3"));
        $this->NewElement("textarea","rmq","Remarque",$param_rmq);


        $this->addDisplayGroup(array('id_client','tel_client', 'societe','nom_client','no_addr','type_rue',
          'adresse_client','cp','nom_ville'),  'global_addr');
        $this->global_addr->setOrder(2);
        $this->addDisplayGroup(array('bat', 'entree','etage','appt','porte','digicode','rmq'), 'detail_addr');
         $this->detail_addr->setOrder(3);
    }


}

