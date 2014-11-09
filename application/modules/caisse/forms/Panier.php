<?php

class Caisse_Form_Panier extends Storm_Form_Default
{

    public function init()
    {

       parent::init("form_panier");
        $this->setAction("/caisse/panier/insert/");
    $this->setAttrib("onsubmit","return false;");

    $this->NewElement("hidden","id_panier","");

    $param_qte=array("required"=>true,
            "options"=>array(1=>1,2,3,4,5,6,7,8,9,10),
            "attribs"=>array("style"=>"width:80px;","onkeydown"=>"t=this;setTimeout(function(){suivant($(t),event);},100);"));

    $this->NewElement("select","qte","",$param_qte);

    $c=new Caisse_Model_DbTable_Categorie();
    $l=$c->listCategorie();

    $options=array();
    foreach($l as $val){
      $array=array();
      $array["is_pizza"]= ($val["is_pizza"] ==1) ? 1 : 0;
      $array["is_base"] = ($val["is_base"]  ==1) ? 1 : 0;
      $array["is_menu"] = ($val["is_menu"]  ==1) ? 1 : 0;
      $array["is_compo"]= ($val["is_compo"] ==1) ? 1 : 0;
      $array["is_taille"]=($val["is_taille"] ==1)? 1 : 0;
      if($val["is_default"] ==1){
        $value=$val["id_cat"];
      }
      $options[]=array("value"=>$val["id_cat"],"label"=>$val["nom_cat"],"attribs"=>$array);

    }

    $param_cat=array("required"=>true,
              "attribs"=>array("onkeydown"=>"t=this;setTimeout(function(){if(verif_client(event))cat_Suivant($(t),event)},100);"),
            "options"=>$options,
            "value"=>$value);

    $this->NewElement("selectattrib","cat","",$param_cat);



      $param_plat=array("RegisterInArrayValidator"=>false,
              "required"=>true,
              "attribs"=>array("onkeydown"=>"t=this;setTimeout(function(){modif_plat($(t),event)},100);"));
      $this->NewElement("select","plat","",$param_plat);

      $param_plat2=array("RegisterInArrayValidator"=>false,
                "attribs"=>array("onkeydown"=>"setTimeout(function(){modif_plat(\$('tr.trhighlight #plat'),event)},100);"),
              "decorators"=>array("Label"=>array("style"=>"width:5px")));
      $this->NewElement("select","plat_2","",$param_plat2);

      $base= new Caisse_Model_DbTable_Base();
      $bases=$base->listbase();

      $option=array();
      foreach ($bases as $value) {
        $option[$value->id_base]=$value->nom_base;
      }
      $param_base=array("attribs"=>array("onkeyup"=>"modif_plat(\$('tr.trhighlight #plat'),event);"),
                    "decorators"=>array("Label"=>array("style"=>"margin-left:180px;width:40px;")),
                    "options"=>$option);
      $this->NewElement("select","base","Base",$param_base);

      $param_ingt=array("options"=>array(0=>""),
                "attribs"=>array("style"=>"width:150px;","onkeydown"=>"t=this;setTimeout(function(){add_supp($(t),event);},100);"),
                "RegisterInArrayValidator"=>false);
      $this->NewElement("select","select_ingt","",$param_ingt);

      $param_plus=array("value"=>0,
              "decorators"=>array("Description"=>array( 'class'=>'pre')),
              "description"=>"-");
      $this->NewElement("hidden","list_moins","",$param_plus);
      $this->NewElement("hidden","list_plus","",$param_plus);



    $param_taille=array("attribs"=>array("onkeydown"=>"t=this;setTimeout(function(){get_prix($(t),event);},100);","style"=>"width:80px;margin-left:10px;"),
              "RegisterInArrayValidator"=>false);
    $this->NewElement("select","taille","",$param_taille);

    $param_prix=array("description"=>"€",
            "attribs"=>array("style"=>"width:50px;margin-left:15px;","onkeyup"=>"suivant(this,event);affich_total();\$('.trhighlight #rmq').val(trim(\$('.trhighlight #rmq').val()));"),
            "validators"=>array("Float"));
    $this->NewElement("text","prix","",$param_prix);

    $param_rmq=array("attribs"=>array("style"=>"width:180px;/*height:43px*/;float:left","onkeyup"=>"valid_panier(this,event)"));
    $this->NewElement("text","rmq","",$param_rmq);


     $this->addDisplayGroup(    array('id_panier','qte','cat','plat','plat_2','base'), 'global_plat');
       $this->global_plat->removeDecorator("DtDdWrapper");

      $this->addDisplayGroup(    array('select_ingt','list_plus','list_moins'), 'ingt_plat');
         $this->ingt_plat->removeDecorator("DtDdWrapper");
     $this->addDisplayGroup(    array('taille','prix','rmq'), 'detail_plat');
        $this->detail_plat->removeDecorator("DtDdWrapper");
    }


}

