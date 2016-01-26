<?php
if (!isset($p_lang)) $p_lang = $lang;

$aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $p_lang, 'HOME');
echo "<form role='form' action='admin.php' method='post'>";
//echo "<div class='margintop70'></div>"; 


echo "<div class='row'>";
	echo "<legend>{$aItems[$p_lang]['label_select_lang']} </legend>";
	echo "<div class='form-group'>";
		echo "<label for='inputPassword' class='control-label col-xs-2'>{$aItems[$p_lang]['lib_tab_lang']}</label>";
	

    	echo "<div class='col-xs-2'>";
        	echo "<select class='form-control' name='opt-lang'>";
 				if ($p_lang == 'EN') {
					echo "<option value='EN' selected>English</option>";
					echo "<option value='FR'>French</option>";
				}
				elseif($p_lang == 'FR') {
					echo "<option value='FR' selected>Français</option>";
					echo "<option value='EN'>Anglais</option>";
				}
            echo "</select>";

		echo "</div>"; 
	echo "</div>";
echo "</div>";

echo "<br>";
echo "<div class='row'>";
	 echo "<legend>{$aItems[$p_lang]['label_website_adr']}</legend>";
	 echo "<div class='form-group'>";
	 	echo "<label class='control-label col-xs-2'>Host</label>";
	    echo "<div class='col-xs-2'>";
	    	echo "<input type='text' class='form-control input-xs' name='hostname' maxlength='50' value=$website />";
	    echo "</div>";
	echo "</div>";
echo "</div>";

// SMTP send mail
echo "<div class='row'>";
	echo "<div class='form-group'>";
	    echo "<label class='control-label col-xs-2'>SMTP</label>";
	    echo "<div class='col-xs-2'>";
	    	echo "<input type='text' class='form-control input-xs' name='smtp' maxlength='20' value=$smtp />";
	    echo "</div>";
	echo "</div>"; 
echo "</div>";

// Port send mail
echo "<div class='row'>";
	echo "<div class='form-group'>";
	    echo "<label class='control-label col-xs-2'>Port</label>";
	    echo "<div class='col-xs-2'>";
	    	echo "<input type='text' class='form-control input-xs' name='port' maxlength='3' value=$port />";
	    echo "</div>";
	echo "</div>"; 
echo "</div>";

//Send mail adresse
echo "<div class='row'>";
	echo "<div class='form-group'>";
	    echo "<label class='control-label col-xs-2'>Send mail</label>";
	    echo "<div class='col-xs-2'>";
	    	echo "<input type='email' class='form-control input-xs' name='sendmail' maxlength='100' value=$email_send />";
	    echo "</div>";
	echo "</div>"; 
echo "</div>";


    //Bouton submit
echo "<div class='row'>";
echo "<br>";
    echo "<div class='form-group'>"; 
        echo "<div class='col-xs-2'>";
        
        echo "</div>";
        echo "<div class='col-xs-4'>";
            echo "<button class='btn btn-primary' type='submit'>{$aItems[$p_lang]['lib_btt_update']}</button>";            
        echo "</div>";    
    echo "</div>";
echo "</div>";
echo "<input type='hidden' name='val_update' value='true'>"; 

echo "</form>";
/*
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
*/


/*
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
*/