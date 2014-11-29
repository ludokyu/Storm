<?php

class Admin_ClientController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */ $auth=Zend_Auth::getInstance();
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

    public function indexAction()
    {
        // action body
      $form= new Storm_Form_Default("searchClient");
      $form->NewElement("text", "name", "Nom",array("value"=>$this->getRequest()->getParam("name"),
          "attribs"=>array("placeholder"=>"nom du client","onkeyup"=>"searchClient(this.value,1)")));
      $this->view->form=$form;
      
      
      
    }

    public function searchAction()
    {
        // action body
      
      if($this->getRequest()->getParam("ajax",0)=="1")
         $this->_helper->layout->disableLayout();

      $Client=new Admin_Model_DbTable_Client();
      $clients=$Client->searchClient($this->getRequest()->getParam("name",""));
     $this->view->clients=$clients;
     $this->view->search=$this->getRequest()->getParam("name","");
     $pages=array();
     $this->view->type_rue=$Client->getType_rue();
     for($i=1;$i<=ceil(count($clients)/25);$i++)
      $pages[$i]=$i;
    
     $this->view->pages=$pages;
     $this->view->page=$this->getRequest()->getParam("page",1);
    }


}



