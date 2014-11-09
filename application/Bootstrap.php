<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

  protected function _initDoctype(){
    $this->bootstrap('view');
    $view=$this->getResource('view');
    $view->doctype('XHTML1_STRICT');
  }

  protected function _initSession(){
    // On initialise la session
    Zend_Session::start();
  
  
  }

  protected function _initAutoload(){
    Zend_Loader_Autoloader::getInstance()->registerNamespace('Storm');
  }

 /* protected function _initDb(){
    // Paramètres de base de données

    $database_config=$this->getOption('resources');
 //   Zend_Debug::dump($database_config["db"]);
    // Création de la connexion en base de donnée à partir du config
    $db=Zend_Db::factory($database_config["db"]['adapter'], $database_config["db"]['params']);
    //$this->_db=$db;

    $profiler=new Zend_Db_Profiler_Firebug('All DB Queries');
    $profiler->setEnabled(true);
    $db->setProfiler($profiler);
    return $db;
  }*/

  public function run(){
    $writer=new Zend_Log_Writer_Firebug();
    $logger=new Zend_Log($writer);
   //$logger->log('Ceci est un message de log !', Zend_Log::INFO);
    Zend_Registry::set('firephp', $logger);
    $front=$this->getResource('FrontController');
    //	$front->setParam('useDefaultControllerAlways', false);
    $front->addModuleDirectory('../application/modules', 'admin');
    $front->addModuleDirectory('../application/modules', 'caisse');
    parent::run();
  }

}

