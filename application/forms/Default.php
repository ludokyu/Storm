<?php

class Storm_Form_Default extends Zend_Form{

  protected $baseurl;

  public function init($name=""){
    /* Form Elements & Other Definitions Here ... */
    $this->baseurl=new Zend_View();
    $this->clearDecorators()
            ->setAttrib('enctype', 'multipart/form-data')
            ->addDecorator('FormElements')
            ->addDecorator('HtmlTag')
            ->addDecorator('Form')
            ->setName($name!="" ? $name : "default")
            ->setMethod("POST");
    $this->removeDecorator("DtDdWrapper");
  }

  public function NewElement($type, $name, $label, $param=array()){
    static $order=0;
    switch($type){

      case "captcha":
        $element=new Zend_Form_Element_Captcha($name, array("captcha"=>$param["captcha"]));
        break;
      case "float":
        $element=new Zend_Form_Element_Text($name);
          $element->addValidator("float");
      default:
        $default=array("text", "hidden", "textarea", "password", "select", "radio", "button", "reset", "file", "submit", "selectattrib", "DoubleMultiCheckbox", "multicheckbox", "html");

        if(in_array($type, $default))
          eval("\$element = new Zend_Form_Element_".ucfirst($type)."('$name');");
        else
          $element=new Zend_Form_Element($name);
    }
    //config par default
    $element->setName($name)
            ->removeDecorator("Label")
            ->removeDecorator("HtmlTag")
            ->removeDecorator("DtDdWrapper");
    if(!in_array($type, array("captcha", "file"))){
      $element->addDecorators(array(
          array('ViewHelper', array('tag'=>null)),
              )
      );
    }
    if(!in_array($type, array("submit", "button", "hidden"))&&$label!=""){
      $element->addDecorators(array(array("Label")));
      $element->setLabel($label." : ");
    }
    else
      $element->setLabel($label);

    //parametrage
    foreach($param as $key=> $p){
      switch($key){
        case "required":
          $element->setRequired($p);
          break;

        case "ignore":
          $element->setIgnore($p);
          break;

        case "attribs":
          $element->setAttribs($p);
          break;

        case "validators":
          $element->setValidators($p);
          break;

        case "ErrorMessage":
          $element->addErrorMessage($p);
          break;

        case "decorators":
          foreach($p as $decorator=> $value)
            $element->addDecorator($decorator, $value);
          break;

        case "options":
          $element->addMultiOptions($p);
          break;

        case "value":
          $element->setValue($p);
          break;

        case "separator":
          $element->setSeparator($p);
          break;

        case "destination":
          $element->setDestination($p);
          break;

        case "RegisterInArrayValidator":
          $element->setRegisterInArrayValidator($p);
          break;

        case "description":
          $element->setDescription($p);
          break;

        case "datalist":
          $element->setDatalist($p);
          break;
      }
    }
    if(array_key_exists('filters', $param))
      $element->setFilters($param['filters']);
    else
      $element->setFilters(array('StringTrim', 'StripTags'));

    if(array_key_exists('description', $param))
      $element->setDescription($param['description']);
    if(array_key_exists('order', $param))
      $element->setOrder($param['order']);
    else{
      $order++;
      $element->setOrder($order);
    }

    $this->addElement($element);
    return $element;
  }

}

