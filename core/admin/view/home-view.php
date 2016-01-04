<?php
if (!isset($p_lang)) $p_lang = $lang;

$aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $p_lang, 'HOME');

echo "<div class='margintop70'></div>"; 

echo "<div class='row'>";
	echo "<div class='col-sm-1 col-md-4 col-lg-4'></div>";

    echo "<div class='col-sm-1 col-md-1 col-lg-1'>";
		echo "<center><img src='img/flag_english.png'></center>";
	echo "</div>";

	echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
		echo "<div class='col-sm-1 col-md-1 col-lg-1'>";
        	echo "<center><img src='img/flag_france.png'></center>";
        echo "</div>";
	echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";

	echo "<div class='col-sm-1 col-md-4 col-lg-4'></div>";  
echo "</div>";


echo "<div class='row'>";
	echo "<div class='col-sm-1 col-md-4 col-lg-4'></div>";
		echo "<div class='col-sm-1 col-md-1 col-lg-1'>";
			echo "<form role='form' action='admin.php' method='post'>";
            echo "<label class='radio-inline'>";
            if ($p_lang == 'EN') echo "<input class='text-center' type='radio' name='opt-lang' value='EN' checked='checked'>English</label>";
            else echo "<input class='text-center' type='radio' name='opt-lang' value='EN'>English</label>";
		echo "</div>";
		echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
			echo "<div class='col-sm-1 col-md-1 col-lg-1'>";
			echo "<label class='radio-inline'>";
			if ($p_lang == 'FR') echo "<input type='radio' name='opt-lang' value='FR' checked='checked'>Français</label>";
			else echo "<input type='radio' name='opt-lang' value='FR'>Français</label>";
		echo "</div>";             
		echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
		echo "<div class='col-sm-1 col-md-4 col-lg-4'></div>";  
	echo "</div>"; 
	echo "<input type='hidden' name='val_update' value='true'>";            

echo "<div class='margintop70'></div>"; 
	echo "<div class='row'>";
		echo "<div class='col-sm-12 col-md-12 col-lg-12 text-center'>";
		echo "<button class='btn btn-primary' type='submit'> {$aItems[$p_lang]['lib_btt_update']} </button>";   
	echo "</div>"; 
echo "</div>"; 

echo "</form>";   