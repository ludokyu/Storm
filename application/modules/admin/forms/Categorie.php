<?php

class Admin_Form_Categorie extends Storm_Form_Default
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init("modif_cat");
        $this->setName("modif_cat");
        $this->setAction("/admin/plat/cat");
        $this->setAttrib("onsubmit","submit_form('#modif_cat'); return false");
        $this->NewElement("hidden","id_cat","");
        $this->NewElement("text","nom_cat","Nom");
        $this->NewElement("radio","is_menu","Est ce un menu",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none"),"options"=>array("1"=>"Oui","0"=>"Non")));
        $this->NewElement("radio","is_compo","Contient plusieurs ingredients",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none"),"decorators"=>array("Label"=>array("style"=>"width:100%")),"options"=>array("1"=>"Oui","0"=>"Non")));
        $this->NewElement("radio","is_taille","Plusieurs tailles sont possible",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none","onclick"=>"changeTaille(this.value);"),"decorators"=>array("Label"=>array("style"=>"width:100%")),"options"=>array("1"=>"Oui","0"=>"Non")));
        $this->NewElement("select","nb_taille","Nombre de taille",array("attribs"=>array("disabled"=>"disabled", "onchange"=>"add_taille(this.value);"),"options"=>array("1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5")));

        $this->NewElement("radio","is_pizza","Est il possible de séparer ce plat en 2 moitié",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none"),"decorators"=>array("Label"=>array("style"=>"width:100%")),"options"=>array("1"=>"Oui","0"=>"Non")));

        $this->NewElement("radio","is_base","Est-il possible d'utiliser différentes sauce cette catégorie",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none"),"decorators"=>array("Label"=>array("style"=>"width:100%")),"options"=>array("1"=>"Oui","0"=>"Non")));

        $this->NewElement("radio","afficher","Afficher le nom de cette catégorie sur le ticket",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none"),"decorators"=>array("Label"=>array("style"=>"width:100%")),"options"=>array("1"=>"Oui","0"=>"Non")));
        $this->NewElement("radio","big_on_ticket","Mettre en évidence le nom des plats de cette catégorie sur le ticket ",array("attribs"=>array("style"=>"float:none;margin-top:0px","label_style"=>"clear:none"),"decorators"=>array("Label"=>array("style"=>"width:100%")),"options"=>array("1"=>"Oui","0"=>"Non")));

        $this->NewElement("button","cancel","Annuler",array("attribs"=>array("style"=>"clear:both;","onclick"=>"\$('#form').css('display','none')")));
        $this->NewElement("submit","submit","Ajouter",array("attribs"=>array("onclick"=>"")));
    }


}

