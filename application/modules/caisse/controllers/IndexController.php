<?php

class Caisse_IndexController extends Zend_Controller_Action{

  public function init(){

    $this->_helper->layout->setLayout("caisse");

    $this->view->headScript()->appendFile($this->view->baseUrl()."/js/jquery.js", 'text/javascript')
            ->appendFile($this->view->baseUrl()."/js/script.js", 'text/javascript')
            //->appendFile($this->view->baseUrl()."/include/js.php",'text/javascript')
            ->appendFile($this->view->baseUrl()."/js/cmd.js", 'text/javascript')
            ->appendFile($this->view->baseUrl()."/js/client.js", 'text/javascript')
    ;
    $this->view->headLink()->prependStylesheet($this->view->BaseUrl().'/css/style.css')
            ->prependStylesheet($this->view->BaseUrl().'/css/client.css')
            ->prependStylesheet($this->view->BaseUrl().'/css/panier.css')
            ->headLink(array('rel'=>'favicon', 'href'=>$this->view->BaseUrl().'/favicon.ico'), 'PREPEND');

    $resourceLoader=new Zend_Loader_Autoloader_Resource(array(
        'basePath'=>'../application/modules/caisse',
        'namespace'=>'Caisse',
    ));

    $resourceLoader->addResourceType('model', 'models', 'Model')
            ->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable');
  }

