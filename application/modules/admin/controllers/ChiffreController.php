<?php

class Admin_ChiffreController extends Zend_Controller_Action{

  public $month=array(
      '',
      'Janvier',
      'Fevrier',
      'Mars',
      'Avril',
      'Mai',
      'Juin',
      'Juillet',
      'Aout',
      'Septembre',
      'Octobre',
      'Novembre',
      'Decembre'
  );

  public function init(){
    /* Initialize action controller here */

    $auth=Zend_Auth::getInstance();
    if(!$auth->hasIdentity())
      $this->_helper->redirector("index", "index");
    else{
      $i=$auth->getIdentity();
      $this->view->identity=$i;
    }
    $this->_helper->layout->setLayout("admin");
    $this->view->headLink()->prependStylesheet($this->view->BaseUrl('/css/style.css'))
            ->headLink()->appendStylesheet($this->view->BaseUrl('/css/admin.css'))
            ->headLink(array('rel'=>'favicon', 'href'=>$this->view->BaseUrl('/favicon.ico')), 'PREPEND');
    $this->view->headScript()->appendFile($this->view->BaseUrl("/js/jquery.js"))
            ->appendFile($this->view->BaseUrl("/js/admin.js"));
    $bootstrap=$this->getInvokeArg('bootstrap');

    // Retrouve l'espace de nom de l'application.

    $ns=rtrim($bootstrap->getAppNamespace(), '_');

    // Récupère les paramètres sous la forme d'un tableau
    $config=$bootstrap->getOption($ns);

    $this->view->Storm_version=$config['version'];

    $resourceLoader=new Zend_Loader_Autoloader_Resource(array(
        'basePath'=>'../application/modules/admin',
        'namespace'=>'Admin',
    ));


    $resourceLoader->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable')
            ->addResourceType('form', 'forms', 'Form');
  }

