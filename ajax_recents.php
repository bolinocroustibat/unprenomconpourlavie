<?php
include("outils/connex.php");
database_connect();
mysql_query("SET NAMES UTF8");

include("outils/convert_date.php");

// AJOUT D'UN NOUVEAU PRENOM
$prenom = $_POST['prenom'];
$prenom = mysql_real_escape_string($prenom);
$query = mysql_query("SELECT id FROM prenoms WHERE prenom = '$prenom'"); // vérifie si le prénom est pas déjà dans la bdd
if(mysql_num_rows($query) == 1){ // si le prenom est déjà en bdd
	echo '<div style="color:red;">Ce prénom a déjà été inventé !</div>';
}
else { 	// c'est bon le prénom n'est pas déjà dans la bdd
	$ip = $_SERVER["REMOTE_ADDR"];
	$nb = mysql_query("SELECT COUNT(id) FROM prenoms WHERE id>(SELECT MAX(id) FROM prenoms)-4 AND ip='$ip'"); // compte le nombre d'enregistrements de la même IP dont l'id est plus grand que le 4e dernier enregistrement
	$nb = mysql_fetch_row($nb);
	$nb = $nb[0];
	if ($nb<=2) { //si il y en 2 ou moins, on poste
		$ip = $_SERVER["REMOTE_ADDR"];
		$time = time();	
		mysql_query("INSERT INTO prenoms (id,prenom,auteur,time,like1,unlike1,ranking,ip) VALUES('','$prenom','Anonyme','$time','0','0','0','$ip')");
		$req = mysql_query("SELECT id FROM prenoms WHERE prenom = '$prenom'");
		$rep = mysql_num_rows($req);
		$id = $rep[0];
		?>
		<li class="entree">
			<div class="entree1ligne">&#147;&nbsp;<span class="prenom"><?php echo $prenom; ?></span>&nbsp;&#148;</div>
			<div class="entree2ligne">né le <?php echo convert_date($time,'grand'); ?> &#183; <span id="<?php echo $id;?>" class="lien like">j'appelerais bien mon gosse comme ça (0)</span> &#183; <span id="<?php echo $id;?>" class="lien unlike">j'appelerais pas mon gosse comme ça (0)</span></div>				
		</li>
	<?php
	}
	else {   //si la même IP a déjà posté 3 fois de suite
		echo '<center><span style="color:red;">Vous avez déjà posté 3 prénoms à la suite !<br/>Attendez que d\'autres prénoms soient postés !</span></center>';
	}
}
?>