  public function indexAction(){
    $this->view->headScript("script", "if(window.opener==null)
            location.href='".$this->view->url(array("action"=>"index", "controller"=>"index", "module"=>"default"), null, "default")."'");
    $config=new Zend_Config_Ini(APPLICATION_PATH.'/configs/storm.ini');
    if($config->module_map){
      $this->view->headScript()->appendFile("http://maps.google.com/maps/api/js?sensor=false", "text/javascript");
      $script="
                function init() {
                    var lat={$config->lat};
                    var lng={$config->lng};
                    var lat_flag={$config->lat_flag};
                    var lng_flag={$config->lng_flag};
                    var flag = new google.maps.LatLng( lat_flag ,lng_flag);

                    /*gestion des routes*/
                    directionsDisplay = new google.maps.DirectionsRenderer();
                    /*emplacement par défaut de la carte (j'ai mis Paris)*/
                    var maison = new google.maps.LatLng(lat, lng);
                    /*option par défaut de la carte*/
                    var myOptions = {
                        zoom:15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: maison
                    }

                    /*creation de la map*/
                    map = new google.maps.Map(document.getElementById('divMap'), myOptions);
                    var marker = new google.maps.Marker({
                        position: maison,
                        map: map
                    });
                    var marker2 = new google.maps.Marker({
                        position: flag,
                        map: map,
                        icon:'images/{$config->logo_map}'
                    });
                    /*connexion de la map + le panneau de l'itinéraire*/
                    directionsDisplay.setMap(map);
                    directionsDisplay.setPanel(document.getElementById('divRoute'));
                    /*intialise le geocoder pour localiser les adresses */
                    geocoder = new google.maps.Geocoder();

                }
                function reinit(){
                    $('#divRoute').html('');
                    $('#divRoute').css('display','none');

                    waypoint=new Array();
                    arrivee=undefined;
                    init();
                }

                var directionsService = new google.maps.DirectionsService();
                var map,geocoder, marker;
                var depart,arrivee,ptCheck;

                $(document).ready(function(){
                    init();
                });";
      $this->view->headScript("script", $script);
    }
  }

  public function listcmdAction(){
    $this->_helper->layout->disableLayout();

    if($this->getRequest()->getParam("action")=="listcmd"){
      
    }
    $cmd=new Caisse_Model_DbTable_Cmd();

    $list_cmd=$cmd->fetchToday();
    $detail_list_cmd=array();
    foreach($list_cmd as $val){
      $data=array();
      $data["no_cmd"]=$val->no_cmd;
      $data["id_cmd"]=$val->id_cmd;
      switch($val->type_cmd){
        case 1:
          $data["type_cmd"]="SUR PLACE";
          break;
        case 2:
          $data["type_cmd"]="EMPORTER";
          break;
        case 3:
          $data["type_cmd"]="LIVRAISON";
          break;
      }
      if($val->id_client!=0){
        $client_class=new Caisse_Model_DbTable_Client();
        $client=$client_class->getInfo($val->id_client);

        $data["client"]=$client->societe." ".$client->nom_client." \n";
        $data["client"].=$client->no_addr." ".$client_class->type_rue[$client->type_rue]." ".$client->adresse_client."\n";
        $data["client"].=$client->code_postal." ".$client->nom_ville."\n";
        $data["client"].=$client->tel_client;
      }
      else
        $data["client"]="";

      $panier_class=new Caisse_Model_DbTable_Panier();
      $panier=$panier_class->getPanierCmd($val->id_cmd);
      $data["panier"]=array();
      foreach($panier as $value){
        $data_panier=array();
        $data_panier["qte"]=$value->qte_panier;
        $data_panier["nom_cat"]=$value->nom_cat;
        $data_panier["nom_plat"]=$value->nom_plat;
        $tab_taille=unserialize($value->tab_taille);
        $data_panier["taille"]=($value->taille!=0) ? $tab_taille[$value->taille] : "";
        $data["panier"][]=$data_panier;
      }
      $data["total"]=$val->total_cmd;
      $date=new Zend_Date($val->date_cmd);
      $data["heure"]=$date->ConvertDate("H:i");
      if($val->type_cmd==3){
        $livreur=new Caisse_Model_DbTable_Livreur();
        $res=$livreur->getAllLivreur();
        $select=new Zend_Form_Element_Select("livreur");
        $select->setAttrib("onkeyup", "livreur(event,$('table#list_cmd_global tr.hightlight').attr('id_cmd'),this.value)");
        $select->addMultiOption(0, "");
        foreach($res as $row){

          $select->addMultiOption($row->id_livreur, $row->nom_livreur);
        }
        $select->setValue($val->id_livreur)
                ->removeDecorator("Label")
                ->removeDecorator("HtmlTag")
                ->removeDecorator("DtDdWrapper");
        $data["livreur"]=$select;
      }
      else
        $data["livreur"]="";
      if($val->type_cmd!=3){
        $reg=new Caisse_Model_DbTable_Reglement();
        $res=$reg->getAll();
        $reglement=new Zend_Form_Element_Select("reglement");
        $reglement->setAttrib("onkeyup", "reglement(event,$('table#list_cmd_global tr.hightlight').attr('id_cmd'),this.value)");
        foreach($res as $row){

          $reglement->addMultiOption($row->code_reglement, $row->nom_reglement);
        }
        $reglement->setValue($val->etat_paiment)
                ->removeDecorator("Label")
                ->removeDecorator("HtmlTag")
                ->removeDecorator("DtDdWrapper");

        $data["reglement"]=$reglement;
      }
      else
        $data["reglement"]="";
      $detail_list_cmd[]=$data;
    }
    $this->view->list=$detail_list_cmd;
  }

  public function recetteAction(){
    // action body
    $cmd=new Caisse_Model_DbTable_Cmd();
    $Regl=new Caisse_Model_DbTable_Reglement();
    $reg=$Regl->getAll();

    $this->view->livreur=$livreurs=$cmd->recetteLiv();
    $livreurs2=$cmd->recetteLiv();
    $encaissement=array();
    $total_encaissement_livreur=array();
    
    foreach($livreurs as $liv){
        
      $paiement=new Storm_Form_Default();
      $paiement->setName("form_encaissement_".$liv->id_livreur);
      $paiement->setAction("/caisse/index/encaisseLiv/");
      $paiement->setAttrib("onsubmit", "return false;");


      $paiement->NewElement("hidden", "id_livreur", "", array("value"=>$liv->id_livreur));
      $total_encaissement=0;
      foreach($reg as $r){
        if($r->code_reglement!=0){
          $enc=$cmd->getEncaissementLiv($liv->id_livreur, $r->code_reglement);
          $paiement->NewElement("float", "encaissement_".$r->code_reglement, $r->nom_reglement, array("value"=>$enc, "attribs"=>array("onblur"=>"calcul_encaissement('".$liv->id_livreur."')"), "decorators"=>array("Label"=>array("style"=>"clear:both;"), "htmltag"=>array("style"=>"position:relative;clear:both"))));

          $total_encaissement+=$enc;
        }
      }
      foreach($livreurs2 as $l){
        if($liv->id_livreur==$l->id_livreur)
        $paiement->NewElement("hidden", "total", "", array("value"=>$l->total));
      }
      $paiement->NewElement("submit", "btnSubmit", "Encaisser", array("decorators"=>array("htmltag"=>array("tag"=>"br")), "attribs"=>array("onclick"=>"encaissement('".$liv->id_livreur."')")));
     // $paiement->NewElement("hidden", "html", "", array("style"=>"display:none", "decorators"=>array("htmltag"=>array("tag"=>"br"))));
      $total_encaissement_livreur[$liv->id_livreur]=$total_encaissement;
      $encaissement[$liv->id_livreur]=$paiement;
      
    }

   
    $this->view->encaissement=$encaissement;
    $this->view->total_encaissement_livreur=$total_encaissement_livreur;
  }

  public function insertlivreurAction(){
    // action body
    $this->_helper->layout->disableLayout();

    $cmd=new Caisse_Model_DbTable_Cmd();
    $cmd->affecterLivreurToCmd($this->getRequest()->getParam("id_cmd"), $this->getRequest()->getParam("value"));

    $this->view->livreur=$cmd->recetteLiv();
    $this->recetteAction();
    $this->render("recette");
  }

  public function verifpwdAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $config=new Zend_Config_Ini(APPLICATION_PATH.'/configs/storm.ini');

    $val=$this->getRequest()->getParam("value");
    if($config->pwd_cmd==md5($val))
      echo 1;
    else
      echo 0;
  }

