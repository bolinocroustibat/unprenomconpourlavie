<?php
include("utils/connect.php");
$db = database_connect();
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

$id = $_POST['id'];
$ip = $_SERVER["REMOTE_ADDR"];
$query = $db->prepare("SELECT like1,unlike1 FROM prenoms WHERE id = ?"); //requete pour choper le nombre de likes et d'unlikes déjà présents
$query->execute([$id]);
$sql_result = $query->fetch();
$like_count = $sql_result['like1'];
$unlike_count = $sql_result['unlike1'];
$query = $db->prepare("SELECT COUNT(id) FROM prenoms_votants WHERE id_prenom = ? AND ip = ?"); //requete pour savoir si cette IP a déjà voté
$query->execute([$id,$ip]);
$sql_result = $query->fetch();
$nb = $sql_result[0];
if ($nb==0) { // si l'IP a jamais voté
	$query = $db->prepare("INSERT INTO prenoms_votants (ip,id_prenom) VALUES(?,?)");
	$query->execute([$ip,$id]);
	$unlike_count = $unlike_count + 1;
	$ranking = $like_count-$unlike_count;
	$query = $db->prepare("UPDATE prenoms SET unlike1 = ?, ranking = ? WHERE id = ?");
	$query->execute([$unlike_count,$ranking,$id]);
	
}
echo ($unlike_count);
?>