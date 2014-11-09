<?php

class sql{

	
	function __construct($host,$user,$pwd,$db){
		
		
		$this->db=$db;
		$this->user=$user;
		$this->pwd=$pwd;
		$this->host=$host;
		
		
	}
	
	function sql_affected_rows($res){
		$nb=$res->affected_rows();
			if($nb===false)
			return false;
			else
			return $nb;
			
		}
	
	function sql_change_user($user,$pwd,$database,$link_identifier){
		$fct=$link_identifier->change_user($user,$pwd,$database);
		
		if($fct===false)
			die("<br>erreur dans le changement d'utilisateur ");
		else{
			$this->user=$user;
			$this->pwd=$pwd;
			$this->bd=$database;
		}
		
	}
	function sql_client_encoding($link){
		$client=$link->client_encoding();
			if($client===false)
			return false;
			
			
		
	}
	
	
	function sql_close(){
		$cl=$this->cnx->close();
		if($cl===false)
			return false;
	}
	
	
	function sql_connect($host,$user,$pwd){
	$id_connexion = new mysqli ($host,$user,$pwd) ;
	if(!$id_connexion){
		die($this->sql_error($id_connexion));
	}
	else{
		$this->host=$host;
		$this->user=$user;
		$this->pwd=$pwd;
		return $id_connexion;
	}
}
function sql_last_id(){
	return $this->cnx->insert_id;
	
}


function sql_error($res){
	//echo mysql_error();
	$error=$res->errno;
	//header("Location:../erreur_$error.html");
	switch($error){
	case 1045:
		echo "Probleme de connexion a la base de donn&eacute;e ";
		break;
	case 1203:
	case 1040:
		echo "Trop de connexion";
		break;
	case 2003:
		echo "connexion impossible";
		break;
	case 1049:
		echo "Base de donn&eacute;e inconnu";
		break;
	case 1064:
		echo "Erreur de syntaxe dans la requ&ecirc;te";
		break;
	case 1065:
		echo "Requete vide";
		break;
	case 1062:
		echo "Enregistrement impossible -> donn&eacute;e d&eacute;j&agrave; ins&eacute;r&eacute;e";
		break;
	case 1054:
		echo "Erreur de la requ&ecirc;te table incompatible ".$res->error;
		break;
	case 2013:
		echo "Connexion perdue";
		break;
	case 1136:
		echo "Nombre de collone incompatible avec la table";
		break;
	default:
		$echo= "erreur inconnu   $error   $res->error";
		break;
	}
	if($echo!=""){
	$echo=str_replace(" ","_",$echo);
	
	header("Location:erreur_$echo.html");
	}
}




function sql_select_db($db,$id){
	$res=$id->select_db($db);
	if($res===false)
		die($this->sql_error($res));
	else
		$this->db=$db;
		
	
}
function sql_query($requete,$line,$fichier){
	$id=$this->sql_connect($this->host,$this->user,$this->pwd);
		$this->sql_select_db($this->db,$id);
		$this->cnx=$id;
	if($requete=="")
		die("<br/>fichier : $fichier : ligne $line<br/>Votre requete est vide");
	$res=$this->cnx->query($requete);
	if($res===false){
		return false;
		die($this->sql_error($res)."<br/>fichier : $fichier : ligne $line<br/>$requete");
	}
	else
		return $res;
	
	$this->close();
}
function sql_multi_query($requete,$line,$fichier){
	$id=$this->sql_connect($this->host,$this->user,$this->pwd);
		$this->sql_select_db($this->db,$id);
		$this->cnx=$id;
	if($requete=="")
		die("<br/>fichier : $fichier : ligne $line<br/>Votre requete est vide");
	$res=$this->cnx->multi_query($requete);
	if($res===false){
		return false;
		die($this->sql_error($res)."<br/>fichier : $fichier : ligne $line<br/>$requete");
	}
	else
		return $res;
	
	$this->close();
}
function sql_safe_query($requete){
	 $id=$this->sql_connect($this->host,$this->user,$this->pwd);
		$this->sql_select_db($this->db,$id);
		$this->cnx=$id;
	$res=$this->cnx->query($requete);
	
		return $res;
	
	$this->close();
}
function sql_fetch_object($res){
	if($res->num_rows>0){
		return $res->fetch_object();
	}
	else
		return false;
}
function sql_fetch_array($res){
	if(@$res->num_rows>0){
		return $res->fetch_array();
	}
	else
		return false;
}

function sql_num_rows($res){
	return $res->num_rows;
}

function sql_data_seek($res,$no){
	if($no>=0)
	$res->data_seek($no);
else
		die("<br/>la valeur de la fonction sql_data_seek ne peut etre negative");
	

}
function sql_field_seek($res,$no){
	if($no>=0)
		$res->field_seek($no);
	else{
		$pos=$this->sql_data_pos($res);
		$this->sql_data_seek($res,$pos+$no);
	//	die("<br/>la valeur de la fonction sql_field_seek ne peut etre negative");
	}

}
function sql_data_pos($res){
	$t=$this->sql_num_rows($res);
	$no=1;
	while($this->sql_fetch_object($res))
		$no++;
	$pos=$t-$no;
	$this->sql_data_seek($res,$pos+1);
	return $pos;
	
}
function __sleep(){
	$objet=array();
		foreach($this AS $key=>$value){
				$objet[$key]=$value;
		}
	return $objet;
}
function __wakeup(){
	
	
}
function __destruct(){
	@$this->sql_close($this->cnx);
	
}
}
?>
