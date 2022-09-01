<?php
include("utils/connect.php");
$db = database_connect();
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

// AJOUT D'UN NOUVEAU PRENOM
$prenom = $_POST['prenom'];
$query = $db->prepare("SELECT COUNT(*) FROM prenoms WHERE prenom = ?"); // vérifie si le prénom est pas déjà dans la bdd
$query->execute([$prenom]);
$sql_result = $query->fetch();
if($sql_result[0] == 1){ // si le prenom est déjà en bdd
	echo '<div style="color:red;">Ce prénom a déjà été inventé !</div>';
}
else { 	// c'est bon le prénom n'est pas déjà dans la bdd
	$ip = $_SERVER["REMOTE_ADDR"];
	$query = $db->prepare("SELECT COUNT(id) FROM prenoms WHERE id>(SELECT MAX(id) FROM prenoms)-4 AND ip=?"); // compte le nombre d'enregistrements de la même IP dont l'id est plus grand que le 4e dernier enregistrement
	$query->execute([$ip]);
	$sql_result = $query->fetch();
	if ($sql_result[0]<=2) { //si il y en 2 ou moins, on poste
		$query = $db->prepare("INSERT INTO prenoms (prenom,auteur,ip) VALUES(?,'Anonyme',?)");
		$query->execute([$prenom,$ip]);
		$query = $db->prepare("SELECT id,time FROM prenoms WHERE prenom = ?");
		$query->execute([$prenom]);
		$id = $rep[0];
		$timestamp = strtotime($row[1]);
		?>
		<li class="entree">
			<div class="entree1ligne">&#147;&nbsp;<span class="prenom"><?php echo $prenom; ?></span>&nbsp;&#148;</div>
			<div class="entree2ligne">né le <?php echo strftime ('%e %B %G', $timestamp); ?> &#183; <span id="<?php echo $id;?>" class="lien like">j'appelerais bien mon gosse comme ça (0)</span> &#183; <span id="<?php echo $id;?>" class="lien unlike">j'appelerais pas mon gosse comme ça (0)</span></div>				
		</li>
	<?php
	}
	else {   //si la même IP a déjà posté 3 fois de suite
		echo '<center><span style="color:red;">Vous avez déjà posté 3 prénoms à la suite !<br/>Attendez que d\'autres prénoms soient postés !</span></center>';
	}
}
?>