  public function insertcmdAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $data=array();
    $panier=new Caisse_Model_DbTable_Panier();
    if($this->getRequest()->getParam("id_client")!="")
      $data["id_client"]=$this->getRequest()->getParam("id_client");
    elseif($this->getRequest()->getParam("type_cmd")==3){
      $client=new Caisse_Model_DbTable_Client();
      $data["id_client"]=$client->getMaxIdClient();
    }
    $cmd=new Caisse_Model_DbTable_Cmd();

    $data["no_cmd"]=intval($cmd->getMaxNoCmd())+1;
    $data["type_cmd"]=$this->getRequest()->getParam("type_cmd");

    $id_cmd=$cmd->insert($data);
    $session_cmd=new Zend_Session_Namespace("cmd");
    foreach($session_cmd->panier as $data){
      $panier->insertPanierToCmd($id_cmd, $data);
    }
    echo $id_cmd;
  }

  public function updatecmdAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $data=array();
    $panier=new Caisse_Model_DbTable_Panier();
    if($this->getRequest()->getParam("id_client")!="")
      $data["id_client"]=$this->getRequest()->getParam("id_client");
    elseif($this->getRequest()->getParam("type_cmd")==3){
      $client=new Caisse_Model_DbTable_Client();
      $data["id_client"]=$client->getMaxIdClient();
    }
    $cmd=new Caisse_Model_DbTable_Cmd();

    $data["type_cmd"]=$this->getRequest()->getParam("type_cmd");


    $session_cmd=new Zend_Session_Namespace("cmd");
    $id_cmd=$session_cmd->id_cmd;

    $panier->truncate($id_cmd);
    foreach($session_cmd->panier as $data){
      $panier->insertPanierToCmd($id_cmd, $data);
    }
    echo $id_cmd;
  }

  public function insertpanierAction(){
    $id_cmd=$this->getRequest()->getParam("id_cmd", 0);
    $panier=new Caisse_Model_DbTable_Panier();
    /* $session_cmd=new Zend_Session_Namespace("cmd");
      foreach($session_cmd->panier as $key=> $data){
      $panier->insertPanierToCmd($id_cmd,$data);
      } */
    $panier->insertPanierToCmd($id_cmd);

    $cmd=new Caisse_Model_DbTable_Cmd();
    $cmd->updateDateModif($id_cmd);
  }

  public function totalcmdAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $id_cmd=$this->getRequest()->getParam("id_cmd");
    $panier=new Caisse_Model_DbTable_Panier();
    $total=$panier->getTotalCmd($id_cmd);

    $cmd=new Caisse_Model_DbTable_Cmd();
    $cmd->update(array("total_cmd"=>$total), "id_cmd=$id_cmd");
  }

  public function printAction(){
    // action body
    $this->_helper->layout->setLayout("print");

    $config=new Zend_Config_Ini(APPLICATION_PATH.'/configs/storm.ini');
    $this->view->config=$config;

    if($this->getRequest()->getParam("id_cmd", 0)==0)
      $this->_helper->viewRenderer->setNoRender();
    else{
      $id_cmd=$this->getRequest()->getParam("id_cmd");
      $cmd=new Caisse_Model_DbTable_Cmd();
      $res_cmd=$cmd->getCmd($id_cmd);
      $this->view->cmd=$res_cmd;
      $this->view->cmd->date_cmd=date("d-m-Y à H:i", strtotime($res_cmd->date_cmd));

      $panier=new Caisse_Model_DbTable_Panier();
      $res_cmd=$panier->getPanierCmdforPrint($id_cmd);
      $this->view->list_panier=$res_cmd;
      foreach($res_cmd as $panier){

        if(!empty($panier->moins_ingt)){

          $Ingt=new Caisse_Model_DbTable_Ingt();
          $res_ingt=$Ingt->getListIngtMoins($panier->moins_ingt);
          $panier->nom_moins=nl2br($res_ingt);
        }

        if(!empty($panier->plus_ingt)){
          $Ingt=new Caisse_Model_DbTable_Ingt();
          $res_ingt=$Ingt->getListIngtPlus($panier->plus_ingt);

          $panier->nom_plus=nl2br($res_ingt);
        }

        if($panier->is_menu){

          $Menu=new Caisse_Model_DbTable_PanierMenu();
          $res_menu=$Menu->getPanierMenu($panier);

          $panier->menu=$res_menu;
        }
      }
    }


    if($config->module_print&&$config->os=="unix"){

      $html=ob_get_contents();
      $fp=fopen(realpath(APPLICATION_PATH)."/".$id_cmd.".html", "w+");

      fwrite($fp, $html);
      fclose($fp);
      $exec="lpr -d{$config->printer}  ".realpath(APPLICATION_PATH)."/".$id_cmd.".html ";
      exec($exec, $output);


      //unlink(realpath(APPLICATION_PATH)."/".$this->cmd->id_cmd.".pdf");
    }
  }

  public function insertpaiementAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $cmd=new Caisse_Model_DbTable_Cmd();
    $cmd->affecterPaiementToCmd($this->getRequest()->getParam("id_cmd"), $this->getRequest()->getParam("value"));

    $res=$cmd->getPaiementToday();
    $retour=array();
    foreach($res as $val){
      $retour[$val["id_livreur"]][]=$val["nom_reglement"]." : ".$val["total"]." &euro;";
    }
    echo json_encode($retour);
  }

  public function cancelAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $cmd=new Caisse_Model_DbTable_Cmd();
    $cmd->update(array("statut_cmd"=>"A"), "id_cmd=".$this->getRequest()->getParam("id_cmd"));
  }

  public function encaisselivAction(){
    // action body
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $Regl=new Caisse_Model_DbTable_Reglement();
    $Cmd=new Caisse_Model_DbTable_Cmd();
    $reg=$Regl->getAll();
    $liv=$this->getRequest()->getParam("id_livreur");
    $paiement=new Storm_Form_Default();
    $paiement->setName("form_encaissement_".$liv);
    $paiement->setAction("/caisse/index/encaisseLiv/");
    $paiement->setAttrib("onsubmit", "return false;");

    $paiement->NewElement("hidden", "id_livreur", "", array("value"=>$liv));

    foreach($reg as $r){
      if($r->code_reglement!=0){

        $paiement->NewElement("float", "encaissement_".$r->code_reglement, $r->nom_reglement, array("validators"=>array(array("Float", false, array("locale"=>"en"))),
            "ErrorMessage"=>"'%value%' ne semble pas être un nombre",
            "value"=>$this->getRequest()->getParam("encaissement_".$r->code_reglement),
            "attribs"=>array("onblur"=>"calcul_encaissement('".$liv."')"),
            "decorators"=>array("label"=>array("style"=>"clear:both;"), "htmltag"=>array("tag"=>"br"))));
      }
    }
    $paiement->NewElement("button", "btnSubmit", "Encaisser", array("decorators"=>array("htmltag"=>array("tag"=>"br")), "attribs"=>array("onclick"=>"encaissement('".$liv."')")));
    $paiement->NewElement("hidden", "html", "", array("style"=>"display:none", "decorators"=>array("htmltag"=>array("tag"=>"br"))));
    $data=$paiement->processAjax($paiement->getValues());
    // print_r($paiement);
    if($data=="true"){
      $values=$paiement->getValues();
      foreach($values as $key=> $value){
        @list($champ, $mode)=explode("_", $key);
        if($champ=="encaissement"&&!empty($value)){
          $data_db=array("en_id_livreur"=>$values["id_livreur"], "enliv_modpaiment"=>$mode, "enliv_montant"=>$value);


          $Cmd->encaissement($data_db);
        }
      }
      //$this->_helper->json->sendJson($data);
    }
    else{
      // echec!
      $this->_helper->json->sendJson($data);
    }
  }

}

