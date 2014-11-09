<?php

class Caisse_CategorieController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                                          'basePath'  => '../application/modules/caisse',
                                          'namespace' => 'Caisse',
                                      ));
        $resourceLoader->addResourceType('model', 'models', 'Model')
                        ->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable');
    }

    public function indexAction()
    {
        // action body
    }

    public function listtailleAction()
    {
        $id_cat=$this->getRequest()->getParam("id_cat",1);
        $cat= new Caisse_Model_DbTable_Categorie();
        $c=$cat->getById($id_cat);

        $this->view->listoption=unserialize($c[0]->tab_taille);
    }


}



