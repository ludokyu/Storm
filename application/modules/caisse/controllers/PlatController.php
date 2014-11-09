<?php

class Caisse_PlatController extends Zend_Controller_Action{

  public function init(){
    $this->_helper->layout->disableLayout();
    $resourceLoader=new Zend_Loader_Autoloader_Resource(array(
        'basePath'=>'../application/modules/caisse',
        'namespace'=>'Caisse',
    ));
    $resourceLoader->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable')
            ->addResourceType('form', 'forms', 'form')
            ->addResourceType('helper', 'views/helpers', 'View_Helper');
  }

  public function indexAction(){
    // action body
  }

  public function listplatAction(){
    // action body
    $cat=$this->getRequest()->getParam("id_cat", 1);
    $plat=new Caisse_Model_DbTable_Plat();
    $this->view->listoption=$plat->listplat($cat);
  }

  public function modifplatAction(){
    // action body
  }

  public function listbaseAction(){
    // action body


    $base=new Caisse_Model_DbTable_Base();
    $this->view->listoption=$base->listbase();
  }

  public function listingtAction(){
    // action body
    $p=$this->getRequest()->getParam("plat", 0);
    $k=$this->getRequest()->getParam("k", 0);

    $form=$this->getForm($p, $this->getRequest()->getParam("p", 0), $this->getRequest()->getParam("m", 0), $k);
    if($this->getRequest()->isPost()){
      $formData=$this->getRequest()->getPost();


      $this->_helper->viewRenderer->setNoRender();
      $plat=new Caisse_Model_DbTable_Plat();
      $list=$plat->find($p);

      $list_ingt_plat=$list[0]->list_ingt;
      $list_init=explode(",", $list_ingt_plat);

      $final=(isset($formData["ingt"])) ? $formData["ingt"] : array();
      $final=array_merge(explode(',', $formData["ingt_plat"]), explode(',', $formData["p"]), $final);
      $final=array_merge($final, explode(',', $formData["p"]));
      $double=array_intersect($list_init, explode(',', $formData["p"]));
      $ingt_plus=array_diff($final, $list_init);
      $ingt_moins=array_diff($list_init, $final);


      if(isset($formData["ingt_2"])&&$formData["ingt_2"]!=""){
        $double=array_merge($double, $formData["ingt_2"]);
      }
      $ingt_plus=array_merge($ingt_plus, $double);
      $ingt_moins[]="0";
      $ingt_plus[]="0";
      //print_r($ingt_plus);

      if(array_search("", $ingt_plus)!==false)
        unset($ingt_plus[array_search("", $ingt_plus)]);
      if(array_search("", $ingt_moins)!==false)
        unset($ingt_moins[array_search("", $ingt_moins)]);

      $ingt_plus=array_unique($ingt_plus);
      $ingt_moins=array_unique($ingt_moins);

      echo implode(",", $ingt_plus)."|".implode(",", $ingt_moins)."|";

      $ingt=new Caisse_Model_DbTable_Ingt();

      if(count($ingt_plus)>0){

        $resp=$ingt->listAll("id_ingt IN (".implode(",", $ingt_plus).")");
        foreach($resp as $p)
          echo "+ $p->nom_ingt\n";
      }
      echo "|";


      if(count($ingt_moins)>0&&(!isset($ingt_moins[0])||( isset($ingt_moins[0])&&$ingt_moins[0]!=""))){
        $resm=$ingt->listAll("id_ingt IN (".implode(",", $ingt_moins).")");
        foreach($resm as $m){
          echo "- $m->nom_ingt\n ";
        }
      }
    }
    else{


      $plat=new Caisse_Model_DbTable_Plat();
      $d=$plat->find($p);
      $l=$d[0]["list_ingt"];
      $l_array=explode(",", $l);

      $plus=explode(",", $this->getRequest()->getParam("p", 0));
      $moins=explode(",", $this->getRequest()->getParam("m", 0));

      $array_2=array_intersect($l_array, $plus); // on recupere les ingredient qui sont dans les tablaeeux afin de connaitre les 2eme a cocher
      $array_1=array_merge($l_array, $plus);
      foreach($moins as $m){
        if(array_search($m, $array_1)!==false)
          unset($array_1[array_search($m, $array_1)]);
      }
      $form->getElement("ingt")->setvalue($array_1);
      $form->getElement("ingt")->setvalue2($array_2);

      $this->view->form=$form;
    }
  }

  public function getForm($plat, $p, $m, $k=0){
    // action body
    $form=new Storm_Form_Default("form_ingt");
    $form->setAction("/caisse/plat/listingt")
            ->setName("form_ingt")
            ->addAttribs(array("onsubmit"=>"return false;"));
    $form->NewElement("hidden", "plat", "", array("value"=>$plat));

    $ingt=new Caisse_Model_DbTable_Ingt();
    $w="";

    if(is_string($k)){
      $w="nom_ingt LIKE '$k%'";
    }

    $i=$ingt->listAll($w);
    $list_form=array();

    $db_plat=new Caisse_Model_DbTable_Plat();
    $d=$db_plat->find($plat);
    $l=$d[0]["list_ingt"];
    $l_array=explode(",", $l);


    $option=array();
    foreach($i as $val){
      $option[$val->id_ingt]=html_entity_decode($val->nom_ingt, ENT_COMPAT, 'UTF-8');
      $list_form[]=$val->id_ingt;
    }


    $base=array_diff($l_array, $list_form);
    $base=array_diff($base, explode(',', $m));
    $form->NewElement("hidden", "ingt_plat", "", array("value"=>implode(',', $base)));

    $plus=array_diff(explode(',', $p), $list_form);

    $form->NewElement("hidden", "p", "", array("value"=>implode(',', $plus)));

    $param=array("separator"=>" ", "options"=>$option
    );
    $form->NewElement("DoubleMultiCheckbox", "ingt", "", $param);

    $form->NewElement("submit", "submit", "Envoyer");

    return $form;
  }

}