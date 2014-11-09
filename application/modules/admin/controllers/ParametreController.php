<?php

class Admin_ParametreController extends Zend_Controller_Action
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
            if($i["username"]!="Storm")
                $this->_helper->redirector("index","Chiffre");
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
      $this->view->title ="Paramétrage";
      $this->view->headTitle()->prepend($this->view->title);
      $form= new Admin_Form_Parametre();

      $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/storm.ini',null,array('skipExtends'=> true,'allowModifications' => true));
      if ($this->getRequest()->isPost()) {
        $formData = $this->getRequest()->getPost();
        unset($formData["submit"]);
        foreach($formData as $key=>$val){
          if($key!="sauvegarde_xml" && $key!="maj_bdd")
            $config->{$key}=$val;
        }
        $w=new Zend_Config_Writer_Ini(array('config'   => $config,
                                            'filename' => APPLICATION_PATH.'/configs/storm.ini'));
        $w->write();

        if(isset($formData["sauvegarde_xml"]) && $formData["sauvegarde_xml"]==1){

          $base=new Admin_Model_DbTable_Base();

          $db=$base->getAdapter();
          $config_db=$db->getConfig();

          $select="SELECT t.table_name,t.table_type,t.engine,t.table_collation,
                      view_definition,
                      ccsa.character_set_name
                  FROM information_schema.tables AS t
                  LEFT JOIN information_schema.collation_character_set_applicability AS ccsa ON ccsa.collation_name=t.table_collation
                  LEFT JOIN information_schema.views AS v ON  v.table_schema=t.table_schema AND v.table_name=t.table_name
                  WHERE t.table_schema='".$config_db["dbname"]."';";
          $tables=$db->fetchAll($select);
          $xml=new SimpleXMLElement("<database></database>");
          $xml->addAttribute("name",$config_db["dbname"]);
          foreach($tables as $t){
            $table=$xml->addChild("table");
            if($t["table_type"]=="BASE TABLE"){
              $table->addAttribute("engine",$t["engine"]);
              $table->addAttribute("collation",$t["table_collation"]);
              $table->addAttribute("charset",$t["character_set_name"]);
            }
            //COLUMNS
            $table->addAttribute("name",$t["table_name"]);
            $select="SELECT column_name,column_default,is_nullable,column_type,column_key,extra,column_comment,character_set_name,collation_name
                  FROM information_schema.columns AS c
                  WHERE c.table_schema='".$config_db["dbname"]."'
                    AND c.table_name='".$t["table_name"]."'
                  ORDER BY ordinal_position;";

            $champs=$db->fetchAll($select);
            foreach($champs as $c){
               $champ=$table->addChild("champ");
               $champ->addAttribute("name",$c["column_name"]);
               $champ->addAttribute("default",$c["column_default"]);
               $champ->addAttribute("nullable",$c["is_nullable"]);
               $champ->addAttribute("type",$c["column_type"]);
               $champ->addAttribute("key",$c["column_key"]);
               $champ->addAttribute("extra",$c["extra"]);
               $champ->addAttribute("comment",$c["column_comment"]);
               $champ->addAttribute("character_set",$c["character_set_name"]);
               $champ->addAttribute("collation",$c["collation_name"]);

            }
            //INDEX
            $select="SHOW INDEX FROM ".$t["table_name"].";";

            $indexs=$db->fetchAll($select);
            $old_index="";
            foreach($indexs as $i){
              if($old_index!=$i["Key_name"]){
                $old_index=$i["Key_name"];
                $index=$table->addChild("index");
                $index->addAttribute("name",$i["Key_name"]);
                $index->addAttribute("nonunique",$i["Non_unique"]);
              }
              $index->addChild("champ",$i["Column_name"]);


            }
            // REFERENTIAL_CONSTRAINTS
            $select="SELECT rc.referenced_table_name, column_name,rc.constraint_name
                  FROM information_schema.referential_constraints AS rc
                  INNER JOIN information_schema.key_column_usage AS kcu ON rc.constraint_name = kcu.constraint_name
                  WHERE rc.constraint_schema='".$config_db["dbname"]."'
                    AND rc.table_name='".$t["table_name"]."'
                  ORDER BY ordinal_position;";
            $references=$db->fetchAll($select);

            foreach($references as $r){

              $ref=$table->addChild("constraint");
              $ref->addAttribute("name",$i["constraint_name"]);
              $ref->addAttribute("column_name",$i["column_name"]);
              $ref->addAttribute("referenced_table_name",$i["referenced_table_name"]);

            }

            if($t["table_type"]=="VIEW")
              $table->addChild("view_definition",$t["view_definition"]);
          }
          $xml->saveXml(APPLICATION_PATH."/configs/storm.xml");
          Zend_Debug::dump($xml);
        }

        if(isset($formData["maj_bdd"]) && $formData["maj_bdd"]==1){

          $base=new Admin_Model_DbTable_Base();

          $db=$base->getAdapter();
          $config_db=$db->getConfig();

          $xml=simplexml_load_file(APPLICATION_PATH."/configs/storm.xml");
          foreach($xml as $x){
            $attributes=$x->Attributes();
            $attributes->name;
            $select="SHOW COLUMNS FROM {$attributes->name}";
            try{
              $sql="ALTER TABLE {$attributes->name} ";
              $res=$db->query($select);
              $columns=$res->fetchAll();
              $oldcolumns=array();
              foreach($columns as $c){
                $oldcolumns[]=$c["Field"];
              }
              $newcolumns=array();
              $lastchamp="";
              foreach($x->champ as $champ){
                $attributes_columns=$champ->Attributes();
                $newcolumns[]=$attributes_columns->name;
                if($lastchamp!="") $sql.=", ";
                if(in_array($attributes_columns->name,$oldcolumns)){
                  $sql.=" CHANGE `{$attributes_columns->name}` `{$attributes_columns->name}` ";
                }
                $sql.=" {$attributes_columns->type} ";


                if(!empty($attributes_columns->character_set))
                  $sql.=" CHARACTER SET {$attributes_columns->character_set} ";
                if(!empty($attributes_columns->collation))
                  $sql.=" COLLATE {$attributes_columns->collation} ";

                 if(!empty($attributes_columns->nullable))
                  $sql.=" NOT ";
                $sql.=" NULL ";


                if(!empty($attributes_columns->default))
                  $sql.=" DEFAULT {$attributes_columns->default} ";

                $sql.=" {$attributes_columns->extra}";

                if(!empty($attributes_columns->comment))
                  $sql.=" COMMENT {$attributes_columns->comment} ";


                if($lastchamp!=""){
                  $sql.=" AFTER `$lastchamp`";
                }

                $lastchamp=$attributes_columns->name;
              }

              //INDEX
              $select="SHOW INDEX FROM ".$attributes->name.";";
              $indexs=$db->fetchAll($select);
              $oldindexs=array();
              foreach($indexs as $i){
                $oldindexs[]=$i["Key_name"];
              }
              foreach($x->index as $index){
                $attributes_index=$champ->Attributes();
                if(in_array($attributes_index->name,$oldindexs)){
                  if($attributes_index->name=="PRIMARY")
                    $sql.=" DROP PRIMARY KEY ,";
                  else
                    $sql.=" DROP KEY {$attributes_index->name},";
                }
                if($attributes_index->name=="PRIMARY")
                    $sql.=" ADD PRIMARY KEY ";
                elseif($attributes_index->nonunique==0)
                  $sql.=" ADD UNIQUE {$attributes_index->name} ";
                else
                  $sql.=" ADD KEY {$attributes_index->name} ";
                $tab_champ=array();
                foreach($index->champ as $champindex){
                  $tab_champ[]=$champindex;
                }
                $sql.="(`".implode("`,`",$tab_champ)."`)";

              }

              $sql.=" ENGINE={$attributes->engine} ";
              $sql.=" CHARSET={$attributes->charset} ";
              $sql.=" COLLATE={$attributes->collation} ;";

              //ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

              //Zend_Debug::dump($x);
              if(!empty($x->view_definition)){
                  $sql="DROP TABLE {$attributes->name}";
                  $sql2="CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `{$attributes->name}` AS {$x->view_definition}";

              }

             Zend_Debug::dump($sql);
            }
            catch(Exception $e){
              $sql="CREATE TABLE {$attributes->name} (";

               foreach($x->champ as $champ){
                $attributes_columns=$champ->Attributes();
                $newcolumns[]=$attributes_columns->name;
                if($lastchamp!="") $sql.=", ";
                if(in_array($attributes_columns->name,$oldcolumns)){
                  $sql.=" CHANGE `{$attributes_columns->name}` `{$attributes_columns->name}` ";
                }
                $sql.=" {$attributes_columns->type} ";


                if(!empty($attributes_columns->character_set))
                  $sql.=" CHARACTER SET {$attributes_columns->character_set} ";
                if(!empty($attributes_columns->collation))
                  $sql.=" COLLATE {$attributes_columns->collation} ";

                 if(!empty($attributes_columns->nullable))
                  $sql.=" NOT ";
                $sql.=" NULL ";


                if(!empty($attributes_columns->default))
                  $sql.=" DEFAULT {$attributes_columns->default} ";

                $sql.=" {$attributes_columns->extra}";

                if(!empty($attributes_columns->comment))
                  $sql.=" COMMENT {$attributes_columns->comment} ";


                if($lastchamp!=""){
                  $sql.=" AFTER `$lastchamp`";
                }

                $lastchamp=$attributes_columns->name;
              }

              $sql.=")";
              /* (
  `id_ville` int(11) NOT NULL AUTO_INCREMENT,
  `id_pays` int(11) NOT NULL,
  `id_region` int(11) NOT NULL,
  `id_dpt` int(11) NOT NULL,
  `code_postal` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `nom_ville` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_ville`),
  KEY `id_pays` (`id_pays`),
  KEY `id_region` (`id_region`),
  KEY `id_dpt` (`id_dpt`),
  KEY `code_postal` (`code_postal`)
)

              */

            }
          }
        }
      }

      $form->populate($config->toArray());

      $this->view->form=$form;
    }


}

