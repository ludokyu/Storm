<?php

class Admin_PlatController extends Zend_Controller_Action{

  public function init(){
    /* Initialize action controller here */
    $this->view->headTitle("Administration");
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
            ->appendFile($this->view->BaseUrl("/js/admin.js"))
            ->appendFile($this->view->BaseUrl("/js/script.js"));
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

  public function indexAction(){
// action body
    $action=$this->getRequest()->getParam("a", "plat");
    $this->view->action=$action;
    switch($action){
      case "plat":
        $title="Liste des plats";
        $Table=new Admin_Model_DbTable_Plat();
        $plats=$Table->getListPlatOrderByCat();
        $this->view->list=$plats;

        break;
      case "cat":
        $title="Liste des catégories";
        $Table=new Admin_Model_DbTable_Categorie();
        $cat=$Table->listCategorie();
        $this->view->list=$cat;
        break;
      case "ingt":
        $title="Liste des ingrédients";
        $Table=new Admin_Model_DbTable_Ingt();
        $ingt=$Table->listAll();
        $this->view->list=$ingt;
        break;
      case "sauce":
        $title="Liste des sauces";
        $Table=new Admin_Model_DbTable_Base();
        $base=$Table->listbase();
        $this->view->list=$base;
        break;
    }
    $this->view->title=$title;
    $this->view->headTitle()->prepend($this->view->title);
  }

  public function platAction(){
// action body
    $this->_helper->layout->disableLayout();
    $form=new Admin_Form_Plat();
    $Table=new Admin_Model_DbTable_Plat();

    if($this->getRequest()->isPost()){
      $formData=$this->getRequest()->getPost();

      $data=$form->processAjax($_POST);

      if($data=="true"){
// succès!


        $data_insert=$formData;

        foreach($data_insert as $k=> $d){
// Zend_Debug::dump($d);
          if(!in_array($k, array("id_plat", "place", "go", "liv", "nom_plat", "list_ingt", "id_cat")))
            unset($data_insert[$k]);
          elseif(is_array($d))
            $data_insert[$k]=implode(",", $d);
        }


// Zend_Debug::dump($formData["id_plat"]);
        if(empty($formData["id_plat"])){
//insert
          $id=$Table->insert($data_insert);
        }
        else{
//update;;
          $id=$formData["id_plat"];
          $Table->update($data_insert, "id_plat=".$id);
        }


        if(!empty($formData["count_menu"])){
          $Menu=new Admin_Model_DbTable_Menu();
          $Menu->update(array("statut_menu"=>"X"), "id_plat=".$id);
          $menu_insert=array();
          for($i=1; $i<=$formData["count_menu"]; $i++){
            if(isset($formData["id_menu_$i"])){
              $menu=array();
              $menu["id_menu"]=$formData["id_menu_$i"];
              $menu["qte"]=$formData["qte_$i"];
              $menu["id_cat"]=$formData["id_cat_$i"];
              $menu["id_plat"]=$id;
              $menu["id_plat_default"]=$formData["id_plat_default_$i"];
              $menu["taille"]=(isset($formData["taille_$i"])) ? $formData["taille_$i"] : "NULL";
              $menu["list_plat"]=implode(",", $formData["list_plat_$i"]);
              $menu["statut_menu"]="O";
              if($formData["id_plat_default_$i"]=="NULL"&&(!isset($formData["list_plat_$i"])||count($formData["list_plat_$i"])>1))
                $menu["is_nec"]=1;
              else
                $menu["is_nec"]=0;
              $menu_insert[]=$menu;
            }
          }

          foreach($menu_insert as $k=> $m){
            if(empty($m["id_menu"])){
//insert
              $id=$Menu->insert($m);
            }
            else{
//update;;
              $id=$m["id_menu"];

              $Menu->update($m, "id_menu=".$id);
            }
          }
        }
        echo "admin=plat|";
        $this->_helper->json->sendJson($data);
      }
      else{
// echec!
        $this->_helper->json->sendJson($data);
      }
    }
    else{
      if($this->getRequest()->getParam("id_plat", 0)!=0){
        $form->submitBtn->setLabel("Modifier");
        $row=$Table->getPlat($this->getRequest()->getParam("id_plat"));
      }
      else{
        $Table=new Admin_Model_DbTable_Categorie();
        $r=$Table->getById($this->getRequest()->getParam("id_cat"));
        $row=$r[0];
      }
      if($row->nb_taille>1){
        $taille=unserialize($row->tab_taille);
        if($this->getRequest()->getParam("id_plat", 0)!=0){
          $prix_go=explode(",", $row->go);
          $prix_place=explode(",", $row->place);
          $prix_liv=explode(",", $row->liv);
        }
        else{
          $prix_go=array();
          $prix_place=array();
          $prix_liv=array();
          for($i=0; $i<$row->nb_taille; $i++){
            $prix_go[]="";
            $prix_place[]="";
            $prix_liv[]="";
          }
        }

        $form_prix_go=array();
        $form_prix_place=array();
        $form_prix_liv=array();
        foreach($taille as $k=> $t){
          $form_prix_go[$t]=$prix_go[$k-1];
          $form_prix_place[$t]=$prix_place[$k-1];
          $form_prix_liv[$t]=$prix_liv[$k-1];
        }

        $attribs=array("label_placement"=>"prepend", "style"=>"float:none;width:40px;margin-left:5px;clear:both;", "label_style"=>"clear:none;margin-top:0px;margin-left:5px;");
        $form->NewElement("multitext", "go", "Emporter", array("options"=>$form_prix_go,
            "attribs"=>$attribs,
            "separator"=>" ", "decorators"=>array("label"=>array("style"=>"width:100px;clear:both;"))));
        $form->NewElement("multitext", "place", "Sur place", array("options"=>$form_prix_place,
            "attribs"=>$attribs,
            "separator"=>" ", "decorators"=>array("label"=>array("style"=>"width:100px;clear:both;"))));
        $form->NewElement("multitext", "liv", "Livraison", array("options"=>$form_prix_liv,
            "attribs"=>$attribs,
            "separator"=>" ", "decorators"=>array("label"=>array("style"=>"width:100px;clear:both;"))));
        $form->global_prix->addElements(array($form->getElement("place"), $form->getElement("go"), $form->getElement("liv")));
        $form->removeElement("go");
        $form->removeElement("liv");
        $form->removeElement("place");
      }

      if($row->is_base!=1)
        $form->removeElement("base_pizza");
      if($row->is_compo!=1)
        $form->removeElement("list_ingt");
      elseif(isset($row->list_ingt))
        $row->list_ingt=explode(",", $row->list_ingt);

      if($row->is_menu!=1){
        $form->removeElement("add_menu");
      }
      if($row->is_menu==1){

        if(isset($row->id_plat)){
          $Menu=new Admin_Model_DbTable_Menu();
          $menu=$Menu->getMenu($row->id_plat);

          $k=1;
          foreach($menu as $m){
            $no=$form->getMenu($k);
            $form->{"id_menu_".$k}->setValue($m->id_menu);
            $form->{"qte_".$k}->setValue($m->qte);
            $form->{"id_cat_".$k}->setValue($m->id_cat);

            if(!unserialize($m->tab_taille))
              $form->removeElement("taille_".$k);
            else{
              $form->{"taille_".$k}->setMultiOptions(unserialize($m->tab_taille))
                      ->setValue($m->taille);
            }

            $plat=$Table->listplat($m->id_cat);
            $option=array();
            foreach($plat as $p){
              $option[$p->id_plat]=$p->nom_plat;
            }
            $form->{"list_plat_".$k}->setMultiOptions($option)
                    ->setValue(explode(",", $m->list_plat));

            $options=array();
            foreach($option as $j=> $o){
              if(in_array($j, explode(",", $m->list_plat))){
                $options[$j]=$o;
                $last=$j;
              }
            }
            $default=(count($options)==1) ? $last : $m->id_plat_default;

            $options["NULL"]="";
            asort($options);

            $form->{"id_plat_default_".$k}->setMultiOptions($options)
                    ->setValue($default);
            $k++;
          }
          $form->cancel->setOrder($no++);
          $form->submitBtn->setOrder($no++);
        }
        else{
          $form->getMenu(1);
        }
      }
    }

    $form->populate($row->toArray());
    $this->view->form=$form;
  }

  public function ingtAction(){
// action body
    $this->_helper->layout->disableLayout();
    $form=new Storm_Form_Default("form_ingt");
    $form->setName("form_ingt");
    $form->setAction("/admin/plat/ingt");
    $form->setAttrib("onsubmit", "submit_form('#form_ingt'); return false");
    $form->NewElement("hidden", "id_ingt", "");
    $form->NewElement("text", "nom_ingt", "Libellé");
    $form->NewElement("button", "cancel", "Annuler", array("attribs"=>array("style"=>"clear:both;", "onclick"=>"$('#form').css('display','none')")));
    $form->NewElement("submit", "submit", "Ajouter", array("attribs"=>array("onclick"=>"")));
    if($this->getRequest()->isPost()){
      $formData=$this->getRequest()->getPost();
      $data=$form->processAjax($_POST);
      if($data=="true"){
// succès!
        unset($formData["submit"]);
        $data_insert=$formData;
        echo "admin=ingt|";
        $ingt=new Admin_Model_DbTable_Ingt();
        if($formData["id_ingt"]==""){
//insert
          $ingt->insert($data_insert);
        }
        else{
//update;;
          $id=$formData["id_ingt"];
          $ingt->update($data_insert, "id_ingt=".$id);
        }
        $this->_helper->json->sendJson($data);
      }
      else{
// echec!
        $this->_helper->json->sendJson($data);
      }
    }
    else{
      if($this->getRequest()->getParam("id_ingt", 0)!=0){
        $form->submit->setLabel("Modifier");
        $Table=new Admin_Model_DbTable_Ingt();
        $row=$Table->get($this->getRequest()->getParam("id_ingt"));
        $form->populate($row->toArray());
      }
      $this->view->form=$form;
    }
  }

  public function baseAction(){
// action body
    $this->_helper->layout->disableLayout();
    $form=new Storm_Form_Default("form_base");
    $form->setName("form_base");
    $form->setAction("/admin/plat/base");
    $form->setAttrib("onsubmit", "submit_form('#form_base'); return false");
    $form->NewElement("hidden", "id_base", "");
    $form->NewElement("text", "nom_base", "Libellé");
    $form->NewElement("button", "cancel", "Annuler", array("attribs"=>array("style"=>"clear:both;", "onclick"=>"$('#form').css('display','none')")));
    $form->NewElement("submit", "submit", "Ajouter", array("attribs"=>array("onclick"=>"")));

    if($this->getRequest()->isPost()){
      $formData=$this->getRequest()->getPost();
      $data=$form->processAjax($_POST);
      if($data=="true"){
// succès!
        unset($formData["submit"]);
        $data_insert=$formData;
        echo "admin=sauce|";
        $base=new Admin_Model_DbTable_Base();
        if($formData["id_base"]==""){
//insert
          $base->insert($data_insert);
        }
        else{
//update;;
          $id=$formData["id_base"];
          $base->update($data_insert, "id_base=".$id);
        }
        $this->_helper->json->sendJson($data);
      }
      else{
// echec!
        $this->_helper->json->sendJson($data);
      }
    }
    else{
      if($this->getRequest()->getParam("id_base", 0)!=0){
        $form->submit->setLabel("Modifier");
        $Table=new Admin_Model_DbTable_Base();
        $row=$Table->get($this->getRequest()->getParam("id_base"));
        $form->populate($row->toArray());
      }
      $this->view->form=$form;
    }
  }

  public function deletebaseAction(){
// action body
    $Table=new Admin_Model_DbTable_Base();
    $Table->delete("id_base=".$this->getRequest()->getParam("id_base", 0));
    $this->_helper->redirector("index", "plat", "admin", array("a"=>"sauce"));
  }

  public function catAction(){
// action body
    $this->_helper->layout->disableLayout();
    $form=new Admin_Form_Categorie();
    if($this->getRequest()->isPost()){
      $formData=$this->getRequest()->getPost();
      $data=$form->processAjax($_POST);
      if($data=="true"){
// succès!
        if(isset($formData["tab_taille"])){
          for($i=count($formData["tab_taille"]); $i>0; $i--)
            $formData["tab_taille"][$i]=$formData["tab_taille"][$i-1];
          unset($formData["tab_taille"][0]);
          $formData["tab_taille"]=serialize($formData["tab_taille"]);
        }
        unset($formData["submit"]);
        $data_insert=$formData;

        echo "admin=cat|";
        $cat=new Admin_Model_DbTable_Categorie();
        if($formData["id_cat"]==""){
//insert
          $cat->insert($data_insert);
        }
        else{
//update;;
          $id=$formData["id_cat"];
          $cat->update($data_insert, "id_cat=".$id);
        }
        $this->_helper->json->sendJson($data);
      }
      else{
// echec!
        $this->_helper->json->sendJson($data);
      }
    }
    else{

      if($this->getRequest()->getParam("id_cat", 0)!=0){
        $Table=new Admin_Model_DbTable_Categorie();
        $form->submit->setLabel("Modifier");
        $row=$Table->get($this->getRequest()->getParam("id_cat", 0));
        if($row->is_taille==1){
          $form->nb_taille->setAttrib("disabled", null);
          $order=$form->nb_taille->getOrder();

          $form->NewElement("multitext", "tab_taille", "Tailles", array("options"=>unserialize($row->tab_taille),
              "order"=>$order+1,
              "attribs"=>array("label_placement"=>"prepend", "style"=>"float:none;margin-left:5px;")
                  )
          );
        }

        $form->populate($row->toArray());
      }
      $this->view->form=$form;
    }
  }

  public function catdefaultAction(){
// action body
    $Table=new Admin_Model_DbTable_Categorie();
    $Table->update(array("is_default"=>0), "is_default=1");
    $Table->update(array("is_default"=>1), "id_cat=".$this->getRequest()->getParam("id_cat", 0));
  }

  public function tailleAction(){
// action body
    $this->_helper->layout->disableLayout();
    $Table=new Admin_Model_DbTable_Categorie();
    $cat=$Table->get($this->getRequest()->getParam("id", 0));
    $taille=unserialize($cat->tab_taille);

    $select=new Zend_Form_Element_Select("taille_".$this->getRequest()->getParam("i", 0));
    $select->setMultiOptions($taille)
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->select=$select;
  }

  public function divplatAction(){
// action body
    $this->_helper->layout->disableLayout();
    $Table=new Admin_Model_DbTable_Plat();
    $plat=$Table->listplat($this->getRequest()->getParam("id", 0));
    $option=array();
    foreach($plat as $p){
      $option[$p->id_plat]=$p->nom_plat;
    }
    $i=$this->getRequest()->getParam("i", 0);
    $element=new Zend_Form_Element_MultiCheckbox("list_plat_$i");
    $element->setMultiOptions($option)
            ->setValue(array_keys($option))
            ->setAttribs(array("label_style"=>"clear:both", "onclick"=>"updateListDefault($i)"))
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->element=$element;
  }

  public function addmenuAction(){
// action body
    $this->_helper->layout->disableLayout();
    $i=$this->getRequest()->getParam("i", 0);
    $id_menu=new Zend_Form_Element_Hidden("id_menu_$i");
    $id_menu->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->id_menu=$id_menu;

    $qte=new Zend_Form_Element_Text("qte_$i");
    $qte->setAttribs(array("style"=>"width:30px;clear:both"))
            ->setValue(1)
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->qte=$qte;

    $cat=new Admin_Model_DbTable_Categorie();
    $c=$cat->listCategorieNotMenu();
    $options=array(array("value"=>0, "label"=>" ", "attribs"=>array("is_taille"=>0)));
    foreach($c as $l){
      $array=array("is_taille"=>$l->is_taille);
      $options[]=array("value"=>$l["id_cat"], "label"=>$l["nom_cat"], "attribs"=>$array);
    }


    $categorie=new Zend_Form_Element_Selectattrib("id_cat_$i");
    $categorie->setAttribs(array("onchange"=>"listPlat(this,$i)"))
            ->setMultiOptions($options)
            ->setRequired(true)
            ->setValue(0)
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->categorie=$categorie;

    $affich=new Zend_Form_Element_Button("affich_list_plat_$i");
    $affich->setLabel("Modifier la liste des plat")
            ->setAttribs(array("style"=>"margin:5px", "onclick"=>"$('#fieldset-div_list_plat_$i').css('display','block')"))
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->affich=$affich;

    $defaut=new Zend_Form_Element_Select("id_plat_default_$i");
    $defaut->setAttribs(array("style"=>"max-width:180px;"))
            ->addMultiOption("NULL", "")
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->defaut=$defaut;

    $close=new Zend_Form_Element_Button("close_list_plat_$i");
    $close->setLabel("Fermer")
            ->setAttribs(array("style"=>"margin:5px", "onclick"=>"$('#fieldset-div_list_plat_$i').css('display','none')"))
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->close=$close;

    $list=new Zend_Form_Element_MultiCheckbox("list_plat_$i");
    $list->setAttribs(array("label_style"=>"clear:both", "onclick"=>"updateListDefault($i)"))
            ->addMultiOption(0, "")
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper")
            ->addDecorator("HtmlTag", array("tag"=>"div", "id"=>"list_plat_$i"));

    $this->view->list=$list;

    $del=new Zend_Form_Element_Button("del_plat_$i");
    $del->setLabel("Supprimer")
            ->setAttribs(array("style"=>"float:right", "onclick"=>"if(confirm('Confirmez vous la suppression de cet élément ?')){delMenu($i)}"))
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    $this->view->del=$del;

    $this->view->i=$i;
  }

  public function deleteplatAction(){
// action body
    $p=new Admin_Model_DbTable_Plat();
    $p->deletePlat($this->getRequest()->getParam("id", 0));
    $this->_redirect("/admin/plat/index/a/plat");
  }

  public function deletecatAction(){
// action body
    $p=new Admin_Model_DbTable_Plat();
    $p->deletePlat($this->getRequest()->getParam("id", 0));
    $this->_redirect("/admin/plat/index/a/cat");
  }

  public function statAction(){
// action body

    $form=new Storm_Form_Default("search");
    $form->NewElement("date", "startDate", "Date de début", array("value"=>$this->getRequest()->getParam("startDate", date("01-m-Y")), 'jQueryParams'=>array('dateFormat'=>'dd-mm-yy',
            'changeMonth'=>'true',
            'changeYear'=>'true'), "attribs"=>array("style"=>"width:80px")));
    $form->NewElement("date", "endDate", "Date de fin", array("value"=>$this->getRequest()->getParam("endDate", date("t-m-Y")), 'jQueryParams'=>array('dateFormat'=>'dd-mm-yy',
            'changeMonth'=>'true',
            'changeYear'=>'true'), "attribs"=>array("style"=>"width:80px"), "decorators"=>array("Label"=>array("style"=>"clear:none;margin-left:20px"))));

    $form->NewElement("submit", "BtnSubmit", "Valider", array("attribs"=>array("style"=>"margin-left:20px;width:80px;")));
    //Zend_Debug::dump($form->startDate);

    $this->view->form=$form;
    $data=$form->getValues();
    $Plat=new Admin_Model_DbTable_Plat();
    $date_deb=explode("-", $data["startDate"]);
    rsort($date_deb);
    $date_deb=implode("-", $date_deb);
    $date_fin=explode("-", $data["endDate"]);
    rsort($date_fin);
    $date_fin=implode("-", $date_fin);
    $this->view->datedeb=$date_deb;
    $this->view->datefin=$date_fin;
    $this->view->listCat=$Plat->consommationCat($date_deb, $date_fin);
  }

  public function statplatAction(){
    // action body
     $this->_helper->layout->disableLayout();
     $Plat=new Admin_Model_DbTable_Plat();
     $this->view->id_cat=$id_cat=$this->getRequest()->getParam("id_cat", 0);
     $date_deb=$this->getRequest()->getParam("datedeb");
     $date_fin=$this->getRequest()->getParam("datefin");
     $plat=$Plat->consommationPlat($id_cat,$date_deb, $date_fin);
    
     $this->view->listPlat=$plat;
             
  }

}

