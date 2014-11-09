<?php session_start();
include_once "sqli.php";
include_once "function.php";
$bd=opendb();
if(count($_REQUEST)>0){
switch ($_REQUEST["action"]){

case "cat_default_admin":
 	$u="UPDATE t_categorie SET is_default=0;";
 	$bd->sql_query($u,__LINE__,__FILE__);
 	$u="UPDATE t_categorie SET is_default=1 WHERE id_cat=".$_REQUEST["id_cat"];
 	$bd->sql_query($u,__LINE__,__FILE__);
 	break;
case "constant":
 	echo constant($_REQUEST["c"]);
 	break;
case "del_cmd":
	$sql="DELETE FROM t_panier WHERE id_cmd=".$_REQUEST["id_cmd"].";";
	$bd->sql_query($sql,__LINE__,__FILE__);
	$sql="DELETE FROM t_cmd  WHERE id_cmd=".$_REQUEST["id_cmd"];
	$bd->sql_query($sql,__LINE__,__FILE__);
	$sqltotal="SELECT SUM(total_cmd) AS total FROM t_cmd WHERE date_cmd LIKE '".date("Y-m-d",strtotime($_REQUEST["time"]))."%' ";
	
	$rest=$bd->sql_query($sqltotal,__LINE__,__FILE__);
	$t=$bd->sql_fetch_object($rest);
	$total_jour=number_format($t->total,2);
	echo $total_jour;
	break;
case "del_panier":
	$sql="UPDATE t_panier SET etat_panier = '0' WHERE id_panier=".$_REQUEST["id_panier"];
	$bd->sql_query($sql,__LINE__,__FILE__);
	$sql="DELETE FROM t_panier_menu WHERE id_panier=".$_REQUEST["id_panier"];
	$bd->sql_query($sql,__LINE__,__FILE__);
	break;
case "detail_cmd":
	$sql="SELECT * FROM t_client INNER JOIN t_cmd ON t_cmd.id_client=t_client.id_client NATURAL JOIN t_ville WHERE id_cmd=".$_REQUEST["id_cmd"];
	$res_c=$bd->sql_query($sql,__LINE__,__FILE__);
	$client=$bd->sql_fetch_object($res_c);
	if($client){
	if($client->societe!="")
		echo "$client->societe ";
	else echo "$client->nom_client ";
		echo "<br/><span id='toto'>$client->no_addr ".$GLOBALS["type_rue"][$client->type_rue]." $client->adresse_client <br/> $client->code_postal $client->nom_ville</span><br/>";
	echo "tel : $client->tel_client <br/>Heure : ".date("H:i",strtotime($client->date_cmd))."<br/>";}
	else{
		$sql="SELECT * FROM t_cmd  WHERE id_cmd=".$_REQUEST["id_cmd"];
	$res_c=$bd->sql_query($sql,__LINE__,__FILE__);
	$client=$bd->sql_fetch_object($res_c);
	echo"Heure : ".date("H:i",strtotime($client->date_cmd))."<br/>";
	}
	$sql="SELECT * FROM t_panier INNER JOIN t_categorie ON t_categorie.id_cat=t_panier.id_cat INNER JOIN t_plat ON t_plat.id_plat=t_panier.id_plat
	WHERE id_cmd=".$_REQUEST["id_cmd"];
 
	$res_c=$bd->sql_query($sql,__LINE__,__FILE__);
	$total_cmd=0;
	while($panier=$bd->sql_fetch_object($res_c)){
		echo "$panier->qte_panier $panier->nom_cat $panier->nom_plat ";
		if(!is_null($panier->id_plat_2)){
			$sql="SELECT nom_plat FROM t_plat WHERE id_plat=$panier->id_plat_2";
			$res_c2=$bd->sql_query($sql,__LINE__,__FILE__);
			$p2=$bd->sql_fetch_object($res_c2);
			echo " /$p2->nom_plat ";
		}if(!is_null($panier->id_base)){
			$sql="SELECT nom_base FROM t_base_pizza WHERE id_base=$panier->id_base";
			$res_c2=$bd->sql_query($sql,__LINE__,__FILE__);
			$p2=$bd->sql_fetch_object($res_c2);
			echo " base $p2->nom_base ";
		}
		if($panier->taille!=""){
			$tab_taille=explode(",",$panier->tab_taille);
		echo $tab_taille[$panier->taille-1]."";
		
		}
		
		if($panier->moins_ingt!=0){
			$sql="SELECT nom_ingt FROM t_ingt WHERE id_ingt IN($panier->moins_ingt)";
			$res_c2=$bd->sql_query($sql,__LINE__,__FILE__);
			while($p2=$bd->sql_fetch_object($res_c2))
			echo " - $p2->nom_ingt ";
		}
		if($panier->plus_ingt!="0"){
			$sql="SELECT nom_ingt FROM t_ingt WHERE id_ingt IN($panier->plus_ingt)";
			$res_c2=$bd->sql_query($sql,__LINE__,__FILE__);
			while($p2=$bd->sql_fetch_object($res_c2))
			echo " + $p2->nom_ingt ";
		}
		$total_cmd+=$panier->prix_panier;
		echo "-> prix : $panier->prix_panier &euro;";
		if($panier->etat_panier=="0"){
			echo " &nbsp; <span style='color:red'>Commande Supprim&eacute;</span>";
		}
		echo "<br/>";
		
	}	echo "Total commande : $total_cmd &euro; <br/>";
	break;
case  "detail_client":
	$sql="SELECT * FROM t_client  NATURAL JOIN t_ville WHERE id_client=".$_REQUEST["id_client"];
	$res_c=$bd->sql_query($sql,__LINE__,__FILE__);
	$client=$bd->sql_fetch_object($res_c);
	if($client){
	if($client->societe!="")
		echo "$client->societe ";
	else echo "$client->nom_client ";
		echo "<br/><span id='toto'>$client->no_addr ".$GLOBALS["type_rue"][$client->type_rue]." $client->adresse_client <br/> $client->code_postal $client->nom_ville</span><br/>";
	echo "tel : $client->tel_client <br/>";
	if($client->bat!="") echo "B&acirc;timent : ".$client->bat."<br/>";
	if($client->entree!="") echo "Entr&eacute;e : ".$client->entree."<br/>";
	if($client->etage!="") echo "&Eacute;tage : ".$client->etage."<br/>";
	if($client->appt!="") echo "No Appartement : ".$client->appt."<br/>";
	if($client->porte!="") echo "Porte : ".$client->porte."<br/>";
	if($client->digicode!="") echo "Digicode/Interphone : ".$client->digicode."<br/>";
	if(trim($client->rmq)!="") echo "Remarque : ".nl2br($client->rmq)."<br/>";
	}
	break;

case "form_plat_admin":
	
		$select="SELECT * FROM t_plat INNER JOIN t_categorie ON t_plat.id_cat=t_categorie.id_cat 
		WHERE id_plat=".$_GET["id_plat"];
		$res=$bd->sql_query($select,__LINE__,__FILE__);
		$b=$bd->sql_fetch_object($res);
		echo "<form id='modif_plat' name='modif_plat' onsubmit='submit_form_plat(\"modif_plat\",\"../include/ajax.php\",\"get\",\"form\");return false;'>
		<input type='hidden' name='action' value='modif_plat' /><input type='hidden' name='id_plat' value='$b->id_plat' />";
		echo "<label>Nom : </label><input type='text' name='name' value='$b->nom_plat' style='width:300px'/><br/>";
		echo "<fieldset><legend>Prix</legend>";
		if($b->is_taille==0){
		echo "<label>Sur Place</label><input type='text' name='place' value='$b->place' style='width:45px;text-align:right;padding-right:5px;'/> &euro;
		<label style='margin-left:20px'>Emporter</label><input type='text' name='go' value='$b->go' style='width:45px;text-align:right;padding-right:5px;'/> &euro;
		<label style='margin-left:20px'>Livraison</label><input type='text' name='liv' value='$b->liv' style='width:45px;text-align:right;padding-right:5px;'/> &euro;</fieldset>";
		}
		else{
			echo "<table><tr><td></td><th>Sur Place</th><th>Emporter</th><th>Livraison</th></tr>";
			$tab_taille=explode(",",$b->tab_taille);
			$prix_place=explode(",",$b->place);
			$prix_go=explode(",",$b->place);
			$prix_liv=explode(",",$b->place);
			for($t=1;$t<=$b->nb_taille;$t++){
				echo "<tr><td>".$tab_taille[$t-1]."</td><td><input type='hidden' name='is_taille' value='1'
				<input type='text' name='place[]' value='".$prix_place[$t-1]."' style='width:45px;text-align:right;padding-right:5px;'/></td>
				<td><input type='text' name='go[]' value='".$prix_go[$t-1]."' style='width:45px;text-align:right;padding-right:5px;'/></td>
				<td><input type='text' name='liv[]' value='".$prix_liv[$t-1]."' style='width:45px;text-align:right;padding-right:5px;'/></td></tr>";	
			}
			echo"</table></fieldset>";
		}
		if($b->is_base==1){
			$sql="SELECT * FROM t_base_pizza ORDER BY nom_base";
			echo "<label><input type='hidden' name='is_base' value='1'/>Choisissez la sauce comme base par defaut de votre plat : </label><select name='base' id='base'>";
			$res=$bd->sql_query($sql,__LINE__,__FILE__);
			while($base=$bd->sql_fetch_object($res)){
				$select="";
				if($base->id_base==$b->base_pizza) $select="selected='selected'";
				echo "<option $select value='$base->id_base'>$base->nom_base</option>";
			}
			echo "</select><br/>";
		}
		if($b->is_compo==1){
			echo "<label><input type='hidden' name='is_compo' value='1'/>Choix des ingr&eacute;dients de votre plat:<label>";
			echo "<div id='ingt'>";
			echo"<table class='ingt'><tr>";
			$sql="SELECT id_ingt,nom_ingt FROM t_ingt ORDER BY nom_ingt";
			$res_ingt=$bd->sql_query($sql,__LINE__,__FILE__);
			$i=0;
			while($l_ingt=$bd->sql_fetch_object($res_ingt)){
				
				if(in_array($l_ingt->id_ingt,explode(",",$b->list_ingt))) $checked=" checked='checked' ";
				else $checked="";
				
				echo "<td  id='$i' >
					<input type='checkbox' name='ingt[]' value='$l_ingt->id_ingt' $checked id='ingt$i'><label for='ingt$i'>$l_ingt->nom_ingt</label></td>";
				$i++;
				if($i%4==0)echo "</tr><tr>";
				
			}
			echo "</tr></table>";
		}
		if($b->is_menu==1){
			echo "<input type='hidden' name='is_menu' value='1'/>
			<input type='button' value='Ajouter un composant &agrave; votre menu' onclick='add_line_menu()'/>
			<table><tr><th>Qt&eacute;</th><th>Cat&eacute;gorie</th><th >Plats</th><td></td><td></td></tr>";
			$select="SELECT t_menu.*,tab_taille FROM t_menu INNER JOIN t_categorie ON t_categorie.id_cat=t_menu.id_cat WHERE id_plat=$b->id_plat";
			
			$res_menu=$bd->sql_query($select,__LINE__,__FILE__);
			$k=0;
			while($m=$bd->sql_fetch_object($res_menu)){
				echo "<tr id='$k'><td><input type='hidden' name='count_menu[]' value='$k'/>
				<input type='text' name='qte_$k' value='$m->qte' style='width:30px;text-align:center'/></td>
				<td><select name='cat_$k' id='cat_$k' onchange='modif_line_menu($k)'>";
				$select="SELECT * FROM t_categorie WHERE is_menu=0 ORDER BY nom_cat";
				$res_cat=$bd->sql_query($select,__LINE__,__FILE__);
				while($c=$bd->sql_fetch_object($res_cat)){
					$select="";
					if($c->id_cat==$m->id_cat) $select="selected='selected'";
					$is_taille=0;
					if($c->is_taille==1) $is_taille=1;
					echo "<option value='$c->id_cat' is_taille='$is_taille' $select>$c->nom_cat</option>";
				}
				if(!is_null($m->taille)){
					$tab_taille=explode(",",$m->tab_taille);
					echo "<select name='taille_menu_$k'>";
					$f=1;
					foreach($tab_taille as $key=>$val){
						$select="";
						if($m->taille==($key+1))
							$select="selected='selected'";
					echo	"<option id='$f' value='".($key+1)."' $select>$val</option>";
					}
					
				}
				echo "</select></td><td><input type='button' value='Modifier la liste' onclick='\$(\"div.list_plat_menu\").css(\"display\",\"none\");\$(\"#list_plat_menu_".$k."\").css(\"display\",\"block\");'>
				<div class='list_plat_menu' id='list_plat_menu_$k'><span style='float:right;cursor:pointer' onclick='\$(\"#list_plat_menu_".$k."\").css(\"display\",\"none\");'>Fermer</span><br/>
				<span style='display:inline-block'>Plat non Compris<br/> dans le menu</span> 
				<span style='display:inline-block;margin-left:100px'>Plat compris <br/>dans le menu</span><br/>
				<select multiple='multiple' name='no' size='5' id='list_plat_not_in_menu_$k' >";
				$select="SELECT * FROM t_plat WHERE id_cat=$m->id_cat AND id_plat NOT IN($m->list_plat)";
				$res_no_plat=$bd->sql_query($select,__LINE__,__FILE__);
				while($l=$bd->sql_fetch_object($res_no_plat)){
					echo "<option value='$l->id_plat'>$l->nom_plat</option>";	
				}
				echo"</select>
				<span class='transfert'><br/>
				<input type='button' value='>>' class='transfert' onclick=\"\$('#list_plat_not_in_menu_$k option:selected').remove().appendTo('#list_plat_in_menu_$k');list_default($k);\"/>
				<br/><br/>
				<input type='button' value='<<' class='transfert' onclick=\"\$('#list_plat_in_menu_$k option:selected').remove().appendTo('#list_plat_not_in_menu_$k');list_default($k);\"/></span>
				<select multiple='multiple' name='list_plat_".$k."[]' size='5'  id='list_plat_in_menu_$k'>";
				$select="SELECT * FROM t_plat WHERE  id_plat IN($m->list_plat)";
				
				$res_plat=$bd->sql_query($select,__LINE__,__FILE__);
				while($l=$bd->sql_fetch_object($res_plat)){
					echo "<option value='$l->id_plat'>$l->nom_plat</option>";	
				}
				
				
				
				echo"</select><span style='clear:both'/></div>
				</td><td>Plat par d&eacute;faut : <select name='plat_defaut_$k'><option/>";
				$select="SELECT * FROM t_plat WHERE id_cat=$m->id_cat AND id_plat IN($m->list_plat)";
							$res_plat=$bd->sql_query($select,__LINE__,__FILE__);
				while($l=$bd->sql_fetch_object($res_plat)){
					$select="";
					if($l->id_plat==$m->id_plat_default)$select="selected='selected'";
					echo "<option value='$l->id_plat' $select>$l->nom_plat</option>";	
				}
				
				
				echo"</select></td><td><img src='../images/b_drop.png' alt='Supprimer' title='Supprimer' style='cursor:pointer' onclick='\$(\"tr[id=$k]\").remove()'/></td></tr>";
				$k++;
			}
			echo "</table>";
		}
		echo "<center style='margin-top:10px;'><input type='submit' value='Modifier'/></center>";
		
	
	break;

case "ajout_plat_admin":
	
		$select="SELECT * FROM  t_categorie WHERE id_cat=".$_GET["id_cat"];
		$res=$bd->sql_query($select,__LINE__,__FILE__);
		$b=$bd->sql_fetch_object($res);
		echo "<form id='modif_plat' name='modif_plat' method='post' action='plat.php'>
		<input type='hidden' name='action' value='ajout_plat' />
		<input type='hidden' name='id_cat' value='".$_GET["id_cat"]."' />";
		echo "<label>Nom : </label><input type='text' name='name' value='' style='width:300px'/><br/>";
		echo "<fieldset><legend>Prix</legend>";
		if($b->is_taille==0){
		echo "<label>Sur Place</label><input type='text' name='place' value='' style='width:45px;text-align:right;padding-right:5px;'/> &euro;
		<label style='margin-left:20px'>Emporter</label><input type='text' name='go' value='' style='width:45px;text-align:right;padding-right:5px;'/> &euro;
		<label style='margin-left:20px'>Livraison</label><input type='text' name='liv' value='' style='width:45px;text-align:right;padding-right:5px;'/> &euro;</fieldset>";
		}
		else{
			echo "<table><tr><td></td><th>Sur Place</th><th>Emporter</th><th>Livraison</th></tr>";
			$tab_taille=explode(",",$b->tab_taille);
			
			for($t=1;$t<=$b->nb_taille;$t++){
				echo "<tr><td>".$tab_taille[$t-1]."</td><td><input type='hidden' name='is_taille' value='1'
				<input type='text' name='place[]' value='' style='width:45px;text-align:right;padding-right:5px;'/></td>
				<td><input type='text' name='go[]' value='' style='width:45px;text-align:right;padding-right:5px;'/></td>
				<td><input type='text' name='liv[]' value='' style='width:45px;text-align:right;padding-right:5px;'/></td></tr>";	
			}
			echo"</table></fieldset>";
		}
		if($b->is_base==1){
			$sql="SELECT * FROM t_base_pizza ORDER BY nom_base";
			echo "<label><input type='hidden' name='is_base' value='1'/>Choisissez la sauce comme base par defaut de votre plat : </label><select name='base' id='base'>";
			$res=$bd->sql_query($sql,__LINE__,__FILE__);
			while($base=$bd->sql_fetch_object($res)) echo "<option value='$base->id_base'>$base->nom_base</option>";
			
			echo "</select><br/>";
		}
		if($b->is_compo==1){
			echo "<label><input type='hidden' name='is_compo' value='1'/>Choix des ingr&eacute;dients de votre plat:<label>";
			echo "<div id='ingt'>";
			echo"<table class='ingt'><tr>";
			$sql="SELECT id_ingt,nom_ingt FROM t_ingt ORDER BY nom_ingt";
			$res_ingt=$bd->sql_query($sql,__LINE__,__FILE__);
			$i=0;
			while($l_ingt=$bd->sql_fetch_object($res_ingt)){
				
				
				echo "<td  id='$i' >
					<input type='checkbox' name='ingt[]' value='$l_ingt->id_ingt' id='ingt$i'><label for='ingt$i'>$l_ingt->nom_ingt</label></td>";
				$i++;
				if($i%4==0)echo "</tr><tr>";
				
			}
			echo "</tr></table>";
		}
		if($b->is_menu==1){
			echo "<input type='hidden' name='is_menu' value='1'/>
			<input type='button' value='Ajouter un composant &agrave; votre menu'  onclick='add_line_menu()'/>
			<table><tr><th>Qt&eacute;</th><th>Cat&eacute;gorie</th><th>Plats</th><td></td></tr>";
			
		}
		echo "</table><center style='margin-top:10px;'><input type='submit' value='Ajouter'/></center>";
		
	
	break;
case "ajout_ingt_admin":
	echo  "<form id='modif_plat' name='modif_plat' method='post' action='ingt.php'>
		<input type='hidden' name='action' value='ajout_ingt' />";
		echo "<label>Nom : </label><input type='text' name='name' value='' style='width:300px'/><br/>
		<br/><center><input type='submit' value='Ajouter'/></center></form>";
	break;

case "modif_ingt_admin":
	$sql="SELECT * FROM t_ingt WHERE id_ingt=".$_REQUEST["id_ingt"];
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$ingt=$bd->sql_fetch_object($res);
	echo  "<form id='modif_plat' name='modif_plat' onsubmit='submit_form(\"modif_plat\",\"../include/ajax.php\",\"get\",\"form\");return false;'>
		<input type='hidden' name='action' value='modif_ingt' />
		<input type='hidden' name='id_ingt' value='$ingt->id_ingt'/>";
		echo "<label>Nom : </label><input type='text' name='name' value='$ingt->nom_ingt' style='width:300px'/><br/>
		<br/><center><input type='submit' value='Modifier'/></center></form>";
	break;
case "ajout_sauce_admin":
	echo  "<form id='modif_plat' name='modif_plat' method='post' action='sauce.php'>
		<input type='hidden' name='action' value='ajout_sauce' />";
		echo "<label>Nom : </label><input type='text' name='name' value='' style='width:300px'/><br/>
		<br/><center><input type='submit' value='Ajouter'/></center></form>";
	break;

case "modif_sauce_admin":
	$sql="SELECT * FROM t_base_pizza WHERE id_base=".$_REQUEST["id_base"];
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$ingt=$bd->sql_fetch_object($res);
	echo  "<form id='modif_plat' name='modif_plat' onsubmit='submit_form(\"modif_plat\",\"../include/ajax.php\",\"get\",\"form\");return false;'>
		<input type='hidden' name='action' value='modif_sauce' />
		<input type='hidden' name='id_base' value='$ingt->id_base'/>";
		echo "<label>Nom : </label><input type='text' name='name' value='$ingt->nom_base' style='width:300px'/><br/>
		<br/><center><input type='submit' value='Modifier'/></center></form>";
	break;
case "modif_cat_admin":
	$sql="SELECT * FROM t_categorie WHERE id_cat=".$_REQUEST["id_cat"];
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$cat=$bd->sql_fetch_object($res);
	$menu_ok=$menu_ko=$compo_ok=$compo_ko=$taille_ok=$taille_ko=$disabled=$pizza_ok=$pizza_ko=$base_ok=$base_ko=$afficher_ok=$afficher_ko=$ticket_ok=$ticket_ko="";
	if($cat->is_menu==1) $menu_ok="checked='checked'";
	else $menu_ko="checked='checked'";
	if($cat->is_compo==1) $compo_ok="checked='checked'";
	else $compo_ko="checked='checked'";
	if($cat->is_taille==1)$taille_ok="checked='checked'";
	else {$taille_ko="checked='checked'";$disabled="disabled=''disabled'";}
	if($cat->is_pizza==1) $pizza_ok="checked='checked'";
	else $pizza_ko="checked='checked'";
	if($cat->is_base==1) $base_ok="checked='checked'";
	else $base_ko="checked='checked'";
	if($cat->afficher==1) $afficher_ok="checked='checked'";
	else $afficher_ko="checked='checked'";
	if($cat->big_on_ticket==1) $ticket_ok="checked='checked'";
	else $ticket_ko="checked='checked'";
	echo  "<form id='modif_plat' name='modif_plat' onsubmit='submit_form(\"modif_plat\",\"../include/ajax.php\",\"get\",\"form\");return false;'>
		<input type='hidden' name='action' value='modif_cat' />
		<input type='hidden' name='id_cat' value='$cat->id_cat'/>";
		echo "<label>Nom : </label><input type='text' name='name' value='$cat->nom_cat' style='width:300px'/><br/>
		<br/><label>Est ce un menu ? </label><div style='text-align:right;width:150px;float:none'><input type='radio' name='is_menu' value='1'  $menu_ok/><label>Oui</label>
		<input type='radio' name='is_menu' value='0'  $menu_ko/><label>Non</label></div><br/>
		<label>Contient plusieurs ingredients ? </label><div style='text-align:right;width:150px;'><input type='radio' name='is_compo' value='1'  $compo_ok/><label>Oui</label>
		<input type='radio' name='is_compo' value='0'  $compo_ko/><label>Non</label></div><br/>
		<label>Plusieurs tailles sont possible ? </label><div style='text-align:right;width:150px;'>
		<input type='radio' name='is_taille' value='1'  $taille_ok
		onclick=\"valid_taille()\"/><label>Oui</label>
		<input type='radio' name='is_taille' value='0'  $taille_ko 
		onclick=\"valid_taille()\"/><label>Non</label></div><br/>
		
		<label>Nombre de taille</label><input type='text' name='nb_taille' value='$cat->nb_taille' $disabled onblur='if(!isNaN(this.value)){add_taille(this.value);}else{alert(\"Ce n est pas un nombre!\");this.focus();}'><br/><br/>
		";
		$taille=explode(",",$cat->tab_taille);
		if($cat->is_taille==1){
			for($i=0; $i<$cat->nb_taille;$i++){
				echo "<label for='tab_taille'>Nom de la taille</label><input type='text' name='tab_taille[]' value='".$taille[$i]."' id='".($i+1)."'/><br id='tab_taille'/>";
			}
			echo"<br/>";
		}
		echo "	<label>Est il possible d'utiliser 2 moiti&eacute; de plat diff&eacute;rents ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='is_pizza' value='1'  $pizza_ok/><label>Oui</label>
		<input type='radio' name='is_pizza' value='0'  $pizza_ko/><label>Non</label></div><br/>";
		echo "	<label>Plusieurs sauce sont-elles possible pour cette cat&eacute;gorie ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='is_base' value='1'  $base_ok/><label>Oui</label>
		<input type='radio' name='is_base' value='0'  $base_ko/><label>Non</label></div><br/>";
		echo "	<label>Afficher le nom de cette cat&eacute;gorie sur le ticket ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='afficher' value='1'  $afficher_ok/><label>Oui</label>
		<input type='radio' name='afficher' value='0'  $afficher_ko/><label>Non</label></div><br/>";
		echo "	<label>Mettre en &eacute;vidence le nom des plats de cette cat&eacute;gorie sur le ticket ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='big_on_ticket' value='1'  $ticket_ok/><label>Oui</label>
		<input type='radio' name='big_on_ticket' value='0'  $ticket_ko/><label>Non</label></div>";
		echo"<br/><center><input type='submit' value='Modifier'/></center></form>";
	break;
case "ajout_cat_admin":
	
	echo  "<form id='modif_plat' name='modif_plat' action='cat.php' method='post'>
		<input type='hidden' name='action' value='ajout_cat' />
		<input type='hidden' name='id_cat' value='$cat->id_cat'/>";
		echo "<label>Nom : </label><input type='text' name='name' value='' style='width:300px'/><br/>
		<br/><label>Est ce un menu ? </label><div style='text-align:right;width:150px;float:none'><input type='radio' name='is_menu' value='1'  /><label>Oui</label>
		<input type='radio' name='is_menu' value='0' checked='checked'/><label>Non</label></div><br/>
		<label>Contient plusieurs ingredients ? </label><div style='text-align:right;width:150px;'><input type='radio' name='is_compo' value='1' checked='checked'/><label>Oui</label>
		<input type='radio' name='is_compo' value='0'  /><label>Non</label></div><br/>
		<label>Plusieurs tailles sont possible ? </label><div style='text-align:right;width:150px;'>
		<input type='radio' name='is_taille' value='1' 
		onclick=\"valid_taille()\"/><label>Oui</label>
		<input type='radio' name='is_taille' value='0'  checked='checked' 
		onclick=\"valid_taille()\"/><label>Non</label></div><br/>
		
		<label>Nombre de taille</label><input type='text' name='nb_taille' value='' disabled='disabled' onblur='if(!isNaN(this.value)){add_taille(this.value);}else{alert(\"Ce n est pas un nombre!\");this.focus();}'><br/><br/>
		";
		
		echo "	<label>Est il possible d'utiliser 2 moiti&eacute; de plat diff&eacute;rents ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='is_pizza' value='1' /><label>Oui</label>
		<input type='radio' name='is_pizza' value='0' checked='checked'/><label>Non</label></div><br/>";
		echo "	<label>Plusieurs sauce sont-elles possible pour cette cat&eacute;gorie ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='is_base' value='1'  /><label>Oui</label>
		<input type='radio' name='is_base' value='0' checked='checked'/><label>Non</label></div><br/>";
		echo "	<label>Afficher le nom de cette cat&eacute;gorie sur le ticket ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='afficher' value='1'checked='checked'/><label>Oui</label>
		<input type='radio' name='afficher' value='0'  /><label>Non</label></div><br/>";
		echo "	<label>Mettre en &eacute;vidence le nom des plats de cette cat&eacute;gorie sur le ticket ? </label>
		<div style='text-align:right;width:150px;'><input type='radio' name='big_on_ticket' value='1'  /><label>Oui</label>
		<input type='radio' name='big_on_ticket' value='0'  checked='checked'/><label>Non</label></div>";
		echo"<br/><center><input type='submit' value='Modifier'/></center></form>";
	break;
case "modif_cat":
	$update="UPDATE t_categorie SET nom='".addslashes(htmlentities($_REQUEST["name"]))."'";
	$update.=",is_menu=".$_REQUEST["is_menu"];
	$update.=",is_compo=".$_REQUEST["is_compo"];
	$update.=",is_menu=".$_REQUEST["is_menu"];
	$update.=",is_taille=".$_REQUEST["is_taille"];
	if($_REQUEST["is_taille"]==1){
		$update.=",nb_taille=".$_REQUEST["nb_taille"];
		$update.=",tab_taille='".addslashes(htmlentities(implode(",",$_REQUEST["tab_taille"])))."'";
	}
	$update.=",is_pizza=".$_REQUEST["is_pizza"];
	$update.=",is_base=".$_REQUEST["is_base"];
	$update.=",afficher=".$_REQUEST["afficher"];
	$update.=",big_on_ticket=".$_REQUEST["big_on_ticket"];
	$update.=" WHERE id_cat=".$_REQUEST["id_cat"];
	if($bd->sql_query($update,__LINE__,__FILE__)) echo "Modification effectu&eacute; avec succ&egrave;s";
	break;
case "modif_ingt":
	$sql="UPDATE t_ingt SET nom_ingt='".addslashes(htmlentities($_REQUEST["name"]))."' WHERE id_ingt=".$_REQUEST["id_ingt"];
	if($bd->sql_query($sql,__LINE__,__FILE__)) echo "Modification effectu&eacute; avec succ&egrave;s";
	break;
case "modif_sauce":
	$sql="UPDATE t_base_pizza SET nom_base='".addslashes(htmlentities($_REQUEST["name"]))."' WHERE id_base=".$_REQUEST["id_base"];
	if($bd->sql_query($sql,__LINE__,__FILE__)) echo "Modification effectu&eacute; avec succ&egrave;s";
	break;
case "modif_plat":
	$update="UPDATE t_plat SET nom_plat='".addslashes($_REQUEST["name"])."'";
	if(!isset($_REQUEST["is_taille"])) $update.=",place='".$_REQUEST["place"]."',go='".$_REQUEST["go"]."',liv='".$_REQUEST["liv"]."'";
	else $update.=",place='".implode(",",$_REQUEST["place"])."',go='".implode(",",$_REQUEST["go"])."',liv='".implode(",",$_REQUEST["liv"])."'";
	if(isset($_REQUEST["is_compo"])) $update.=",list_ingt='".implode(",",$_REQUEST["ingt"])."'";
	if(isset($_REQUEST["is_base"])) $update.=",base_pizza='".implode(",",$_REQUEST["base"])."'";
	$update.=" WHERE id_plat=".$_REQUEST["id_plat"].";";
	if(isset($_REQUEST["is_menu"])){
		$delete="DELETE FROM t_menu WHERE id_plat=".$_REQUEST["id_plat"];
		$bd->sql_query($delete,__LINE__,__FILE__);
	
		$nb=count($_REQUEST["count_menu"]);
		for($i=0;$i<$nb;$i++){
			
			$insert="INSERT INTO t_menu SET id_menu=NULL,id_plat=".$_REQUEST["id_plat"];
			$insert.=",qte=".$_REQUEST["qte_$i"];
			$insert.=",id_cat=".$_REQUEST["cat_$i"];
			if(isset($_REQUEST["list_plat_$i"]))
				$insert.=",list_plat='".implode(",",$_REQUEST["list_plat_$i"])."'";
			else    $insert.=",list_plat=(SELECT GROUP_CONCAT(id_plat SEPARATOR ',') FROM t_plat WHERE id_cat=".$_REQUEST["cat_$i"]." )";
			if($_REQUEST["plat_defaut_$i"]!="")
				$insert.=",id_plat_default=".$_REQUEST["plat_defaut_$i"];
			if(isset($_REQUEST["taille_menu_$i"])){
				$insert.=",taille=".$_REQUEST["taille_menu_$i"];
			}
			if($_REQUEST["plat_defaut_$i"]=="" && (!isset($_REQUEST["list_plat_$i"]) || count($_REQUEST["list_plat_$i"])>1))
				$insert.=",is_nec=1";
			$insert.=";
			";
			$bd->sql_query($insert,__LINE__,__FILE__);
			
		}
		
	}
	if($bd->sql_query($update,__LINE__,__FILE__)) echo "Modification effectu&eacute; avec succ&egrave;s ".nl2br($update);
	else echo "Erreur durant l'enregistrement";
	break;

case "insert_cmd":
	$client="" ;
	if($_REQUEST["id_client"]!="")
		$client="id_client=".$_REQUEST["id_client"]." ,";
	elseif($_REQUEST["type_cmd"]==3){
		$sql="SELECT MAX(id_client) AS id_client FROM t_client";
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$c=$bd->sql_fetch_object($res);
	$client="id_client=$c->id_client ,";
	}
	$sql="SELECT MAX(no_cmd) AS max FROM t_cmd";
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$r=$bd->sql_fetch_object($res);
	$m=intval($r->max)+1;
	if((isset($_SESSION["last_cmd"]) && $_SESSION["last_cmd"]["total"]!=0) || !isset($_SESSION["last_cmd"]["total"])){
	
	$sql="INSERT INTO t_cmd SET $client type_cmd=".$_REQUEST["type_cmd"].",no_cmd=$m";
	$_SESSION["last_cmd"]=array();
	$_SESSION["last_cmd"]["client"]=$client;
	$_SESSION["last_cmd"]["total"]=0;
	$bd->sql_query($sql,__LINE__,__FILE__);
	$id= $bd->sql_last_id();
	$_SESSION["last_cmd"]["id_cmd"]=$id;
	echo $id;
	}
	break;

case "inventaire":
	$req="";
	$nb=count($_REQUEST["count"]);
	$total=0;
	echo "<input type='button' value='Imprimer' style='float:left'
	onclick=\"document.inventaire.method='POST';document.inventaire.target='print';document.inventaire.action='print_inventaire.php';document.inventaire.submit();\"/><br style='clear:both'/>";
	for($i=0;$i<$nb;$i++){
		$bg="68,68,68";
		if($i%2==0) $bg="102,102,102";
		if($_REQUEST["type_$i"]=="ingt"){
			$req="UPDATE t_ingt SET prix_inv=".$_REQUEST["prix_$i"]." WHERE id_ingt=".$_REQUEST["id_$i"]." ;";
			$bd->sql_query($req,__LINE__,__FILE__);
		}
		elseif($_REQUEST["type_$i"]=="plat"){
			$req="UPDATE t_plat SET prix_plat=".$_REQUEST["prix_$i"]." WHERE id_plat=".$_REQUEST["id_$i"]." ;";
			$bd->sql_query($req,__LINE__,__FILE__);
		}
		$prix_p=$_REQUEST["qte_$i"]*$_REQUEST["prix_$i"];
		$total+=$prix_p;
		echo "<span style='float:left;background-color:rgba($bg, 0.5);width:400px;display:inline-block;'>".$_REQUEST["name_$i"]." : </span>
		<span style='float:right;background-color:rgba($bg, 0.5)'>".$_REQUEST["qte_$i"]." x ".$_REQUEST["prix_$i"]."= $prix_p &euro; </span><br style='clear:both'/>";
	}
	echo "TOTAL : $total &euro;";
	break;
case "list_cmd_admin":
	$sql="SELECT id_cmd, type_cmd, total_cmd,id_client FROM t_cmd WHERE date_cmd LIKE '".date("Y-m-d",strtotime($_REQUEST["time"]))."%' ORDER BY date_cmd DESC ";
//
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$i=1;
	echo"<table border='1' id='list_cmd_global'><tr><th>Type de Commande</th><th style='width:75px'>Total</th><th>".$_REQUEST["total_jour"]." &euro;</th></tr>";
	while($cmd=$bd->sql_fetch_object($res)){
	
	
		echo "<tr id='$i'  id_cmd='$cmd->id_cmd' 
		onclick=\"\$('tr').attr('class','');\$('tr[id=$i]').attr('class','hightlight')\">
		<td style='text-align:center;font-weight:bold'>";
	
		switch ($cmd->type_cmd){
		case 1://sur place
			echo "SUR PLACE";
			break;
		case 2://emporter
			echo "EMPORTER";
			break;
		case 3://livraison
			echo "LIVRAISON";
			break;
		}
		echo "</td><td valign='top'>$cmd->total_cmd &euro;</td><td valign='top'><input type='button' style='cursor:pointer' value='Voir le d&eacute;tail'
		onclick='affich_detail_cmd($cmd->id_cmd)'/>";
		if($cmd->type_cmd==3)
		echo"<input type='button' style='cursor:pointer' value='Voir les coordonn&eacute;es du client '
		onclick='affich_detail_client($cmd->id_client)'/>";
		
		echo"</td></tr>";
		
		$i++;
	}
	echo "</table>";
	break;
case "list_mois_admin":
	$sqltotal="SELECT SUM(total_cmd) AS total,date_cmd FROM t_cmd WHERE date_cmd LIKE '".date("Y-m",strtotime($_REQUEST["time"]))."%' GROUP BY SUBSTR(date_cmd,1,10) ";

	$rest=$bd->sql_query($sqltotal,__LINE__,__FILE__);
	
	echo"
	<table border='1' id='list_cmd_global'><tr><td colspan='2'>
	<input type='button' value='Imprimer' onclick='window.open(\"print_mois.php?mois=".date("Y-m",strtotime($_REQUEST["time"]))."\")'/></td></tr>
	<tr><th>Date de la Commande</th><th style='width:75px'>Total</th></tr>";
	while($cmd=$bd->sql_fetch_object($rest)){
		
		
		echo "<tr >
		<td style='text-align:center;font-weight:bold'>";
	
		
			echo date("d",strtotime($cmd->date_cmd))." ".$cal[date("n",strtotime($cmd->date_cmd))]." ".date("Y",strtotime($cmd->date_cmd));
			
		echo "</td><td valign='top'>".number_format($cmd->total,2)." &euro;</td></tr>";
		
		
	}
	echo "</table>";
	break;
case "list_jour":
	$sql="SELECT SUBSTR( date_cmd,9, 2 ) AS jour FROM t_cmd 
	WHERE date_cmd LIKE '".$_REQUEST["a"]."-".str_pad($_REQUEST["m"], 2, "0", STR_PAD_LEFT)."%' GROUP BY SUBSTR( date_cmd,9, 2 ) ";
	$rest=$bd->sql_query($sql,__LINE__,__FILE__);
	echo "<option/>";
	while($t=$bd->sql_fetch_object($rest))
echo "<option value='$t->jour'> $t->jour</option>";
	break;
case "list_mois":
	$sql="SELECT SUBSTR( date_cmd,6, 2 ) AS mois FROM t_cmd WHERE date_cmd LIKE '".$_REQUEST["value"]."%' GROUP BY SUBSTR( date_cmd,6, 2 ) ";
	$rest=$bd->sql_query($sql,__LINE__,__FILE__);
	echo "<option/>";
	while($t=$bd->sql_fetch_object($rest))
echo "<option value='".intval($t->mois)."> : ".$cal[intval($t->mois)] ."</option>";
	break;
case "list_plat":
	$i=1;
	if($_REQUEST["id_cat"]!=""){
	$sql="SELECT id_plat,nom_plat FROM  t_plat  WHERE id_cat = ".$_REQUEST["id_cat"]." ORDER BY nom_plat";
	
		$res_v=$bd->sql_query($sql,__LINE__,__FILE__);
		while($ville=$bd->sql_fetch_object($res_v)){
			echo "<option id='$i' value='$ville->id_plat'";
			$i++;
		if(isset($cmd->id_plat) && $ville->id_plat==$cmd->id_plat) echo "selected='selected' ";
		echo ">".htmlentities($ville->nom_plat)."</option>";}
	}else echo "";
	break;

case "livreur":
	$sql="UPDATE t_cmd SET id_livreur=".$_REQUEST["value"]. " WHERE id_cmd=".$_REQUEST["id_cmd"].";";
	$bd->sql_query($sql,__LINE__,__FILE__);
	$sql="UPDATE t_livreur SET is_fav=1 WHERE id_livreur=".$_REQUEST["value"];
	$bd->sql_query($sql,__LINE__,__FILE__);
	$sqllivreur="SELECT SUM(total_cmd) AS total, nom_livreur FROM t_cmd INNER JOIN t_livreur ON t_livreur.id_livreur=t_cmd.id_livreur
	WHERE date_cmd LIKE '".date("Y-m-d")."%' GROUP BY t_cmd.id_livreur";

	$rest=$bd->sql_query($sqllivreur,__LINE__,__FILE__);
	while($t=$bd->sql_fetch_object($rest))
		echo "- <span style='color:lightgreen'>$t->nom_livreur</span> : ".number_format($t->total,2) ." &euro;<br/>";
	break;

case "modif_pizza":
	$sql="SELECT id_plat,nom_plat FROM  t_plat  WHERE id_cat =".$_REQUEST["cat"]."";
	if(isset($_REQUEST["menu"]))
		$sql.=" AND id_plat IN(SELECT list_plat FROM t_menu WHERE id_menu=".$_REQUEST["menu"].") ";
	$sql.=" ORDER BY nom_plat";

		$select="";
		$res_v=$bd->sql_query($sql,__LINE__,__FILE__);
	while($ville=$bd->sql_fetch_object($res_v)){
		echo "<option id='$ville->id_plat' value='$ville->id_plat'>$ville->nom_plat</option>";}
	break;
case "recette":
	$sqltotal="SELECT SUM(total_cmd) AS total FROM t_cmd WHERE date_cmd LIKE '".date("Y",strtotime($_REQUEST["time"]))."%' ";
	
	$rest=$bd->sql_query($sqltotal,__LINE__,__FILE__);
	$t=$bd->sql_fetch_object($rest);
	echo "Total pour ".date("Y",strtotime($_REQUEST["time"])).": ".number_format($t->total,2)."&euro;<br/>";
	
		$sqltotal="SELECT SUM(total_cmd) AS total FROM t_cmd WHERE date_cmd LIKE '".date("Y-m",strtotime($_REQUEST["time"]))."%' ";
	
	$rest=$bd->sql_query($sqltotal,__LINE__,__FILE__);
	$t=$bd->sql_fetch_object($rest);
	echo "Total pour ".$cal[date("n",strtotime($_REQUEST["time"]))]." ".date("Y",strtotime($_REQUEST["time"])).": ".number_format($t->total,2)."&euro;
	<div id='blocaction2' style='display:inline'>+ d&eacute;tails	</div><br/>";
			$sqltotal="SELECT SUM(total_cmd) AS total FROM t_cmd WHERE date_cmd LIKE '".date("Y-m-d",strtotime($_REQUEST["time"]))."%' ";
	$rest=$bd->sql_query($sqltotal,__LINE__,__FILE__);
	$t=$bd->sql_fetch_object($rest);
		
	echo "Total du ".date("d-m-Y",strtotime($_REQUEST["time"])).": ".number_format($t->total,2)."&euro;<div id='blocaction'>+ d&eacute;tails
	</div>";
	break;
case "search_liv":
	$sql="SELECT * FROM t_livreur WHERE nom_livreur LIKE '".$_REQUEST["value"]."%' ORDER BY nom_livreur  ";
$res=$bd->sql_query($sql,__LINE__,__FILE__);
	while($li=$bd->sql_fetch_object($res)){
	echo "<span style='display:inline-block;width:200px'>$li->nom_livreur";
	
	if($li->is_fav==1)echo "<img src='../images/star.gif'/>";
	echo"</span> 
	<span style='display:inline-block;width:150px;margin-right:10px;'>$li->tel_livreur </span>
	
	
	<input type='button' value='Modifier' onclick='location.href=\"?action=form_modif_livreur&id_livreur=$li->id_livreur\"'/>
	<input type='button' value='Supprimer' onclick='location.href=\"?action=del_livreur&id_livreur=$li->id_livreur\"'/><hr style='margin:0px;width:550px;'/><br/>";
}
	break;

case "update_cmd":
	$client="";
	if($_REQUEST["id_client"]!="")
		$client="id_client=".$_REQUEST["id_client"]." ,";
	$sql="UPDATE t_cmd SET $client total_cmd= (
	SELECT SUM(prix_panier) FROM t_panier WHERE etat_panier LIKE '1' AND id_cmd=".$_REQUEST["id_cmd"].")
	,type_cmd=".$_REQUEST["type_cmd"]." WHERE id_cmd=".$_REQUEST["id_cmd"];
	$bd->sql_query($sql,__LINE__,__FILE__);
	$sql="SELECT SUM(prix_panier) AS total FROM t_panier WHERE etat_panier LIKE '1' AND id_cmd=".$_REQUEST["id_cmd"];
	$res=$bd->sql_query($sql,__LINE__,__FILE__);
	$t=$bd->sql_fetch_object($res);
	$_SESSION["last_cmd"]["total"]=$t->total;
	break;


case "verif_pwd":
	$ini=parse_ini_file("../storm.ini");
	$m=$ini["mdp_cmd"];
	
	if(md5($_REQUEST["value"])==trim($m))
		echo 1;
	else echo 2;
	
	break;



}
}
else echo "Aucune donn&eacute;e re&ccedil;ue";
?>
