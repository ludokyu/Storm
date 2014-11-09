<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Multi */
require_once 'Zend/Form/Element/Multi.php';

/**
 * Select.php form element
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Select.php 20096 2010-01-06 02:05:09Z bkarwin $
 */
class Zend_Form_Element_DoubleMultiCheckbox extends Zend_Form_Element_MultiCheckbox
{
    
	public $helper = 'formDoubleMultiCheckbox';
    /**
     * Flag: autoregister inArray validator?
     * @var bool
     */
   
    /**
     * Retrieve separator
     *
     * @return mixed
     */
  
   	public function setValue2($value)
    {
       
        $this->_value["value2"]=$value;
        return $this;
    }
   	 public function setValue($value)
    {
        $this->_value["value"]=$value;
        return $this;
    }
    public function getValue()
    {
        
        $valueFiltered = $this->_value;

        if ($this->isArray() && is_array($valueFiltered)) {
            array_walk_recursive($valueFiltered, array($this, '_filterValue'));
        } else {
            $this->_filterValue($valueFiltered, $valueFiltered);
        }

        return $valueFiltered;
        
    }
     public function getValue2()
    {
        
         $valueFiltered = $this->_value;

        if ($this->isArray() && is_array($valueFiltered)) {
            array_walk_recursive($valueFiltered, array($this, '_filterValue'));
        } else {
            $this->_filterValue($valueFiltered, $valueFiltered);
        }

        return $valueFiltered["value2"];
    }
}


    
    

    
   
    

