<?php
include("outils/connex.php");
database_connect();
mysql_query("SET NAMES UTF8");

include('outils/convert_date.php');

include ("header.php");
?>

<div id="formulaire">
	Mon bout'chou s'appellera...&nbsp;&nbsp;
	<form action="" method="post" style="display:inline;">
		<span class="guillemet">&#8220;&nbsp;</span>
		<input type="text" style="width:200px; height:24px; border:1px solid #ccc;" name="submitprenom" id="submitprenom" maxlength="30" />
		<span class="guillemet">&nbsp;&#8221;</span>&nbsp;&nbsp;&nbsp;</span>
		<input type="submit" name="submit" value="Soumettre" id="submit" class="bouton"/>
	</form>
</div>

<div id="menu">
	<a href="http://www.unprenomconpourlavie.com/best.html"><img src="style/ours.png" border="0" style="position:relative;top:5px;" alt="" />&nbsp;&nbsp;&nbsp;Top des prénoms cons</a>
	<span><img src="style/biberon.png" border="0" alt="" />&nbsp;&nbsp;&nbsp;Derniers prénoms cons</span>
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
		$req = mysql_query("SELECT SQL_CALC_FOUND_ROWS id,prenom,time,like1,unlike1,ranking FROM prenoms ORDER BY time DESC LIMIT $debut,$nb_prenoms_page");
		echo'<ol id="liste_entrees">';
		while($rep = mysql_fetch_row($req)) {
			$id = $rep[0];	
			$prenom = stripslashes($rep[1]);
			$time = $rep[2];
			$like1 = $rep[3];
			$unlike1 = $rep[4];
			echo ('<li class="entree">');
			echo ('<div class="entree1ligne">&#8220;&nbsp;<span class="prenom">'.$prenom.'</span>&nbsp;&#8221;</div>');
			echo ('<div class="entree2ligne">né(e) le '.convert_date($time,'grand').' &#183; <span id="'.$id.'" class="lien like">j\'appelerais bien mon gosse comme ça ('.$like1.')</span> &#183; <span id="'.$id.'" class="lien unlike">j\'appelerais pas mon gosse comme ça  ('.$unlike1.')</span></div></li>');							
		}
		echo'</ol>';
	?>
			
	<div id="pages">
	
	<?php
		// LISTE DES PAGES
		if ($page > 1) {
			echo '<a href="recents-'.($page-1).'.html" class="lien">prénoms cons précédents</a> - ';
		}
	
		$req = mysql_query("SELECT FOUND_ROWS() AS NbRows"); // fait une deuxième requête sql plus simple pour connaitre le nb total de statuts grace Ã  SQL_CALC_FOUND_ROWS
		$rep = mysql_fetch_row($req);
		$nb_total_pages = round($rep[0] /10);
		$nb_start = $page-5; // liste à  partir de la 5e précédente par rapport à l'actuelle				
		if ($nb_start<1) { 
			$nb_start=1; //sauf si la page actuelle est <5
		}
		$nb = $nb_start;
		
		while($nb < $page) {					
			echo ('<a href="recents-'.$nb.'.html" class="lien">'.$nb.'</a> - '); // toutes les pages précédentes
			$nb++;
		}
		if ($nb == $page) {
			echo ('<span style="color:black;font-weight:900;">'.$nb.'</span> - '); // la page actuelle
			$nb++;
		}
		while(($nb <= $nb_total_pages) && ($nb <= $nb_start+9)) {					
			echo ('<a href="recents-'.$nb.'.html" class="lien">'.$nb.'</a> - ');// les pages d'après : celles inférieures au nombre total de pages et pas plus de 10 en tout
			$nb++;
		}
		
		if ($page < $nb_total_pages) {
			echo '<a href="recents-'.($page+1).'.html" class="lien">prénoms cons suivants</a>';
		}
	?>
	
	</div>

</div>

<script type="text/javascript" >
$(function() {
	$("#submit").click(function()
	{
		var prenom = $("#submitprenom").val();
		var dataString = 'prenom='+ prenom;
		if(prenom=='')
		{
			alert('Inventez d\'abord un prénom con avant de cliquer !');
		}
		else
		{
			$.ajax({
			type: "POST",
			url: "ajax_recents.php",
			data: dataString,
			cache: false,
			success: function(html){
				$("ol#liste_entrees li:last").fadeOut("slow");
				$(html).insertBefore("ol#liste_entrees li:first");
				$("ol#liste_entrees li:first").fadeIn("slow");
				/*$("ol#update").append(html);*/
				}
			});
		}return false;
	});
});
</script>

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

</body>

</html>