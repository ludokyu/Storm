<?php

class Admin_ContactController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
         $this->view->headTitle("Administration");
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
                $this->_helper->redirector("index","index");
         else{
            $i=$auth->getIdentity();
            $this->view->identity=$i;
        }
        $this->_helper->layout->setLayout("admin");
        $this->view->headLink()->prependStylesheet($this->view->BaseUrl('/css/style.css'))
            ->headLink()->appendStylesheet($this->view->BaseUrl('/css/admin.css'))
            ->headLink(array('rel' => 'favicon','href' => $this->view->BaseUrl('/favicon.ico')),'PREPEND');
        $this->view->headScript()->appendFile($this->view->BaseUrl("/js/jquery.js"))
            ->appendFile($this->view->BaseUrl("/js/admin.js"))
             ->appendFile($this->view->BaseUrl("/js/script.js"));
        $bootstrap = $this->getInvokeArg('bootstrap');

        // Retrouve l'espace de nom de l'application.

        $ns = rtrim($bootstrap->getAppNamespace(), '_');

        // Récupère les paramètres sous la forme d'un tableau
        $config = $bootstrap->getOption($ns);

        $this->view->Storm_version = $config['version'];

        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                                          'basePath'  => '../application/modules/admin',
                                          'namespace' => 'Admin',
                                      ));


        $resourceLoader->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable')
            ->addResourceType('form', 'forms', 'Form');
    }

    public function indexAction()
    {
        // action body
         $this->view->title ="Liste des contacts";
        $this->view->headTitle()->prepend($this->view->title);
        $Table=new Admin_Model_DbTable_Contact();
        $this->view->list=$Table->getAll();
    }

    public function livreurAction()
    {
        // action body
       $this->view->title ="Liste des livreurs";
        $this->view->headTitle()->prepend($this->view->title);
        $Table=new Admin_Model_DbTable_Contact();
        $this->view->list=$Table->search("",1);

    }

    public function addAction()
    {
        // action body
        $this->view->title ="Ajouter un contact";
        $this->view->headTitle()->prepend($this->view->title);
        $form=new Admin_Form_Livreur($this->getRequest()->getParam("is_liv",0));
        //Zend_Debug::dump($form);
        $data["is_liv"]=$this->getRequest()->getParam("is_liv",0);
       $form->populate($data);
        $this->view->form=$form;
         if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            unset($formData["submit"]);
             $Table=new Admin_Model_DbTable_Contact();
             $Table->insert($formData);
           if($formData["is_liv"]==1)
                  $this->_helper->redirector("livreur","contact");
           else  $this->_helper->redirector("index","contact");
        }
    }

    public function updateAction()
    {
        // action body
         $this->view->title ="Modifier un contact";
        $this->view->headTitle()->prepend($this->view->title);
        $Table=new Admin_Model_DbTable_Contact();

        $form=new Admin_Form_Livreur($this->getRequest()->getParam("is_liv",0));
        $row=$Table->get($this->getRequest()->getParam("id_livreur"));

        $form->populate($row->toArray());
        $this->view->form=$form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            unset($formData["submit"]);

         $Table->update($formData,"id_livreur=".$formData["id_livreur"]);
             if($formData["is_liv"]==1)
                  $this->_helper->redirector("livreur","contact");
           else  $this->_helper->redirector("index","contact");
        }

    }

    public function deleteAction()
    {
        // action body
        $id=$this->getRequest()->getParam("id_livreur");
         $Table=new Admin_Model_DbTable_Contact();
         $Table->update(array("statut_livreur"=>"X"),"id_livreur=$id");
         if($this->getRequest()->getParam("is_liv")==1)
               $this->_helper->redirector("livreur","contact");
           else  $this->_helper->redirector("index","contact");
    }

    public function searchAction()
    {
        // action body
         $this->_helper->layout->disableLayout();

        $Table=new Admin_Model_DbTable_Contact();
        $this->view->list=$Table->search($this->getRequest()->getParam("search"),$this->getRequest()->getParam("livreur",0));

    }


}











