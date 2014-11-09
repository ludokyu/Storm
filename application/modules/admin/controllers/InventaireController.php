<?php

class Admin_InventaireController extends Zend_Controller_Action
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
            ->appendFile($this->view->BaseUrl("/js/admin.js"));
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

        $this->view->title = "Inventaire";
        $this->view->headTitle()->prepend($this->view->title);
        $Ingt= new Admin_Model_DbTable_Ingt();
        $list=$Ingt->listAll();

        $form=new Storm_Form_Default("Inventaire");
        $form->setName("Inventaire");
        $form->setAction($this->view->url(array("action"=>"resultInventaire","controller"=>"inventaire")));
        $form->setAttrib("onsubmit","submit_inventaire(this); return false;");
        $i=1;
        $global_div=array();
        foreach($list as $ingt){
            $form->NewElement("hidden","type_$i","",array("value"=>"ingt"));
            $form->NewElement("hidden","id_$i","",array("value"=>$ingt->id_ingt));
            $form->NewElement("hidden","name_$i","",array("value"=>$ingt->nom_ingt));
            $form->NewElement("text","qte_$i",$ingt->nom_ingt,array("value"=>0,

                                                                "attribs"=>array("style"=>"width:50px"),
                                                                "decorators"=>array("Label"=>array("style"=>"width:225px"))));
            $form->NewElement("text","prix_$i","",array("value"=>number_format($ingt->prix_inv,2),
                "description"=>"€","attribs"=>array("style"=>"width:50px;margin-left:30px")));
            $global_div[]="type_$i";
            $global_div[]="id_$i";
            $global_div[]="name_$i";
            $global_div[]="prix_$i";
            $global_div[]="qte_$i";

            $i++;
        }

        $form->addDisplayGroup($global_div,"ingredient",array("legend"=>"Ingrédient"));

        $Cat= new Admin_Model_DbTable_Categorie();
        $listcat=$Cat->listCatInventaire();
        $cat_div=array();
        $Plat= new Admin_Model_DbTable_Plat();
        foreach($listcat as $cat){
            $list=$Plat->listplat($cat->id_cat);
            $cat_div[$cat->nom_cat]=array();
            foreach($list as $plat){
                $form->NewElement("hidden","type_$i","",array("value"=>"plat"));
                $form->NewElement("hidden","id_$i","",array("value"=>$plat->id_plat));
                $form->NewElement("hidden","name_$i","",array("value"=>$plat->nom_plat));
                $form->NewElement("text","qte_$i",$plat->nom_plat,array("value"=>0,
                                                                "attribs"=>array("style"=>"width:50px"),
                                                                    "decorators"=>array("Label"=>array("style"=>"width:225px"))));
                $form->NewElement("text","prix_$i","",array("value"=>number_format($plat->prix_inv,2),
                                                                    "description"=>"€",
                                                                    "attribs"=>array("style"=>"width:50px;margin-left:30px")));

                $cat_div[$cat->nom_cat][]="type_$i";
                $cat_div[$cat->nom_cat][]="id_$i";
                $cat_div[$cat->nom_cat][]="name_$i";
                $cat_div[$cat->nom_cat][]="prix_$i";
                $cat_div[$cat->nom_cat][]="qte_$i";

                $i++;
            }
        }

        $form->addDisplayGroup($global_div,"list_cat_admin",array("class"=>"list_plat_admin_inventaire"));
        $display=$form->list_cat_admin;
        $form->ingredient->removeDecorator("DtDdWrapper");
        foreach($global_div as $val)
            $display->removeElement($val);
        $display->addDisplayGroup($form->ingredient);

        $form->removeDisplayGroup("ingredient");
        foreach($cat_div as $k=>$v){
            $nomgrop="div_cat_".str_replace(" ","",$k);
            $form->addDisplayGroup($v,$nomgrop,array("legend"=>$k));
            $form->{$nomgrop}->removeDecorator("DtDdWrapper");
            $display->addDisplayGroup($form->$nomgrop);
            $form->removeDisplayGroup($nomgrop);
            foreach($v as $p)
                $form->removeElement($p);
        }
        foreach($global_div as $val)
             $form->removeElement($val);

         //$display->addElement(new Zend_Form_Element_Text());
        $display->removeDecorator("DtDdWrapper");
        $form->NewElement("hidden","count","",array("value"=>$i-1));

        $form->NewElement("submit","submit","Obtenir le résultat");
        $form->NewElement("button","print_empty","Imprimer un inventaire vide",array("attribs"=>array("style"=>"clear:both","onclick"=>"window.open('".$this->view->url(array("action"=>"printEmpty"))."')")));
        $form->addDisplayGroup(array("submit","print_empty"),"field_submit",array("attribs"=>array("style"=>"clear:none;float:left;width:auto;border:none;padding:0px")));
        $form->field_submit->removeDecorator("DtDdWrapper");
        $this->view->form=$form;

        $script="$(document).ready(function(){
            $('legend').each(function(){
                $(this).click(function(){
                    if($('dl',$(this).parent()).css('display')=='none'){
                        $('legend').each(function(){
                             $('dl',$(this).parent()).css('display','none');
                             $(this).removeClass('hightlight');
                        });
                        $('dl',$(this).parent()).css('display','block');
                        $(this).addClass('hightlight')
                    }
                    else{
                        $('dl',$(this).parent()).css('display','none');
                        $(this).removeClass('hightlight');
                    }
                });
            });
        });";
        $this->view->headScript()->appendScript($script);
    }

    public function resultinventaireAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $req="";
        $nb=$this->getRequest()->getParam("count");
        $total=0;

        $Ingt=new Admin_Model_DbTable_Ingt();
        $Plat=new Admin_Model_DbTable_Plat();
        $list=array();
        for($i=1;$i<=$nb;$i++){

            if($this->getRequest()->getParam("type_$i")=="ingt"){
                $Ingt->update(array("prix_inv"=>$this->getRequest()->getParam("prix_$i")),"id_ingt=".$this->getRequest()->getParam("id_$i"));
            }
            elseif($this->getRequest()->getParam("type_$i")=="plat"){
                $Plat->update(array("prix_inv"=>$this->getRequest()->getParam("prix_$i")),"id_plat=".$this->getRequest()->getParam("id_$i"));
            }
            $prix_p=number_format($this->getRequest()->getParam("qte_$i")*$this->getRequest()->getParam("prix_$i"),2);
            $total+=$prix_p;
            $data=array();
            $data["name"]=$this->getRequest()->getParam("name_$i");
            $data["qte"]=$this->getRequest()->getParam("qte_$i");
            $data["prix"]=$this->getRequest()->getParam("prix_$i");
            $data["inv"]=$prix_p;
            $list[]=$data;

        }
        $this->view->list=$list;
        $this->view->total=$total;

        $pdf = new Zend_Pdf();
        $page=$this->newPage($pdf,"A4");
        $y=660;
        foreach($list as $l){
            if((int)$l['inv']>0){
             $this->cell($page,$l['name'],30,$y,280);
             $this->cell($page,$l['prix']." €",310,$y,100);
             $this->cell($page,$l['qte'],410,$y,70);
             $this->cell($page,$l['inv']." €",480,$y,60);
             $y-=20;
             if($y<50){
                 $page=$this->newPage($pdf,"A4");
                 $y=660;
             }
            }
        }
        $this->cell($page,"TOTAL",410,$y,70);
        $this->cell($page,$total." €",480,$y,60);
        $last=max(array_keys($pdf->pages));
        $this->footer($page,$last+1);
        $pdf->properties['Title']="Inventaire du ".date("d/m/Y");
        $pdf->properties['Author']="STORM";
        $pdf->properties['CreationDate']="D:".date("YmdHisO");
        $pdf->save("inventaire.pdf");
    }

    public function header(&$page)
    {
        // action body

        $stampImage = Zend_Pdf_Image::imageWithPath(realpath(APPLICATION_PATH."/../www/images/Storm128.png"));
        $page->drawImage($stampImage, 0, 842-128, 128 ,842);
        $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/storm.ini');
        $page->drawText('STORM -- '.$config->PIZZA, 150, 790);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 20);
        $page->drawText('Inventaire au '.date("d/m/Y"), 150, 760);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 16);
        $this->cell($page,'Dénomination',30,680,280);
        $this->cell($page,'Prix Unitaire',310,680,100);
        $this->cell($page,'Quantité',410,680,70);
        $this->cell($page,'Total',480,680,60);

    }

    public function footer(&$page, $no)
    {

            $page->drawText('Page '.$no, 280, 20);
    }

    public function newPage(&$pdf, $size)
    {
        if(count($pdf->pages)>0){
            $last=max(array_keys($pdf->pages));
            $this->footer($pdf->pages[$last],$last+1);
        }
        $pdf->pages[]=$page=$pdf->newPage($size);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 32);
        $this->header($page);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_ROMAN), 16);
        return $page;
    }

    public function cell(&$page, $text, $x1, $y1, $length)
    {
        $y2=$y1+20;
        $x2=$x1+$length;
        $page->drawLine($x1,$y1,$x1,$y2);
        $page->drawLine($x1,$y2,$x2,$y2);
        $page->drawLine($x2,$y2,$x2,$y1);
        $page->drawLine($x2,$y1,$x1,$y1);
        $page->drawRectangle($x1,$y1,$x1,$y2);

        while($this->getStringWidth($page,$text)>$length){
            //print_r($page->getFont());
            $page->setFont($page->getFont(),$page->getFontSize()-1);
        }
        $page->drawText($text, $x1+2, $y1+5,'UTF-8');
        $page->setFont($page->getFont(),16);
    }

    public function getStringWidth($page, $text)
    {
        $originalFont = $page->getFont();
        $taillePolice=$page->getFontSize();
        $xPosition=0;
        for ($charIndex = 0; $charIndex < strlen($text); $charIndex++) {
            // Use original font for text width calculation
            $width = $originalFont->widthForGlyph(
                        $originalFont->glyphNumberForCharacter($text[$charIndex])
                     );
            $xPosition += $width / $originalFont->getUnitsPerEm() * $taillePolice;
        }
        return $xPosition;
    }

    public function printemptyAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $Ingt=new Admin_Model_DbTable_Ingt();
        $Plat=new Admin_Model_DbTable_Plat();
        $list=array();

        $listingt=$Ingt->listAll();
        //Zend_Debug::dump($list);
        foreach($listingt as $ingt){
            $data=array();
            $data["name"]=$ingt->nom_ingt;
            $data["qte"]="";
            $data["prix"]=!empty($ingt->prix_inv)?number_format($ingt->prix_inv,2):"";
            $data["inv"]="";
            $list[]=$data;
        }

        $Cat= new Admin_Model_DbTable_Categorie();
        $listcat=$Cat->listCatInventaire();

        foreach($listcat as $cat){
          $listplat=$Plat->listplat($cat->id_cat);
          foreach($listplat as $plat){
            $data=array();
            $data["name"]=$plat->nom_plat;
            $data["qte"]="";
            $data["prix"]=!empty($plat->prix_inv)?number_format($plat->prix_inv,2):"";
            $data["inv"]="";
            $list[]=$data;
          }
        }

        $pdf = new Zend_Pdf();
        $page=$this->newPage($pdf,"A4");
        $y=660;
        foreach($list as $l){
          $this->cell($page,$l['name'],30,$y,280);
          $this->cell($page,"",310,$y,100);
          $this->cell($page,(!empty($l['inv'])?$l["inv"]." €":""),410,$y,70);
          $this->cell($page,"",480,$y,60);
          $y-=20;
          if($y<50){
             $page=$this->newPage($pdf,"A4");
             $y=660;
          }

        }
        $this->cell($page,"TOTAL",410,$y,70);
        $this->cell($page,"",480,$y,60);
        $last=max(array_keys($pdf->pages));
        $this->footer($page,$last+1);
        $pdf->properties['Title']="Inventaire du ".date("d/m/Y");
        $pdf->properties['Author']="STORM";
        $pdf->properties['CreationDate']="D:".date("YmdHisO");
        $pdf->save("inventaire.pdf");

        $this->_redirect($this->view->BaseUrl("/inventaire.pdf"));
    }


}







