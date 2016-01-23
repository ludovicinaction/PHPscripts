<?php

//Items Translation
$aItems = $oAdmin->getItemTransation('BLOG', 'FRONT', $lang, 'HOME');
$sAllOption = $aItems[$lang]['txt_opt_cat']; // 'All' text option

if (isset($_GET['cat'])) $val_cat = filter_input(INPUT_GET, 'cat', FILTER_SANITIZE_NUMBER_INT);
if (!isset($val_cat)) $val_cat=0;

$val_cat = (int) $val_cat;

/*
        if (isset($_POST['cat'])) echo "<br>POST : {$_POST['cat']}<br>";
        elseif (isset($_GET['cat'])) echo "<br>GET : {$_GET['cat']}<br>";
        var_dump($val_cat);
        echo "<br> blog.php?cat=$val_cat";
*/


echo "<form method='get' class='margintop70' action='blog.php?cat=$val_cat' />";

echo "<div class='form-group'>";
    echo "<div class='col-xs-12 col-sm-6 col-md-6 col-lg-3'>";
        echo "<label for='sel1'> {$aItems[$lang]['lib_form_cat']} </label>";
        echo "<select class='form-control' name='cat' maxlength='30'>";
            echo "<option value='0'> {$aItems[$lang]['txt_opt_cat']} </option>";

                foreach ($alistCat as $ligne=>$val){
                    $id_cat = (int) $val['id_cat'];        
                    
                    if ($val_cat === $id_cat) {
                        if ($val_cat === 0 ) echo '<option value=\'' . $id_cat . '\' selected>' . $sAllOption  . '</option>';
                        else echo '<option value=\'' . $id_cat . '\' selected>' . $val['nom_cat'] . '</option>';
                    }    
                    else echo '<option value=\'' . $id_cat . '\'>' . $val['nom_cat'] . '</option>';

                }
            echo "</select>";
 
        echo "</div>";

    echo "</div>";	
    echo "<div class='col-xs-12 col-sm-6 col-md-6 col-lg-3'>";	
        echo "<label for='sel2'>&nbsp</label> <br />";
        echo "<button class='btn btn-primary' type='submit'> {$aItems[$lang]['lib_btt_submit']} </button>";
    echo "</div>";		

echo "</form>";	