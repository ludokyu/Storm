<?php

class Caisse_PanierController extends Zend_Controller_Action{

  public function init(){
    $this->_helper->layout->disableLayout();
    $resourceLoader=new Zend_Loader_Autoloader_Resource(array(
        'basePath'=>'../application/modules/caisse',
        'namespace'=>'Caisse',
    ));

    $resourceLoader->addResourceType('model', 'models', 'Model')
            ->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable')
            ->addResourceType('form', 'forms', 'Form')
            ->addResourceType('view', 'views', 'View')
            ->addResourceType('helper', 'views/helpers', 'Helper');

    if(Zend_Registry::isRegistered('firephp')) 
       $r=Zend_Registry::get('firephp');

 
      $cmd=new Zend_Session_Namespace("cmd");
      if(!isset($cmd->panier))
      $cmd->panier=array();
      else
        $r->log($cmd->panier,Zend_Log::INFO);
      
      $this->cmd=$cmd;
  }

  public function indexAction(){
    
  }

  public function addlineAction(){
    
    

    $no=$this->getRequest()->getParam("no");

    $form=$this->getForm($no);
    $form->removeElement("plat_2");
    $form->removeElement("base");
    $this->view->form=$form;

    $this->view->no=$no;
    
  }

  public function getForm($no){

    $form=new Caisse_Form_Panier();

    $form->qte->setAttrib("tabindex", $no."01");
    $form->cat->setAttrib("tabindex", $no."02");
    $form->plat->setAttrib("tabindex", $no."03");
    $form->select_ingt->setAttrib("tabindex", $no."04");
    $form->taille->setAttrib("tabindex", $no."05");
    $form->prix->setAttrib("tabindex", $no."06");
    $form->rmq->setAttrib("tabindex", $no."07");
    return $form;
  }

  public function getprixAction(){
    // action body
    $qte=$this->getRequest()->getParam("qte");
    $cat=$this->getRequest()->getParam("cat");
    ;
    $id_prod=$this->getRequest()->getParam("prod");
    $type_cmd=$this->getRequest()->getParam("type_cmd");
    $prix_supp=0;

    $taille=$this->getRequest()->getParam("taille", 0);


    if($this->getRequest()->getParam("ingt_p", "")!=""){
      $p=$this->getRequest()->getParam("ingt_p");
      $m=$this->getRequest()->getParam("ingt_m", 0);

      $Ingt=new Caisse_Model_DbTable_Ingt();
      $prix_supp=$Ingt->CalculSupp($cat, $taille, $p, $m);
    }


    if($type_cmd==1)
      $champ="place";
    elseif($type_cmd==2)
      $champ="go";
    elseif($type_cmd==3)
      $champ="liv";

    $plat=new Caisse_Model_DbTable_Plat();

    $pr=$plat->find($id_prod);

    $prix_plat=explode(",", $pr[0][$champ]);

    if($taille==0)
      $prix=$prix_plat[0]+$prix_supp;
    else
      $prix=$prix_plat[$taille-1]+$prix_supp;
    $prix=$qte*$prix;

    $this->view->prix=$prix;
  }

  public function insertAction(){
    // action body*
    $this->_helper->viewRenderer->setNoRender();
     $db_panier=new Caisse_Model_DbTable_Panier();
   // $form=$this->getForm();
    $formData=$this->getRequest()->getPost();

    $db_cat=new Caisse_Model_DbTable_Categorie();
    $c=$db_cat->is_menu($formData["cat"]);
    $menu=new Caisse_Model_DbTable_PanierMenu();
    $data=array("id_cat"=>$formData["cat"], "id_plat"=>$formData["plat"], "plus_ingt"=>$formData["list_plus"],
        "moins_ingt"=>$formData["list_moins"], "taille"=>$formData["taille"], "prix_panier"=>$formData["prix"],
        "rmq"=>$formData["rmq"], "qte_panier"=>$formData["qte"]);
    if(isset($formData["plat_2"]))
      $data["id_plat_2"]=$formData["plat_2"];
    if(isset($formData["base"]))
      $data["id_base"]=$formData["base"];

    if($formData["id_panier"]!=""){
      $last=$formData["id_panier"];
      $this->cmd->panier[$last]=$data;
       $db_panier->update($data, "id_panier=".$last);
      if($c->is_menu!=1)
        $menu->truncate($last);
    }
    else{
       $last_d=$db_panier->addtopanier($data);
      
      if(!isset($this->cmd->panier))
        $this->cmd->panier=array();
      $this->cmd->panier[]=$data;
      $last=key($this->cmd->panier);
      echo $last_d;
      if($c->is_menu==1)
        $menu->insertInpanier($last_d);
      else
        $menu->truncate();
    }
    if(Zend_Registry::isRegistered('firephp')) {
       $r=Zend_Registry::get('firephp');
      $r->log($this->cmd->panier,  Zend_Log::INFO);
       
     }
  }

  public function truncateAction(){
    $this->_helper->viewRenderer->setNoRender();
    $panier=new Caisse_Model_DbTable_Panier();
    $panier->truncate();
    $this->cmd->panier=array();
  }

  public function affichcmdAction(){
    // action body
    $id_cmd=$this->getRequest()->getParam("id_cmd");
    $cmd=new Caisse_Model_DbTable_Panier();
    $list_panier=$cmd->getPanierCmd($id_cmd);
    $view=array();
    $i=1;
    foreach($list_panier as $val){
      $data=array();
      $data["id"]=$i;

      $data["id_panier"]=$val->id_panier;

      $form=$this->getForm($i);


      $form->plat->addMultiOption($val->id_plat, $val->nom_plat);
      if($val->tab_taille!="")
        $form->taille->addMultiOptions(unserialize($val->tab_taille));


      if(is_null($val->id_plat_2))
        $form->removeElement("plat_2");
      else{
        $plat=new Caisse_Model_DbTable_Plat();
        $nom_plat=$plat->find($val->id_plat_2);
        $form->plat_2->addMultiOption($val->id_plat_2, $nom_plat[0]["nom_plat"]);
      }
      if(is_null($val->id_base))
        $form->removeElement("base");
      else
        $form->base->setValue($val->id_base);

      $a=$val->toArray();
      $a["cat"]=$val->id_cat;
      $a["qte"]=$val->qte_panier;
      $a["prix"]=$val->prix_panier;
      $a["list_plus"]=$val->plus_ingt;
      $a["list_moins"]=$val->moins_ingt;
      $ingt=new Caisse_Model_DbTable_Ingt();
      $resultp=$ingt->getListIngtPlus($val->plus_ingt);

      $form->list_plus->setDescription((($resultp=="") ? "" : "+ ".$resultp));
      $form->list_plus->getDecorator("Description")->setOption("style", "visibility:visible");
      $resultm=$ingt->getListIngtMoins($val->moins_ingt);
      $form->list_moins->setDescription((($resultm!="") ? "- ".($resultm) : ""));
      $form->list_moins->getDecorator("Description")->setOption("style", "visibility:visible");
      $form->populate($a);
      $data["form"]=$form;
      $view[]=$data;
      $i++;
    }
    //Zend_Debug::dump($view);
    $this->view->view=$view;
  }

  public function delpanierAction(){
    // action body
    $Panier=new Caisse_Model_DbTable_Panier();
    $Panier->deletePanier($this->getRequest()->getParam("id_panier"));
    $PanierM=new Caisse_Model_DbTable_PanierMenu();
    $PanierM->deletePanier($this->getRequest()->getParam("id_panier"));
  }

}

