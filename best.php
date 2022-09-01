<?php
include("utils/connect.php");
$db = database_connect();

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

include ("header.php");
?>

<div id="formulaire">
	Mon bout'chou s'appellera...&nbsp;&nbsp;
	<form action="best.php" method="post" enctype="multipart/form-data" style="display:inline;">
		<span class="guillemet">&#8220;&nbsp;</span>
		<input type="text" style="width:200px; height:24px; border:1px solid #ccc;" name="prenom" id="prenom" maxlength="30" />
		<span class="guillemet">&nbsp;&#8221;</span>&nbsp;&nbsp;&nbsp;
		<input type="submit" name="submit" value="Soumettre" id="submit" class="bouton"/>
	</form>
	
	<?php
	// AJOUT D'UN NOUVEAU PRENOM
	if(isset($_POST['prenom']) && $_POST['prenom']!='') {
		$prenom = $_POST['prenom'];
		$prenom = addslashes(strip_tags($prenom));
		$query = $db->prepare("SELECT id FROM prenoms WHERE prenom = '$prenom'"); // requete pour vérifier si le prénom est pas déjà dans la bdd
		$query->execute();
		$count = $query->rowCount();
		if($count >= 1){ // si le prenom est déjà en BDD
			echo '<div style="color:red;">Ce prénom a déjà été inventé !</div>';
		}
		else { 	// c'est bon le prénom n'est pas déjà dans la bdd
			$ip = $_SERVER["REMOTE_ADDR"];
			$query = $db->prepare("SELECT COUNT(id) FROM prenoms WHERE id>(SELECT MAX(id) FROM prenoms)-4 AND ip='$ip'"); // compte le nombre d'enregistrements de la même IP dont l'id est plus grand que le 4e dernier enregistrement
			$query->execute();
			$nb = $query->fetch();
			if ($nb[0]<=2) { //si il y en 2 ou moins, on poste
				$ip = $_SERVER["REMOTE_ADDR"];
				$query = $db->prepare("INSERT INTO prenoms (prenom,auteur,ip) VALUES(?,'Anonyme',?)");
				$query->execute([$prenom,$ip]);
				header('location:recents.php');
			}
			else {   //si la même IP a déjà posté 3 fois de suite
				echo '<center><span style="color:red;">Vous avez déjà posté 3 prénoms à la suite !<br/>Attendez que d\'autres prénoms soient postés !</span></center>';
			}
		}
	}
	?>

</div>

<div id="menu">
	<span><img src="style/ours.png" style="position:relative;top:5px;" alt="" />&nbsp;&nbsp;&nbsp;Top des prénoms cons</span>
	<a href="http://www.unprenomconpourlavie.com/recents.html"><img src="style/biberon.png" style="position:relative;" alt="" />&nbsp;&nbsp;&nbsp;Derniers prénoms cons</a>
</div>

<div id="contenu">

	<?php
		// AFFICHAGE DES PRENOMS
		$nb_prenoms_page = 10;
		$page = 1;
		if(isset($_GET['page']) && $_GET['page']!='') {
			$page = addslashes($_GET['page']);
		}
		$debut = ($page-1)*$nb_prenoms_page;
		// echo $nb_prenoms_page.' prénoms à partir du '.$debut.'ème';		
		$query = $db->prepare("SELECT SQL_CALC_FOUND_ROWS id,prenom,time,like1,unlike1,ranking FROM prenoms ORDER BY ranking DESC LIMIT $debut,$nb_prenoms_page");
		$query->execute();
		$sql_result = $query->fetchAll();
		echo'<ol id="liste_entrees">';
		foreach ($sql_result as $row) {
			$id = $row[0];
			$prenom = stripslashes($row[1]);
			$timestamp = strtotime($row[2]);
			$like1 = $row[3];
			$unlike1 = $row[4];					
			echo ('<li class="entree">');
			echo ('<div class="entree1ligne">&#8220;&nbsp;<span class="prenom">'.$prenom.'</span>&nbsp;&#8221;</div>');
			echo ('<div class="entree2ligne">né(e) le '.strftime ('%e %B %G', $timestamp).' &#183; <span id="'.$id.'" class="lien like">j\'appelerais bien mon gosse comme ça ('.$like1.')</span> &#183; <span id="'.$id.'" class="lien unlike">j\'appelerais pas mon gosse comme ça  ('.$unlike1.')</span></div></li>');					
		}
		echo'</ol>';
	?>
	
	<div id="pages">
	
	<?php
		// LISTE DES PAGES
		if ($page > 1) {
			echo '<a href="best-'.($page-1).'.html" class="lien">prénoms cons précédents</a> - ';
		}
		
		$query = $db->prepare("SELECT FOUND_ROWS() AS NbRows"); // fait une deuxième requête sql plus simple pour connaitre le nb total de statuts grace à SQL_CALC_FOUND_ROWS
		$query->execute();
		$sql_result = $query->fetch();
		$nb_total_pages = round($sql_result[0] /10);
		$nb_start = $page-5; // liste à partir de la 5e précédente par rapport à l'actuelle				
		if ($nb_start<1) { 
			$nb_start=1; //sauf si la page actuelle est <5
		}
		$nb = $nb_start;
		
		while($nb < $page) {					
			echo ('<a href="best-'.$nb.'.html" class="lien">'.$nb.'</a> - '); // toutes les pages précédentes
			$nb++;
		}
		if ($nb == $page) {
			echo ('<span style="color:black;font-weight:900;">'.$nb.'</span> - '); // la page actuelle
			$nb++;
		}
		while(($nb <= $nb_total_pages) && ($nb <= $nb_start+9)) {					
			echo ('<a href="best-'.$nb.'.html" class="lien">'.$nb.'</a> - '); // les pages d'après : celles inférieures au nombre total de pages et pas plus de 10 en tout
			$nb++;
		}
		
		if ($page < $nb_total_pages) {
			echo '<a href="best-'.($page+1).'.html" class="lien">prénoms suivants</a>';
		}
	?>
	
	</div>

</div>

<script type="text/javascript" >
$(function() {
	$(".like").click(function()
	{
		var id = $(this).attr("id");
		var dataString = 'id='+ id;
		$.ajax({
		type: "POST",
		url: "ajax_like.php",
		data: dataString,
		cache: false,
		success: function(rep){
			$(".like").filter("#"+id).html('j\'appelerais bien mon gosse comme ça ('+rep+')');		
			}
		});
	});
});
</script>

<script type="text/javascript" >
$(function() {
	$(".unlike").click(function()
	{
		var id = $(this).attr("id");
		var dataString = 'id='+ id;
		$.ajax({
		type: "POST",
		url: "ajax_unlike.php",
		data: dataString,
		cache: false,
		success: function(rep){
			$(".unlike").filter("#"+id).html('j\'appelerais pas mon gosse comme ça ('+rep+')');
			}
		});
	});
});
</script>

</div>

</body>

</html>