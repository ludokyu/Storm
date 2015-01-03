<?php

class Caisse_Form_Menu extends Storm_Form_Default{

  public function init($name="form_menu"){
    /* Form Elements & Other Definitions Here ... */
    parent::init($name);

    $this->setAction("/caisse/menu");
    $this->NewElement("hidden", "id_panier", "", array("order"=>1));
  }

  public function getForm($id_plat){

    $menu=new Caisse_Model_DbTable_Menu();
    $m=$menu->fetchAll("id_plat=$id_plat AND statut_menu='O'");
    $i=0;
    $tabindex="9001";
    $count=10;
    foreach($m as $val){
      $cat=new Caisse_Model_DbTable_Categorie();
      $c=$cat->find($val->id_cat);
      $nom_cat=$c[0]["nom_cat"];
      $is_taille=$c[0]["is_taille"];
      $tab_taille=unserialize($c[0]["tab_taille"]);

      $taille=($is_taille) ? $tab_taille[$val->taille] : "";
      $plat=new Caisse_Model_DbTable_Plat();
      $p=$plat->listplat($val->id_cat, $val->list_plat);

      for($t=1; $t<=$val->qte; $t++){

        $option=array();
        foreach($p as $key=> $value){
          $option[$value->id_plat]=$value->nom_plat;
        }
        $param_element=array("order"=>$count, "RegisterInArrayValidator"=>false,
            "value"=>$val->id_plat_default,
            "attribs"=>array("tabindex"=>"$tabindex", "onkeydown"=>"t=this;setTimeout(function(){suivant($(t),event);},100);",
                "onclick"=>"$('#div_menu dl.highlight').removeClass('highlight');$(this).parent('dl').addClass('highlight')",
                "is_compo"=>$c[0]["is_compo"], "is_base"=>$c[0]["is_base"], "is_pizza"=>$c[0]["is_pizza"], "id_cat"=>$val->id_cat),
            "options"=>$option);
        $this->NewElement("select", "id_plat_$i", "1 $nom_cat $taille", $param_element);

        $this->NewElement("hidden", "id_menu_$i", "", array("order"=>($count+3), "value"=>$val->id_menu));



        if($c[0]["is_compo"]==1){

          $this->NewElement("hidden", "ingt_plus_$i", "", array("description"=>"1","decorators"=>array("description"=> array('tag'=>'p', 'class'=>'description')), "order"=>($count+4), "value"=>0));
         
          $this->NewElement("hidden", "ingt_moins_$i", "", array("description"=>"1","decorators"=>array("description"=> array('tag'=>'p', 'class'=>'description')), "order"=>($count+5), "value"=>0));
          
         
        }
        $this->addDisplayGroup(array("id_plat_$i", "id_menu_$i", "ingt_plus_$i", "ingt_moins_$i"), "menu_$i");
        $this->{"menu_".$i}->removeDecorator("DtDdWrapper");


        $i++;
        $tabindex++;
        $count+=10;
      }
    }

    $this->NewElement("hidden", "count", "", array("value"=>$i));

    $this->NewElement("submit", "submit_menu", "Envoyer", array("order"=>$count,
        "attribs"=>array("tabindex"=>$tabindex, "onkeyup"=>"suivant(this,event)", "style"=>"clear:both;margin-left:160px;")));

    $this->setAttrib("onsubmit", "return false");
  }

}

