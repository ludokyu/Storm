<?php

class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headTitle("Administration");
        $this->view->headLink()->prependStylesheet($this->view->BaseUrl('/css/style.css'))
            ->headLink()->appendStylesheet($this->view->BaseUrl('/css/admin.css'))
            ->headLink(array('rel' => 'favicon','href' => $this->view->BaseUrl('/favicon.ico')),'PREPEND');

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


        $resourceLoader->addResourceType('form', 'forms', 'Form');

    }

    public function indexAction()
    {
        // action body

        $form=new Admin_Form_Login();
        $this->view->form=$form;
        $this->view->title = "Authentification";
        $this->view->headTitle()->prepend($this->view->title);
        $form = new Admin_Form_Login();
        $this->view->message = '';
        $this->view->InlineScript()->appendScript("document.getElementById('username').focus();");
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            if ($form->isValid($values)) {
                Zend_Loader::loadClass('Zend_Filter_StripTags');
                $f = new Zend_Filter_StripTags();
                $username = $f->filter($values['username']);
                $password = $f->filter($values['password']);
                //

                $authAdapter = new Zend_Auth_Adapter_Digest( APPLICATION_PATH."/configs/pwd.ini","Admin",$username,$password );


                $auth = Zend_Auth::getInstance();

                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $this->view->message = 'Connexion réussi !';
                    $identite = $result->getIdentity();

                    $storage = $auth->getStorage();
                    $storage->write($identite);

                    $this->_helper->redirector("index","chiffre");

                }
                else
                        $this->view->message = 'Échec de la connexion !<br/>Identifiant ou mot de passe incorrect';
            }
            else
                $this->view->message = "Veuillez renseigner l'identifiant et le mot de passe !";
        }
    }

    public function logoutAction()
    {
        // action body
         $this->_helper->redirector("index","index","");
    }

    public function logAction()
    {
        // action body
        
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
   
    $this->view->php_error= file_get_contents(APPLICATION_PATH."/../data/logs/error.log");
    $this->view->zend_error= file_get_contents(APPLICATION_PATH."/../data/logs/app.log");
    }


}





