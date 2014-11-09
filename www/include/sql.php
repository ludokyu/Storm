<?php

class sql{
	
	function __construct($host,$user,$pwd,$db){
		//echo("$host $user $pwd");
		
		$this->db=$db;
		$this->user=$user;
		$this->pwd=$pwd;
		$this->host=$host;
		
		
	}
	function sql_affected_rows($res=false){
		if($res===false){
			if(@mysql_affected_rows())
				return @mysql_affected_rows();
			else
				return false;
			
		}
		else{
			if(@mysql_affected_rows($res))
				return @mysql_affected_rows($res);
			else
				return false;
			
			
		}
	
	}
	function sql_info($res=false){
		if($res===false){
			if(@mysql_info())
				return @mysql_info();
			else
				return false;
			
		}
		else{
			if(@mysql_info($res))
				return @mysql_info($res);
			else
				return false;
			
			
		}
	
	}
	function sql_stat($res=false){
		if($res===false){
			if(@mysql_stat())
				return @mysql_stat();
			else
				return false;
			
		}
		else{
			if(@mysql_stat($res))
				return @mysql_stat($res);
			else
				return false;
			
			
		}
	
	}
	function sql_list_processes($res=false){
		if($res===false){
			if(@mysql_list_processes())
				return @mysql_list_processes();
			else
				return false;
			
		}
		else{
			if(@mysql_list_processes($res))
				return @mysql_list_processes($res);
			else
				return false;
			
			
		}
	
	}
	function sql_change_user($user,$pwd,$database=null,$link_identifier=null){
		$fct="@mysql_change_user($user,$pwd";
		if(!is_null($database))
			$fct.=",$database";
		if(!is_null($link_identifier))
			$fct.=",$link_identifier";
		$fct.=")";
		
		if(!eval($fct))
			echo("<br>erreur dans le changement d'utilisateur ");
		
		else{
			$this->user=$user;
			$this->pwd=$pwd;
			$this->bd=$database;
		}	
			
			
		
		
	}
	function sql_client_encoding($line,$fichier,$link=false){
		
		if($link===false){
			if(@mysql_client_encoding())
				return @mysql_client_encoding();
			else
			echo("<br/>fichier : $fichier : ligne : $line<br/>");
			
		}
		else{
			if(@mysql_client_encoding($link))
				return @mysql_client_encoding($link);
			else
			echo("<br/>fichier : $fichier : ligne : $line<br/>");
			
			
		}
	}
	
	
	function sql_close($link=false){
		if($link===false){
			if(@mysql_close())
				return @mysql_close();
			else
				return false;
			
		}
		else{
			if(@mysql_close($link))
				return @mysql_close($link);
			else
			return false;
			
			
		}
		
	}
	
	
	function sql_connect($host,$user,$pwd){
	$id_connexion = mysql_connect ($host,$user,$pwd) ;
	if(!$id_connexion){
		echo($this->sql_error());
		//header("Location:erreur_".$this->sql_errno().".html");
	}
	else{
		$this->host=$host;
		$this->user=$user;
		$this->pwd=$pwd;
		return $id_connexion;
	}
}

function sql_errno(){
	return mysql_errno();
}

function sql_error($error=false){
	//echo mysql_error();
	if($error===false)
	$error=mysql_errno();
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
		echo "Erreur de la requ&ecirc;te table incompatible".mysql_error();
		break;
	case 2013:
		echo "Connexion perdue";
		break;
	case 1136:
		echo "Nombre de collone incompatible avec la table";
		break;
	case 1052:
		echo "Erreur : ".mysql_error();
		break;
	case 0: echo "Aucune erreur";
		break;
	default:
		echo "erreur inconnu : $error : ".mysql_error();
		break;
	}
}




function sql_select_db($db,$id){
	if(!@mysql_select_db($db,$id))
		echo($this->sql_error());
	else
		$this->db=$db;
}
function sql_query($requete,$line,$fichier){
	 $id=$this->sql_connect($this->host,$this->user,$this->pwd);
		$this->sql_select_db($this->db,$id);
		$this->cnx=$id;
	$res=mysql_query($requete);
	if($res===false){
		
		echo($this->sql_error()."<br/>fichier : $fichier : ligne $line<br/>$requete");
		return false;
	}
	else
		return $res;
	
	$this->close();
}
function sql_safe_query($requete){
	 $id=$this->sql_connect($this->host,$this->user,$this->pwd);
		$this->sql_select_db($this->db,$id);
		$this->cnx=$id;
	$res=mysql_query($requete);
	
		return $res;
	
	$this->close();
}
function sql_multi_query($requetes){
	echo "Cette fonction n'est pas disponible avec l'extension mysql";
}
function sql_fetch_object($res){
	if(@mysql_num_rows($res)>0){
		return @mysql_fetch_object($res);
	}
	else{
		$this->sql_free_result($res);
		return false;
	}
}
function sql_fetch_array($res){
	if(@mysql_num_rows($res)>0){
		return @mysql_fetch_array($res);
	}
	else{
		$this->sql_free_result($res);
		return false;
	}
}
function sql_last_id(){
	return @mysql_insert_id();
	
}

function sql_num_rows($res){
	return @mysql_num_rows($res);
}
function sql_data_seek($res,$no){
	if($no>=0)
	@mysql_data_seek($res,$no);
else
		echo("<br/>la valeur de la fonction sql_data_seek ne peut etre negative");
	

}

function sql_field_seek($res,$no){
	if($no>=0)
		@mysql_field_seek($res,$no);
else{
		$pos=$this->sql_data_pos($res);
		$this->sql_data_seek($res,$pos+$no);
	//	echo("<br/>la valeur de la fonction sql_field_seek ne peut etre negative");
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
function sql_free_result($res){
	
	@mysql_free_result($res);	
}
function __sleep(){
	$this->sql_close();	
}


}
?>
