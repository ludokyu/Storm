<?php if(!isset($_SESSION))session_start();
//error_reporting(E_ALL ^ E_NOTICE);
$root="";
	if(!is_file("storm.ini"))
		$root="../";
$ini=parse_ini_file($root."storm.ini");
error_reporting($ini["error_reporting"]);

$cal=array("","Janvier","Fevrier","Mars","Avril","Mai", "Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre");

if(isset($ini["pizza"])){
define("PIZZA",$ini["pizza"]);
define("TEL",$ini["tel"]);
define("LOGO",$ini["logo"]);
define("LOGO_MAP",$ini["logo_map"]);
define("WEB",$ini["web"]);
define("ADDR",$ini["addr"]);
define("LAT",$ini["lat"]);
define("LNG",$ini["lng"]);
define("LAT_FLAG",$ini["lat_flag"]);
define("LNG_FLAG",$ini["lng_flag"]);
define("SIRET",$ini["siret"]);
}
else include_once"pizza.php";
if (!$sock = @fsockopen('www.google.fr', 80, $num, $error, 5)){
$_SESSION["WEB"]=0;

}
else
$_SESSION["WEB"]=1;



?>
