<?php

class Admin_Form_Login extends Storm_Form_Default
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init("login");


        $this->NewElement('text','username','Login',array("required"=>true,"attribs"=>array("style"=>"clear:both"),"ErrorMessage"=>"Veuillez renseigner l'identifiant"));
        $this->NewElement('password','password','Mot de passe',array("required"=>true,"attribs"=>array("style"=>"clear:both"),"ErrorMessage"=>"Veuillez renseigner le mot de passe","Label"=>array("style"=>"clear:both")))
          ->getDecorator('label')->setOption('style','clear:both');
        $this->NewElement('submit','submit','Se Connecter',array("ignore"=>true,"attribs"=>array("style"=>"clear:both")));

        $this->NewElement('button','cancel','Sortir',array("attribs"=>array("onclick"=>"location.href='/'","style"=>"margin-left:100px;")));

    }


}

