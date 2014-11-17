<?php

class Admin_Form_Date extends Storm_Form_Default
{

    public function init($name="")
    {
        /* Form Elements & Other Definitions Here ... */
        
		$this->setAction("/admin/chiffre/index");
		$attribs=array("attribs"=>array("onchange"=>"getMonthFromYear(this.value)"));
		$Cmd=new Admin_Model_DbTable_Cmd();
		$date=$Cmd->getYearCmd();
		$option=array();
		for($i=date("Y",strtotime($date->date_min));$i<=date("Y",strtotime($date->date_max));$i++)
			$option[$i]=$i;
		$attribs["options"]=$option;
		$this->NewElement("select","year","Année",$attribs);
		
		$attribs=array("attribs"=>array("onchange"=>"getDayFromMonthYear(\$('select#year').val(),this.value)"),
			"decorators"=>array("Label"=>array("style"=>"clear:none")));	
		
		$this->NewElement("select","month","Mois",$attribs);    
		
		$attribs=array("attribs"=>array("onchange"=>"submit()"),
			"decorators"=>array("Label"=>array("style"=>"clear:none")));

		$this->NewElement("select","day","Jour",$attribs);
      
    }


}

