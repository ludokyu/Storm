<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

  protected function _initDoctype(){
    $this->bootstrap('view');
    $view=$this->getResource('view');
    $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');

    $view->doctype('XHTML1_STRICT');
  }

  protected function _initSession(){
    // On initialise la session
    Zend_Session::start();
  }

  
  protected function _initLogging(){

$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH."/../data/logs/app.log","a+");
$logger = new Zend_Log($writer);

}
 
  protected function _initView(){
    $view=new Zend_View();
    //... code de paramétrage de votre vue : titre, doctype ...
    $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
    //... paramètres optionnels pour les helpeurs jQuery ....
    ZendX_JQuery::enableView($view);
    $view->jQuery()
            ->addStylesheet($view->BaseUrl('/js/css/smoothness/jquery-ui-1.8.17.custom.css'))
            ->setLocalPath($view->BaseUrl('/js/jquery/js/jquery-1.7.1.min.js'))
            ->setUiLocalPath($view->BaseUrl('/js/jquery/js/jquery-ui-1.8.17.custom.min.js'))
            ->addJavascriptFile($view->BaseUrl('/js/jquery/development-bundle/ui/i18n/jquery.ui.datepicker-fr.js'))
            ->enable()
            ->uiEnable();
    $viewRenderer=Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setView($view);

    return $view;
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
    } */

  public function run(){
    $logger=new Zend_Log();
    $writer=new Zend_Log_Writer_Firebug();
    $logger->addWriter($writer);
    $front=$this->getResource('FrontController');
    //	$front->setParam('useDefaultControllerAlways', false);
    $front->addModuleDirectory('../application/modules', 'admin');
    $front->addModuleDirectory('../application/modules', 'caisse');
    parent::run();
  }

}

