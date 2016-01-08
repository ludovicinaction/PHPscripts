<?php

$sLibCreateButton = $aItems[$lang]['lib_btt_create'];
$sLibEditButton = $aItems[$lang]['lib_btt_edit'];

$aSelectType = $oAdmin->selectDistinctOptionSelect('type');




if (isset ($_SESSION['id_trans']) ) $id_trans=$_SESSION['id_trans'];


if (count($aTrans) == 0){
	echo "No translation found";
}
else{

	echo "<div class='row'>";
		echo "<article class='col-xs-12 col-sm-10 col-md-8 col-lg-10'>";	
			echo "<table class='table table-hover table-striped table-condensed '>";
				echo "<thead>";
					echo "<tr>";
						echo "<th>"; echo "{$aItems[$lang]['lib_tab_name']}"; echo "</th>";
						echo "<th>"; echo "{$aItems[$lang]['lib_tab_lang']}"; echo "</th>";
						echo "<th>"; echo "Office"; echo "</th>";
						echo "<th>"; echo "Type"; echo "</th>";
						echo "<th>"; echo "Designation"; echo "</th>";
						echo "<th>"; echo "{$aItems[$lang]['lib_tab_trans']}"; echo "</th>";
						echo "<th>"; echo "Actions"; echo "</th>";
					echo "</tr>";
				echo "</thead>";


			//SEARCH LINE
			echo "<form method='post' action='admin.php?p=trans&a=adm_trans&c=search' />";
			echo "<tr style='background-color:#0099CC'>";
				//Module choice
				$lib_all = $aItems[$lang]['lib_all_select'];
				echo "<td>"; 
					echo "<select class='form-control' name='search_module'>";
					if ($search_module == 'ALL') echo "<option value='ALL' selected>$lib_all</option>";
					else echo "<option value='ALL'>$lib_all</option>";
					if ($search_module == 'BLOG') echo "<option value='BLOG' selected>BLOG</option>";						
					else echo "<option value='BLOG'>BLOG</option>";					
					echo "</select>";						
				echo "</td>";  
				//Language choice
				echo "<td>"; 
					echo "<select class='form-control' name='search_lang'>";
					if ($search_lang == 'ALL') echo "<option value='ALL' selected>$lib_all</option>";
					else echo "<option value='ALL'>$lib_all</option>";
					if ($search_lang == 'EN') echo "<option value='EN' selected>EN</option>"; 
					else echo "<option value='EN'>EN</option>"; 
					if ($search_lang == 'FR') echo "<option value='FR' selected>FR</option>"; 
					else echo "<option value='FR'>FR</option>";		
				echo "</td>"; 
				//Front or back-office
				echo "<td>"; 
					echo "<select class='form-control' name='search_office'>";
					if ($search_office == 'ALL') echo "<option value='ALL' selected>$lib_all</option>";
					else echo "<option value='ALL'>$lib_all</option>";
					if ($search_office == 'BACK') echo "<option value='BACK' selected>BACK</option>";
					else echo "<option value='BACK'>BACK</option>";
					if ($search_module == 'FRONT')  echo "<option value='FRONT' selected>FRONT</option>";
					else echo "<option value='FRONT'>FRONT</option>";
					echo "</select>";						
				echo "</td>"; 
				//Type choice
				echo "<td>"; 
					echo "<select class='form-control' name='search_type'>";
					echo "<option value='ALL'>$lib_all</option>";
					foreach ($aSelectType as $value) {
						$valtype = $value['type'];
						if ($search_type == $valtype) echo "<option value='$valtype' selected>$valtype</option>";
						else echo "<option value='$valtype'>$valtype</option>";
					}
					echo "</select>";	
					echo " ";   
				echo "</td>"; 
				// Description choice
				echo "<td>"; 
					echo " ";   
				echo "</td>"; 
				// Translation choice
				echo "<td>"; 
					echo " ";   
				echo "</td>"; 
				//search button	
				echo "<td>"; 
					//echo "<a href='admin.php?p=trans&a=adm_trans&c=search' class='btn btn-xs btn-info'><span class='glyphicon glyphicon-search'></span> </a>";
					echo "<button class='btn btn-info btn-xs' type='submit'><span class='glyphicon glyphicon-search'></span></button>";
				echo "</form>";	
				echo "</td>";  
			echo "</tr>";			


			if (isset($c) && 'update' === $c) echo "<form method='post' action='admin.php?p=trans&a=adm_trans&c=update&valid=no' />";
			elseif (isset($c) && 'add' === $c) echo "<form method='post' action='admin.php?p=trans&a=adm_trans&c=add&valid=no' />";


			//ADD LINE ( ADD CHOICE )
			echo "<tr>";
				//Module choice
				echo "<td>"; 
					if ( isset($c) && 'add' === $c) {
						echo "<select class='form-control' name='module'>";
						echo "<option value='BLOG'>BLOG</option>";
						echo "</select>";	
					}	
					else echo " ";   
				echo "</td>";  
				//Language choice
				echo "<td>"; 
					if ( isset($c) && 'add' === $c) {
						echo "<select class='form-control' name='lang'>";
						echo "<option value='EN'>EN</option>";
						echo "<option value='FR'>FR</option>";
						echo "</select>";	
					}	
					else echo " ";   
				echo "</td>"; 
				//Front or back-office
				echo "<td>"; 
					if ( isset($c) && 'add' === $c) {
						echo "<select class='form-control' name='office'>";
						echo "<option value='BACK'>BACK</option>";
						echo "<option value='FRONT'>FRONT</option>";
						echo "</select>";	
					}	
					else echo " ";   
				echo "</td>"; 
				//Type choice
				echo "<td>"; 
					if ( isset($c) && 'add' === $c) {
						echo "<input type='text' name='type' maxlength='20' value='' class='form-control'>";
					}	
					else echo " ";   
				echo "</td>"; 
				// Description choice
				echo "<td>"; 
					if ( isset($c) && 'add' === $c) {
						echo "<input type='text' name='desc' maxlength='25' value='' class='form-control'>";
					}	
					else echo " ";   
				echo "</td>"; 
				// Translation choice
				echo "<td>"; 
					if ( isset($c) && 'add' === $c) {
						echo "<input type='text' name='translation' maxlength='200' value='' class='form-control'>";
					}	
					else echo " ";   
				echo "</td>"; 
				//add button	
				echo "<td>"; 
					echo "<a href='admin.php?p=trans&a=adm_trans&c=add' class='btn btn-xs btn-success'><span class='glyphicon glyphicon-plus'></span> </a>";   		
				echo "</td>";  
			echo "</tr>";


			if ( isset ($c) && ($c ==='update' or $c==='add') ){
				echo "<tr>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					
					echo "<td>";
						if ( isset($c) && 'add' === $c)	echo "<button class='btn btn-primary' type='submit'>$sLibCreateButton</button>";						
					echo "</td>";	

				echo "</tr>";
			}	


		$i = 1;	
		foreach ($aTrans as $trans) {
			$id_trans = $trans['id'];
			$module_name = $trans['module'];
			$lang = $trans['lang'];
			$office = $trans['office'];
			$type = $trans['type'];
			$desc = $trans['description'];
			$trans = $trans['texte'];

			// Update line ( update choice )
			echo "<tr id=$i>";
				echo "<td>"; 
				//Module choice
				if ( isset ($c) && 'update' === $c && $id == $id_trans){
					echo "<select class='form-control' name='module'>";
						if ($module_name == 'BLOG') {
							echo "<option selected value='BLOG'>BLOG</option>";
							echo "<option value='module2'>module 2</option>"; // For test
							echo "<option value='module3'>module 3</option>";
						}
						elseif ($module_name == 'module2') { 
							echo "<option selected>module 2</option>";
							echo "<option>module 3</option>";
							echo "<option>BLOG</option>";
						}	
					echo '</select>';
				}else echo $module_name;
					
				echo "</td>";
				// Language choice
				echo "<td>";
					if ( isset ($c) && 'update' === $c && $id == $id_trans){				
						echo "<select class='form-control' name='lang'>";
						if ($lang == 'FR') {
							echo "<option selected value='FR'>FR</option>";
							echo "<option value='EN'>EN</option>";
						}
						elseif ($lang == 'EN') {
							echo "<option selected value='EN'>EN</option>";
							echo "<option value='FR'>FR</option>";
						}	
						echo '</select>';
					}else echo $lang;	
				echo "</td>";
				//Front or back-office choice
				echo "<td>"; 
					if ( isset ($c) && 'update' === $c && $id == $id_trans){
						echo "<select class='form-control' name='office'>";
						if ($office == 'FRONT'){
							echo "<option selected value='FRONT'>FRONT</option>";
							echo "<option value='BACK'>BACK</option>";
						}
						elseif($office == 'BACK'){
							echo "<option selected value='BACK'>BACK</option>";
							echo "<option value='FRONT'>FRONT</option>";
						}
						echo "</select>";
					}else echo $office;	
				echo "</td>";
				// Type Choice
				echo "<td>"; 
					if ( isset ($c) && 'update' === $c && $id == $id_trans){
						$_SESSION['id_trans'] = $id_trans;
						echo "<input type='text' name='type' maxlength='20' value='$type' class='form-control'>";
					}
					else echo $type;				
				echo "</td>";			
				// Description choice
				echo "<td>"; 
					if ( isset ($c) && 'update' === $c && $id == $id_trans){
						echo "<input type='text' name='desc' maxlength='25' value='$desc' class='form-control'>";
					}
					else echo $desc;				
				echo "</td>";
				// Translation choice
				echo "<td>"; 
					if ( isset ($c) && 'update' === $c && $id == $id_trans){
						echo "<input type='text' name='translation' maxlength='200' value='$trans' class='form-control'>";
					}
					else echo $trans;			

				echo "</td>";
				echo "<td>"; 
					$focus = $i - 2;
					//update button
	
					echo "<a href='admin.php?p=trans&a=adm_trans&c=update&id=$id_trans#$focus' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-pencil'></span> </a>";		
					
					echo "  ";
					//delete button
					echo "<a href='admin.php?p=trans&a=adm_trans&c=delete&id=$id_trans&valid=no' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span> </a>";
				echo "</td>";
			echo "</tr>";


			//Submit Update button
			if ( isset ($c) && 'update' === $c && $id_trans == $id ){
				echo "<tr>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					echo "<td>"; echo "</td>";
					
					echo "<td>";
						if ( isset($c) && 'update' === $c)	echo "<button class='btn btn-primary' type='submit'>$sLibEditButton</button>";						
					echo "</td>";	

				echo "</tr>";
			}	
			$i += 1;			
		}

		
			echo "</table>";

		echo "</form>";
		echo "</article>";	
	echo "</div>";			

}	