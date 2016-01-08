<?php
$aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'SUBMENU_CAT_ADM');

if (isset ($_SESSION['id_cat']) ) $id_cat=$_SESSION['id_cat'];

if (isset($c) && 'update' === $c) echo "<form method='post' class='margintop70' action='admin.php?p=gest_art&a=gest_cat&c=update&valid=no' />";
elseif (isset($c) && 'add' === $c) echo "<form method='post' class='margintop70' action='admin.php?p=gest_art&a=gest_cat&c=add&valid=no' />";

if (count($aCat) == 0){
	echo "Il n'existe aucune catégorie";
}
else{

	echo "<div class='row'>";
	echo "<article class='col-xs-12 col-sm-10 col-md-8 col-lg-4'>";	
		echo "<table class='table table-bordered table-striped table-condensed'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>"; echo $aItems[$lang]['title_cat_name']; echo "</th>";
					echo "<th>"; echo "Actions"; echo "</th>";
				echo "</tr>";
			echo "</thead>";



	foreach ($aCat as $valcat){
			echo "<tr>";
				$nom_cat = $valcat['nom_cat'];
				$id_cat = $valcat['id_cat'];					
				
				echo "<td>"; 
					if ( isset ($c) && 'update' === $c && $id == $id_cat){
						$_SESSION['id_cat'] = $id_cat;
						echo "<input type='text' name='nom_cat' maxlength='30' value='$nom_cat' class='form-control'>";
					}
					else echo $nom_cat;
				echo "</td>";

				echo "<td>"; 
					echo "<a href='admin.php?p=gest_art&a=gest_cat&c=update&id=$id_cat' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span> </a>";		
					echo "  ";
					echo "<a href='admin.php?p=gest_art&a=gest_cat&c=delete&id=$id_cat&valid=no' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span> </a>";
				echo "</td>";
			echo "</tr>";	
	}
		echo "<tr>";
			echo "<td>"; 
				if ( isset($c) && 'add' === $c) echo "<input type='text' name='nom_cat' maxlength='30' value='' class='form-control'>";
				else echo " ";   
			echo "</td>";  
			echo "<td>"; 
				echo "<a href='admin.php?p=gest_art&a=gest_cat&c=add' class='btn btn-xs btn-success'><span class='glyphicon glyphicon-plus'></span> </a>";   		
			echo "</td>";  
		echo "</tr>";

		echo "</table>";	
			if ( isset ($c) && 'update' === $c) echo "<button class='btn btn-primary' type='submit'>{$aItems[$lang]['lib_edit_btt']}</button>";
			elseif ( isset($c) && 'add' === $c)	echo "<button class='btn btn-primary' type='submit'>{$aItems[$lang]['lib_create_btt']}</button>";
			

	echo "</form>";

	echo "</article>";	
	echo "</div>";
}	