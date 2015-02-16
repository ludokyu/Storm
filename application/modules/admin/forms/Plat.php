<?php

class Admin_Form_Plat extends Storm_Form_Default{

  public function init($name="modif_plat"){
    /* Form Elements & Other Definitions Here ... */
    parent::init($name);
    $this->setName($name);
    $this->setAction("/admin/plat/plat");
    $this->setAttrib("onsubmit", "submit_form('#modif_plat'); return false");
    
     $this->NewElement("text", "place", "Sur place", array('description'=>'€', "order"=>1, "attribs"=>array("style"=>"width:40px")));
    $this->NewElement("text", "go", "Emporter", array('description'=>'€', "order"=>2, "attribs"=>array("style"=>"width:40px")));
    $this->NewElement("text", "liv", "Livraison", array('description'=>'€', "order"=>3, "attribs"=>array("style"=>"width:40px")));
    $this->addDisplayGroup(array('go', 'place', 'liv'), 'global_prix', array("legend"=>"Prix"));

    $this->NewElement("hidden", "id_plat", "", array('order'=>5));
    $this->NewElement("hidden", "id_cat", "", array('order'=>6));
    $this->NewElement("text", "nom_plat", "Nom", array('order'=>7));
   

    $Table_base=new Admin_Model_DbTable_Base();
    $bases=$Table_base->listbase();
    $option=array();
    foreach($bases as $b){
      $option[$b->id_base]=$b->nom_base;
    }
    $this->NewElement("select", "base_pizza", "Choisissez la sauce comme base par defaut de votre plat", array("options"=>$option, "order"=>8));

    $Table_ingt=new Admin_Model_DbTable_Ingt();
    $bases=$Table_ingt->listAll();
    $option=array();
    foreach($bases as $b){
      $option[$b->id_ingt]=$b->nom_ingt;
    }
    $this->NewElement("MultiCheckbox", "list_ingt", "Choix des ingrédients de votre plat", array("options"=>$option, "separator"=>" ", "order"=>9, "attribs"=>array("label_style"=>"clear:none;font-size:10pt;width:120px;")));
    $this->NewElement("button", "add_menu", "Ajouter un composant à votre menu", array("attribs"=>array("onclick"=>"addMenu()"), "order"=>10));
    $this->NewElement("hidden", "count_menu", "", array("value"=>0, "order"=>11));
  }

  public function getMenu($i){
    $ordre=$this->count_menu->getOrder();

    $no=$ordre+(($i-1)*12)+1;
    // $no=$this->count()+1;
    $this->count_menu->setValue($this->count_menu->getValue()+1);

    $no=1;
    foreach($this as $el){
      if($no<=$el->getOrder())
        $no=$el->getOrder()+1;
    }


    $this->NewElement("hidden", "id_menu_$i", "", array("order"=>$no++));

    $this->NewElement("text", "qte_$i", "", array("attribs"=>array("style"=>"width:30px;clear:both"), "order"=>$no++));
    $cat=new Admin_Model_DbTable_Categorie();
    $c=$cat->listCategorieNotMenu();
    $option=array();
    foreach($c as $l){
      $array=array("is_taille"=>$l->is_taille);
      $options[]=array("value"=>$l["id_cat"], "label"=>$l["nom_cat"], "attribs"=>$array);
    }

    $param_cat=array("required"=>true,
        "attribs"=>array("onchange"=>"listPlat(this,$i)"),
        "options"=>$options,
        "order"=>$no++);

    $this->NewElement("selectattrib", "id_cat_$i", "", $param_cat);

    $this->NewElement("select", "taille_$i", "", array("order"=>$no++));
    $this->NewElement("button", "affich_list_plat_$i", "Modifier la liste des plats", array("order"=>$no++,
        "attribs"=>array("style"=>"margin:5px", "onclick"=>"\$('#fieldset-div_list_plat_$i').css('display','block')")));
    $this->NewElement("select", "id_plat_default_$i", "", array("order"=>$no++, "attribs"=>array("style"=>"max-width:180px;")));
    // 
    $this->NewElement("button", "close_list_plat_$i", "Fermer", array("order"=>$no++, "attribs"=>array("style"=>"margin:5px", "onclick"=>"\$('#fieldset-div_list_plat_$i').css('display','none')")));

    $this->NewElement("multicheckbox", "list_plat_$i", "", array("order"=>$no++, "attribs"=>array("label_style"=>"clear:both", "onclick"=>"updateListDefault($i)"), "decorators"=>array("htmltag"=>array("tag"=>"div", "id"=>"list_plat_$i"))));
    //,"decorators"=>array("HtmlTag"=>array("tag"=>"div","id"=>array("container-list_plat_$i"),"class"=>array("list_container")))
    $this->NewElement("button", "del_plat_$i", "Supprimer", array("order"=>$no++, "attribs"=>array("style"=>"float:right", "onclick"=>"if(confirm('Confirmez vous la suppression de cet élément ?')){delMenu($i)}")));
    $this->addDisplayGroup(array("close_list_plat_$i", "list_plat_$i"), "div_list_plat_$i", array("class"=>"list_container"));
    $this->{"div_list_plat_$i"}->setOrder($no++)
            ->removeDecorator("DtDdWrapper");



    return $no;
  }

  public function render(Zend_View_Interface $view=null){

    $this->NewElement("button", "cancel", "Annuler", array("attribs"=>array("style"=>"clear:both;", "onclick"=>"\$('#form').css('display','none')")));
    if($this->getElement("id_plat")->getValue()!="")
      $this->NewElement("submit", "submitBtn", "Modifier", array("attribs"=>array("onclick"=>"")));
    else
      $this->NewElement("submit", "submitBtn", "Ajouter", array("attribs"=>array("onclick"=>"")));
    
      $max=1;
      foreach($this as $el){
        if($max<$el->getOrder())
          $max=$el->getOrder();
      }
    $this->addDisplayGroup(array('cancel', 'submitBtn'), 'group_submit', array("style"=>"border:none;","order"=>$max+1));

    return parent::render($view);
  }

}

