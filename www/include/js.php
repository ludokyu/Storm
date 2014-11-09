<?php session_start();
header("content-type:text/javascript");
include_once "sqli.php";
include_once "function.php";
$bd=opendb();
?>

 function init() {
 	 var lat=<?php echo LAT;?>;
 	 var lng=<?php echo LNG;?>;
 	  var lat_flag=<?php echo LAT_FLAG;?>;
 	 var lng_flag=<?php echo LNG_FLAG; ?>;
 	   var flag = new google.maps.LatLng( lat_flag ,lng_flag);
 	<?php if($_SESSION["WEB"]==1){ ?>
     //gestion des routes
     directionsDisplay = new google.maps.DirectionsRenderer();
    //emplacement par d�faut de la carte (j'ai mis Paris)
     var maison = new google.maps.LatLng(lat, lng);
   //  option par d�faut de la carte
    var myOptions = {
     zoom:15,
     mapTypeId: google.maps.MapTypeId.ROADMAP,
     center: maison
     }
     
     //creation de la map
    map = new google.maps.Map(document.getElementById("divMap"), myOptions);
      var marker = new google.maps.Marker({
      position: maison, 
      map: map
      });
      var marker2 = new google.maps.Marker({
      position: flag, 
      map: map, 
      icon:"images/<?php echo LOGO_MAP;?>"
  }); 
     //connexion de la map + le panneau de l'itin�raire
     directionsDisplay.setMap(map);
     directionsDisplay.setPanel(document.getElementById("divRoute"));
     //intialise le geocoder pour localiser les adresses 
     geocoder = new google.maps.Geocoder();
     	<?php } ?>
     }
     function reinit(){
	$("#divRoute").html("");
	$("#divRoute").css("display","none");
	<?php if($_SESSION["WEB"]==1){ ?>
	waypoint=new Array();
		arrivee=undefined;
		init();
	<?php } ?>
}

function add_line_menu(){
	last=$("input[name=count_menu[]]:last").val();
	
	k=parseInt(last)+1;
	if(last==undefined)
		k=0;
	html="<tr id='"+k+"'><td><input type='hidden' name='count_menu[]' value='"+k+"'/>";
	html+="<input type='text' name='qte_"+k+"' value='1' style='width:30px;text-align:center'/></td>";
	html+="<td><select name='cat_"+k+"' id='cat_"+k+"' onchange='modif_line_menu("+k+")'>";
	html+="<?php				$select="SELECT * FROM t_categorie WHERE is_menu=0 ORDER BY nom_cat";
				$res_cat=$bd->sql_query($select,__LINE__,__FILE__);
				while($c=$bd->sql_fetch_object($res_cat)){
					
					$is_taille=0;
					if($c->is_taille==1) $is_taille=1;
					echo "<option value='$c->id_cat' is_taille='$is_taille' >$c->nom_cat</option>";
				}
		?>";
	html+="</select></td><td><input type='button' value='Modifier la liste' onclick='$(\".list_plat_menu\").css(\"display\",\"none\");$(\"#list_plat_menu_"+k+"\").css(\"display\",\"block\");'>";
	html+="<div class='list_plat_menu' id='list_plat_menu_"+k+"'><span style='float:right;cursor:pointer' onclick='$(\"#list_plat_menu_"+k+"\").css(\"display\",\"none\");'>Fermer</span>";
	html+="<br/><span style='display:inline-block'>Plat non Compris<br/> dans le menu</span>"; 
	html+="<span style='margin-left:100px;display:inline-block'>Plat compris <br/>dans le menu</span><br/>";
	html+="<select multiple='multiple' name='no' size='5' id='list_plat_not_in_menu_"+k+"' >";
	html+="</select><span class='transfert'><br/><input type='button' value='>>' class='transfert' ";
	html+="onclick=\"$('#list_plat_not_in_menu_"+k+" option:selected').remove().appendTo('#list_plat_in_menu_"+k+"');list_default("+k+");\"/>";
	html+="<br/><br/><input type='button' value='<<' class='transfert' ";
	html+=" onclick=\"$('#list_plat_in_menu_"+k+" option:selected').remove().appendTo('#list_plat_not_in_menu_"+k+"');list_default("+k+");\"/></span>";
	html+="<select multiple='multiple' name='list_plat_"+k+"[]' size='5'  id='list_plat_in_menu_"+k+"'>";
	html+="</select><span style='clear:both'/></div>";
	html+="</td><td>Plat par d&eacute;faut : <select name='plat_defaut_"+k+"'><option/>";
	html+="</select></td><td><img src='../images/b_drop.png' alt='Supprimer' title='Supprimer' style='cursor:pointer' onclick='$(\"tr[id="+k+"]\").remove()'/></td></tr>";
	if(last!=undefined)
		$("tr[id="+last+"]").after(html);
	else $("tr:last").after(html);
}

