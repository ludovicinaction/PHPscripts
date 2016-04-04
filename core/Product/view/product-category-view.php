<?php

		if (isset($c) && 'update' === $c) echo "<form method='post' class='margintop70' action='admin.php?p=product&a=create_cat&c=update&valid=no&id=$id' />";
		elseif (isset($c) && 'add' === $c) echo "<form method='post' class='margintop70' action='admin.php?p=product&a=create_cat&c=add&valid=no' />";
	
if (count($aCat) == 0) echo 'No product categories';

		echo "<div class='row'>";
		echo "<article class='col-xs-12 col-sm-10 col-md-8 col-lg-4'>";	
		echo "<table class='table table-bordered table-striped table-condensed'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>"; echo "Categories"; echo "</th>";
					echo "<th>"; echo "Actions"; echo "</th>";
				echo "</tr>";
			echo "</thead>";

		foreach ($aCat as $CatData) {
				$iCat = $CatData['id_cat'];
				$sNameCat = $CatData['cat_name'];
				echo "<tr>";
					echo "<td>";
					echo $CatData['cat_name'];

					if ( isset ($c) && 'update' === $c && $id == $iCat){
						echo "<input type='text' name='name_cat' maxlength='30' value='$sNameCat' class='form-control'>";
					}


					echo "</td>";

					echo "<td>";
						echo "<a href='admin.php?p=product&a=create_cat&c=update&id=$iCat' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span> </a>";		
						echo "  ";
						echo "<a href='admin.php?p=product&a=create_cat&c=delete&id=$iCat&valid=no' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span> </a>";
					echo "</td>";
				echo "</tr>";
			}	
		echo "<tr>";
			echo "<td>"; 
				if ( isset($c) && 'add' === $c) echo "<input type='text' name='name_cat' maxlength='30' value='' class='form-control'>";
				else echo " ";   
			echo "</td>";  
			echo "<td>"; 
				echo "<a href='admin.php?p=product&a=create_cat&c=add' class='btn btn-xs btn-success'><span class='glyphicon glyphicon-plus'></span> </a>";   		
			echo "</td>";  
		echo "</tr>";

		echo "</table>";	
			if ( isset ($c) && 'update' === $c) echo "<button class='btn btn-primary' type='submit'>Edit</button>";
			elseif ( isset($c) && 'add' === $c)	echo "<button class='btn btn-primary' type='submit'>Create</button>";





		echo "</article>";
		echo "</div>";	