  public function indexAction(){
    // action body
    $this->view->headTitle("Administration");
    $form=new Admin_Form_Date();
    $this->view->form=$form;
    $y=$this->getRequest()->getParam("year", date("Y"));
    $m=$this->getRequest()->getParam("month", date("m"));
    $d=$this->getRequest()->getParam("day", date("d"));
    $this->view->title="Détail des commandes du $d/$m/$y";
    $this->view->headTitle()->prepend($this->view->title);
    $this->view->y=$y;
    $this->view->m=$m;
    $this->view->d=$d;
    $form->year->setValue($y);
    $Cmd=new Admin_Model_DbTable_Cmd();
    $date=$Cmd->getMonthCmdFromYear($y);
    $month=explode(",", $date->month);
    sort($month);
    $mois=array();
    foreach($month as $val)
      $mois[$val]=$this->month[intval($val)];
    $form->month->setMultiOptions($mois);

    $form->month->setValue($m);
    $date=$Cmd->getDayCmdFromYearMonth($y, str_pad($m, 2, 0, STR_PAD_LEFT));
    $day=explode(",", $date->day);
    sort($day);

    $jour=array("");
    foreach($day as $val)
      $jour[$val]=$val;
    $form->day->setMultiOptions($jour);
    $form->day->setValue($d);
    $this->view->headScript()->appendScript("$(document).ready(function(){
            $('body').attr('onkeyup','admin_event(event,\"/year/$y/month/$m/day/$d\")');
            });");
  }

  public function monthAction(){
    $this->_helper->layout->disableLayout();
    $Cmd=new Admin_Model_DbTable_Cmd();
    $date=$Cmd->getMonthCmdFromYear($this->getRequest()->getParam("year"));
    $month=explode(",", $date->month);
    sort($month);
    echo "<option/>";
    foreach($month as $val)
      echo "<option value='$val'>".$this->month[intval($val)]."</option>\n";
  }

  public function dayAction(){
    $this->_helper->layout->disableLayout();
    $Cmd=new Admin_Model_DbTable_Cmd();
    $date=$Cmd->getDayCmdFromYearMonth($this->getRequest()->getParam("year"), $this->getRequest()->getParam("month"));
    $day=explode(",", $date->day);
    sort($day);

    echo "<option/>";
    foreach($day as $val)
      echo "<option value='".$val."'>".$val."</option>\n";
  }

  public function listcmdAction(){
    // action body
    $y=$this->getRequest()->getParam("year", date("Y"));
    $m=$this->getRequest()->getParam("month", date("m"));
    $d=$this->getRequest()->getParam("day", date("d"));

    $Cmd=new Admin_Model_DbTable_Cmd();
    $this->view->total_jour=$Cmd->getTotalFromDate("$y-$m-$d")->total_jour;
    $this->view->cmd=$Cmd->getCmdByDate("$y-$m-$d");
  }

  public function recapmonthAction(){
    // action body
    $y=$this->getRequest()->getParam("year", date("Y"));
    $m=$this->getRequest()->getParam("month", date("m"));
    $d=$this->getRequest()->getParam("day", date("d"));
    $this->view->month=date("Y-m", strtotime("$y-$m-$d"));
    $Cmd=new Admin_Model_DbTable_Cmd();
    $Reg=new Admin_Model_DbTable_Reglement();
    $r=$Reg->getAll();
    $tab_reglement=array();
    foreach($r as  $re){
      $tab_reglement[$re->code_reglement]=$re->nom_reglement;
    }
    $this->view->tabReglement=$tab_reglement;
    $this->view->cmdMonth=$Cmd->getRecapMonth("$y-$m-$d");
    $this->view->RecapReglement=$Cmd->getRecapPaiementMonth("$y-$m-$d");
    $this->view->month=$this->month;
  }

  public function recetteAction(){
    // action body
    $y=$this->getRequest()->getParam("year", date("Y"));
    $m=$this->getRequest()->getParam("month", date("m"));
    $d=$this->getRequest()->getParam("day", date("d"));

    $Cmd=new Admin_Model_DbTable_Cmd();
    $this->view->totalYear=$Cmd->getTotalYear("$y-$m-$d");
    $this->view->totalMonth=$Cmd->getTotalMonth("$y-$m-$d");
    $this->view->totalDay=$Cmd->getTotalDay("$y-$m-$d");

    $this->view->year=$y;
    $this->view->month=$this->month[intval($m)];
    $this->view->day=$d;
  }

  public function detailcmdAction(){
    // action body
    $this->_helper->layout->disableLayout();

    $id_cmd=$this->getRequest()->getParam("id_cmd");
    $cmd=new Admin_Model_DbTable_Cmd();
    $res_cmd=$cmd->getCmd($id_cmd);
    $this->view->cmd=$res_cmd;
    $this->view->cmd->date_cmd=date("d-m-Y à H:i", strtotime($res_cmd->date_cmd));

    $panier=new Admin_Model_DbTable_Panier();
    $res_cmd=$panier->getPanierCmdforPrint($id_cmd);
    $this->view->list_panier=$res_cmd;
    $total_cmd=0;

    foreach($res_cmd as $panier){
      //Zend_Debug::dump($panier);
      $total_cmd+=$panier->prix_panier;

      if(!empty($panier->moins_ingt)){

        $Ingt=new Admin_Model_DbTable_Ingt();
        $res_ingt=$Ingt->getListIngtMoins($panier->moins_ingt);
        $panier->nom_moins=nl2br($res_ingt);
      }

      if(!empty($panier->plus_ingt)){
        $Ingt=new Admin_Model_DbTable_Ingt();
        $res_ingt=$Ingt->getListIngtPlus($panier->plus_ingt);

        $panier->nom_plus=nl2br($res_ingt);
      }

      if($panier->is_menu){

        $Menu=new Admin_Model_DbTable_PanierMenu();
        $res_menu=$Menu->getPanierMenu($panier);

        $panier->menu=$res_menu;
      }
    }
    $this->view->total_cmd=$total_cmd;
  }

  public function deleteAction(){
    // action body
    $Panier=new Admin_Model_DbTable_Panier();
    $Panier->delPanierFromCmd($this->getRequest()->getParam("id_cmd", 0));
    $Cmd=new Admin_Model_DbTable_Cmd();
    $Cmd->update(array("statut_cmd"=>"X"), "id_cmd=".$this->getRequest()->getParam("id_cmd"));
    $this->_helper->redirector("index", "chiffre", "admin", array("year"=>$this->getRequest()->getParam("year"),
        "month"=>$this->getRequest()->getParam("month"),
        "day"=>$this->getRequest()->getParam("day")));
  }

}

