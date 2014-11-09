<?php

class Admin_SecuriteController extends Zend_Controller_Action
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
        $title="Modification des mots de passe";
           $this->view->title =$title;
        $this->view->headTitle()->prepend($this->view->title);
    }

    public function cmdAction()
    {
        // action body
        $title="Modification du mot de passe pour la modification des commandes ";
        $this->view->title =$title;
        $this->view->headTitle()->prepend($this->view->title);
        $form=new Storm_Form_Default();
        $form->NewElement("password","old","Ancien mot de passe",array("attribs"=>array("style"=>"width:150px")));
        $form->NewElement("password","new","Votre nouveau mot de passe",array("attribs"=>array("style"=>"width:150px")));
        $form->NewElement("submit","submit","Valider",array("attribs"=>array("style"=>"clear:both")));

        $this->view->form=$form;
         $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/storm.ini',null,array('skipExtends'=> true,'allowModifications' => true));


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if(md5($formData["old"])==$config->pwd_cmd){
                $config->pwd_cmd=md5($formData["new"]);
                $w=new Zend_Config_Writer_Ini(array('config'   => $config,
                                  'filename' => APPLICATION_PATH.'/configs/storm.ini'));
                $w->write();
                echo "Modification du mot de passe effectué.";
            }
            else{
                echo "l'ancien mot de passe est erroné";
            }
        }
    }

    public function adminAction()
    {
        // action body
        $title="Modification du mot de passe d'administration ";
        $this->view->title =$title;
        $this->view->headTitle()->prepend($this->view->title);
        $form=new Storm_Form_Default();
        $form->NewElement("password","old","Ancien mot de passe",array("attribs"=>array("style"=>"width:150px")));
        $form->NewElement("password","new","Votre nouveau mot de passe",array("attribs"=>array("style"=>"width:150px")));
        $form->NewElement("submit","submit","Valider",array("attribs"=>array("style"=>"clear:both")));

        $this->view->form=$form;
        $auth = Zend_Auth::getInstance();
        $identity=$auth->getIdentity();


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $file=file_get_contents( APPLICATION_PATH.'/configs/pwd.ini');
            $users=explode("\n",$file);
            $pwds=array();
            foreach($users as $u){
                $d=explode(":",$u);
                $pwds[$d[0]]=array("username"=>$d[0],"realm"=>$d[1],"md5"=>$d[2]);
            }
            if(md5($identity["username"].":".$identity["realm"].":".$formData["old"])==$pwds[$identity["username"]]["md5"]){
                $md5=md5($identity["username"].":".$identity["realm"].":".$formData["new"]);
                $pwds[$identity["username"]]["md5"]=$md5;
                $file_content="";
                foreach($pwds as $p){
                    $file_content.=$p["username"].":".$p["realm"].":".$p["md5"]."\n";
                }
                $f=fopen( APPLICATION_PATH.'/configs/pwd.ini',"w+");
                fwrite($f,$file_content);
                fclose($f);

                echo "Modification du mot de passe effectué.";
            }
            else{
                echo "l'ancien mot de passe est erroné";
            }
        }
    }


}

