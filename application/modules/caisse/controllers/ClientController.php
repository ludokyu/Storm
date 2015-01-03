<?php

class Caisse_ClientController extends Zend_Controller_Action{

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
   
      $this->cmd=new Zend_Session_Namespace("cmd");
   
  }

  public function indexAction(){

    $form=$this->getForm();
    $this->view->form=$form;

    if($this->getRequest()->isPost()){
      $formData=$this->getRequest()->getPost();
      if($formData["type_cmd"]!=3){
        $form->adresse_client->setRequired(false);
        $form->cp->setRequired(false);
        $form->nom_ville->setRequired(false);
      }
      $data=$form->processAjax($_POST);
      if($data=="true"){
        // succès!

        $data_insert=$formData;
        unset($data_insert["type_cmd"]);
        $data_insert["code_postal"]=$data_insert["cp"];
        unset($data_insert["cp"]);
        unset($data_insert["id_client"]);
        $client=new Caisse_Model_DbTable_Client();
        if($formData["id_client"]==""){
          //insert client
          $id_client=$client->add($data_insert);
          echo "id_client=".$id_client."|";
          $this->cmd->id_client=$id_client;
        }
        else{
          //update;;

          $id=$formData["id_client"];
          $select=$client->select();
          $select->from($client, "id_client")
                  ->where("id_client!= ?", $id)
                  ->where("tel_client = ?", $data_insert["tel_client"]);
          $row=$client->fetchAll($select);
          if($row->count()==0){
            $client->updateclient($data_insert, $id);
            $this->cmd->id_client=$id;
          }
          else{
            echo "id_client=".$row[0]->id_client."|";
            $this->cmd->id_client=$row[0]->id_client;
          }
        }
        $this->_helper->json->sendJson($data);
      }
      else{
        // echec!
        $this->_helper->json->sendJson($data);
      }
    }
    else{
      if($this->getRequest()->getParam("id_cmd", "")!=""){
        $cmd=new Caisse_Model_DbTable_Cmd();
        $client=$cmd->find($this->getRequest()->getParam("id_cmd"));
        //Zend_Debug::dump($client);
        $form->type_cmd->setValue($client[0]->type_cmd);
        $this->getRequest()->setParam("id_client", $client[0]->id_client);
      }
      $id_client=$this->getRequest()->getParam("id_client", 0);
      if($id_client!=0){
        $client=new Caisse_Model_DbTable_Client();
        $c=$client->getById($id_client);
        $ville=new Caisse_Model_DbTable_Ville();
        if($c[0]["nom_ville"]==""){
          $v=$ville->getVille($c[0]["id_ville"]);
          //$form->id_ville->addMultiOption($c[0]["id_ville"],htmlentities($v[0]["nom_ville"]));
          $c[0]["cp"]=$v[0]["code_postal"];
          $c[0]["nom_ville"]=$v[0]["nom_ville"];
        }
        else
          $c[0]["cp"]=$c[0]["code_postal"];
        $form->populate($c[0]);
      }
    }
  }

  public function getForm(){

    return new Caisse_Form_Client();
  }

  public function telclientAction(){

    $this->_helper->viewRenderer->setNoRender();
    $tel=$this->getRequest()->getParam('tel');
    $client=new Caisse_Model_DbTable_Client();
    $result=$client->getByTel($tel);

    echo (!is_null($result)) ? $result->id_client : "no";
  }

  public function searchrueAction(){

    $type_rue=$this->getRequest()->getParam('type_rue');
    $rue=$this->getRequest()->getParam('rue');
    $client=new Caisse_Model_DbTable_Client();
    $result=$client->getAddress($type_rue, $rue);
    $this->view->liste=$result;
  }

  public function totalcmdAction(){

    $id=$this->getRequest()->getParam('id_client');
    $this->view->verif=0;

    if($id!="undefined"&&$id!=""){
      $this->view->verif=1;
      $cmd=new Caisse_Model_DbTable_Cmd();
      $r=$cmd->Totalclient($id);
      $this->view->totalclient=$r;
      $this->view->verif=(is_null($r)) ? 0 : 1;
    }
  }

  public function searchnameAction(){

    $form=new Zend_Form("search_client");
    $form->setAction("/caisse/client/searchname")
            ->setName("search_client")
            ->setAttrib("onsubmit", "return false");
    $element=new Zend_Form_Element_Text("name");
    $element->setLabel("Nom :")
            ->addDecorators(array(
                array("HtmlTag"),
                array("Label")
            ))
            ->setAttrib("onkeyup", "searchname(event,\$(this).val())");
    $form->addElement($element);

    $this->view->form=$form;

    if($this->getRequest()->isPost()){

      $formData=$this->getRequest()->getPost();
      $form->populate($formData);
      $data_insert=$formData;

      $client=new Caisse_Model_DbTable_Client();
      if(isset($data_insert["name"])&&$data_insert["name"]!=""){
        $clients=$client->getClientByName($data_insert["name"]);

        $this->view->clients=$clients;
        $this->view->rue=$client->getType_rue();
      }
    }
  }

  public function searchvilleAction(){

    $cp=$this->getRequest()->getParam('cp');

    $ville=new Caisse_Model_DbTable_Ville();
    $result=$ville->VilleBycp($cp);
    $this->view->liste=$result;
  }

}

