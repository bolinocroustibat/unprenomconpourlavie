<?php
include("outils/connex.php");
$db = database_connect();

$id = $_POST['id'];
$ip = $_SERVER["REMOTE_ADDR"];
$req = mysql_query("SELECT like1,unlike1 FROM prenoms WHERE id = '$id'"); //requete pour choper le nombre de likes et d'unlikes déjà présents
$rep = mysql_fetch_row($req);
$like_count = $rep[0];
$unlike_count = $rep[1];
$nb = mysql_query("SELECT COUNT(id) FROM prenoms_votants WHERE id_prenom = '$id' AND ip = '$ip'"); //requete pour savoir si cette IP a déjà voté
$nb = mysql_fetch_row($nb);
$nb = $nb[0];
if ($nb==0) { // si l'IP a jamais voté
	mysql_query("INSERT INTO prenoms_votants (id,ip,id_prenom) VALUES('','$ip','$id')");
	$unlike_count = $unlike_count + 1;
	$ranking = $like_count-$unlike_count;
	mysql_query("UPDATE prenoms SET unlike1 = '$unlike_count', ranking = '$ranking' WHERE id = '$id'");
}
echo ($unlike_count);
?>