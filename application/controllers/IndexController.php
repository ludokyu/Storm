<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    
    }

    public function indexAction()
    {
    $this->view->headLink()->prependStylesheet($this->view->BaseUrl('/css/style.css'))
            ->headLink(array('rel'=>'favicon', 'href'=>$this->view->BaseUrl('/favicon.ico')), 'PREPEND');
    $bootstrap=$this->getInvokeArg('bootstrap');

    // Retrouve l'espace de nom de l'application.

    $ns=rtrim($bootstrap->getAppNamespace(), '_');

    // Récupère les paramètres sous la forme d'un tableau
    $config=$bootstrap->getOption($ns);

    $this->view->Storm_version=$config['version'];
    }

    public function encaisseLivAction()
    {
        // action body
    }


}



