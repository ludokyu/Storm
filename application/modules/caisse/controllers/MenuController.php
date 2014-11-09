<?php

class Caisse_MenuController extends Zend_Controller_Action
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
       							->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable')
       							->addResourceType('form', 'forms', 'form')
       							->addResourceType('helper','views/helpers','Helper');
      
            
    }

    public function indexAction()
    {
        // action body
        $id_plat=$this->getRequest()->getParam('plat',1);
       $form=$this->getForm($id_plat);
              if ($this->getRequest()->isPost()) {
                        $formData = $this->getRequest()->getPost();
                      $data = $form->processAjax($_POST);
                        if ($data=="true") {
              			  // succès!
              			  
              			  $data_insert=$formData;
              			  	
              			  	$panier=new Caisse_Model_DbTable_PanierMenu();
		              			
		              			 
		              			  		$panier->updatepanier($data_insert);
		              		
             			   $this->_helper->json->sendJson($data);
            				} 
            				else {
            			    // echec!
            			    
             		   $this->_helper->json->sendJson($data);
                        }
            			
	       }
	       else{
	       	$id_panier=$this->getRequest()->getParam("id_panier",0);
	       	$p=new Caisse_Model_DbTable_PanierMenu();
	       	$subpanier=$p->findsubpanier($id_panier);
	       	if(isset($subpanier[0])){
	       		$data["id_panier"]=$subpanier[0]->id_panier;
	       		foreach($subpanier as $key=>$val){
	       			$data["id_plat_$key"]=$val->id_plat;
	       			$data["id_menu_$key"]=$val->id_menu;
	       			$data["id_plat_2_$key"]=$val->id_plat2;
	       			$c=0;
	       			if($val->id_plat2!=0){
	       				$plat2=new Zend_Form_Element_Select("id_plat_2_$key");
	       					
	       				
	       				$plat2->setOrder($form->{"id_plat_$key"}->getOrder()+1)->setLabel(" / ");
	       				
	       				 $menu=new Caisse_Model_DbTable_Menu();
        				$m=$menu->find($val->id_menu);
        				       				$plat=new Caisse_Model_DbTable_Plat();
	        		 	$p=$plat->listplat($m[0]->id_cat,$m[0]->list_plat);
	        		 	
	       				foreach ( $p as  $value ) {
       						$plat2->addMultiOption($value->id_plat,$value->nom_plat);
						}
	       				$form->addElement($plat2);
	       				$c=1;
	       			}
	       			
	       			$data["base_$key"]=$val->id_base;
	       			if($val->id_base!=0){
	       				
	       				$base=new Zend_Form_Element_Select("base_$key");
	       				$base->setOrder($form->{"id_plat_$key"}->getOrder()+2)->setLabel(" Base : ");
	       				$b=new Caisse_Model_DbTable_Base();
	       				$l=$b->listbase();
	       					foreach ( $l as  $value ) {
       						$base->addMultiOption($value->id_base,$value->nom_base);
						}
	       				$form->addElement($base);
	       			}
	       			$data["ingt_plus_$key"]=$val->ingt_plus;
	       			$data["ingt_moins_$key"]=$val->ingt_moins;
	       			if($form->getElement("ingt_plus_$key")){
	       			
	       				$i=$form->getElement("ingt_plus_$key");
	       				//$i->setLabel("+champignons");
	       				       			
	       			}
	       			if($form->getElement("ingt_moins_$key")){
	       				
	       				$i=$form->getElement("ingt_moins_$key");
	       				//$i->setLabel("+champignons");
	       				       			
	       			}
	       		}
	       		$form->populate($data);
	       		
	       
	       	}
        $this->view->form=$form;
	       }
    }

    public function necessaryAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$id_plat=$this->getRequest()->getParam('plat',1);
    	$menu=new Caisse_Model_DbTable_Menu();
       	$count=$menu->verif_nec($id_plat);
		echo $count;
    }
	public function getForm($id_plat){
		
		 $form=new Caisse_Form_Menu();
		 $form->getForm($id_plat);
		 return $form;
	} 


}



