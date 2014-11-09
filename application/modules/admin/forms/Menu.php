<?php

class Admin_Form_Menu extends Storm_Form_Default{

  public function init(){
    /* Form Elements & Other Definitions Here ... */
    $this->NewElement("hidden", "count_menu[]", "");
    $this->NewElement("text", "qte[]", "", array("attribs"=>array("style"=>"width:30px")));
    $cat=new Admin_Model_DbTable_Categorie();
    $c=$cat->listCategorieNotMenu();
    $option=array();
    foreach($c as $l){
      $array=array("is_taille"=>$l->is_taille);
      $options[]=array("value"=>$l["id_cat"], "label"=>$l["nom_cat"], "attribs"=>$array);
    }

    $param_cat=array("required"=>true,
        "attribs"=>array("onkeyup"=>"cat_Suivant(this,event)"),
        "options"=>$options);

    $this->NewElement("selectattrib", "cat[]", "", $param_cat);

    $this->NewElement("select", "taille_menu[]", "");

    $this->NewElement("multicheckbox", "list_plat[]", "");

    $this->NewElement("button", "list_plat[]", "");
  }

}

