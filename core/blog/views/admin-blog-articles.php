<?php
/**
 * Affichage HTML du formulaire BACK-END. Gestion des articles
 * @package BLOG
 * @category Vue du module "Blog"
 */

$aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'SUBMENU_POST_ADMIN');

$aPostData = $oArticles->getPostData();

// Formulaire de tri
echo "<div class='margintop70'></div>";

echo "<div class='row'>";

echo "<form method='post' class='margintop70' action='admin.php?p=gest_art&a=modif&tri' />";
	echo "<div class='form-group'>";
		echo "<div class='col-xs-12 col-sm-6 col-md-3 col-lg-3'>";
			echo "<label for='sel1'>{$aItems[$lang]['lib_cat_filter']}</label>";
			echo "<select class='form-control' name='cat' maxlength='30'>";
			echo "<option value='0'> {$aItems[$lang]['lib_select_cat']} </option>";
			foreach ($alistCat as $ligne=>$val){
				echo '<option value=\''.$val['id_cat'].'\'>' . $val['nom_cat'] . '</option>';
			}
			echo "</select>";

		echo "</div>";
		echo "<div class='col-xs-12 col-sm-3 col-md-2 col-lg-2 form-group'>";
			echo "<label type='texte'>{$aItems[$lang]['lib_input_startdate']}</label>";
			echo "<input type='text' name='datedebut' maxlength='10' class='form-control'>";
			echo "<label type='texte'>{$aItems[$lang]['lib_enddate']}</label>";
			echo "<input type='text' name='datefin' maxlength='10' class='form-control'>";
		echo "</div>";
	echo "</div>";

echo "</div>";

echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-6 col-md-1 col-lg-1'>";
	echo "<button class='btn btn-primary' type='submit'>{$aItems[$lang]['lib_btt_post_filter']}</button>";
	echo "</div>";

echo "</div>";	
echo "</form>";

echo "<br>";

if (count($aPostData) == 0){
	echo "Il n'y a aucun article enregistré.";
} 
else{

	// Affichage du tableau de gestion des articles
	echo "<div class='row'>";
		echo "<article class='col-sm-10 col-md-10 col-lg-10'>";	
			echo "<table class='table table-bordered table-striped table-condensed'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th>"; echo "{$aItems[$lang]['lib_title']}"; echo "</th>";
						echo "<th>"; echo "{$aItems[$lang]['lib_creation_date']}"; echo "</th>";						
						echo "<th>"; echo "{$aItems[$lang]['lib_publication_date']}"; echo "</th>";						
						echo "<th>"; echo "Actions"; echo "</th>";
					echo "</tr>";
				echo "</thead>";

		//Champs de tri de recherche			
		foreach ($aPostData as $ligne=>$val){
			if ($lang == 'FR'){
				$dDateCrea = utf8_encode($val['date_crea_art']);
				$dDatePub = utf8_encode($val['date_pub_art']);
			}
			elseif ($lang == 'EN') {             
				$dDateCrea = $val['date_crea_art'];
				$dDatePub = $val['date_pub_art'];	
			}	

			echo "<tr>";
				echo "<td>"; echo $val['titre_art']; echo "</td>";
				echo "<td>"; echo $dDateCrea; echo "</td>";
				echo "<td>"; echo $dDatePub;  echo "</td>";
				echo "<td>";
					$id_art = $val['id_art'];
					echo "<a href='admin.php?p=gest_art&a=lire&id=$id_art' target='_blank' class='btn-xs btn btn-success'><span class='glyphicon glyphicon-eye-open'></span> </a>";
					echo " ";
					echo "<a href='admin.php?p=gest_art&a=modif&id=$id_art' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span> </a>";
					echo " ";
					echo "<a href='admin.php?p=gest_art&a=supp&id=$id_art' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span> </a>";
				echo "</td>";	
			echo "</tr>";
		}
			echo "</table>";
		echo "</article>";
	echo "</div>";
}	