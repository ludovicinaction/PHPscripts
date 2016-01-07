<?php

//Items Translation
$aItems = $oAdmin->getItemTransation('BLOG', 'FRONT', $lang, 'HOME');

    if (isset($_POST['cat'])) {
        $val_cat = (int) $_POST['cat'];
        echo "<form method='post' class='margintop70' action='blog.php?&tri&cat=$val_cat' />";
    }
    else echo "<form method='post' class='margintop70' action='blog.php' />";
    
        echo "<div class='form-group'>";
            echo "<div class='col-xs-12 col-sm-6 col-md-6 col-lg-3'>";
                    echo "<label for='sel1'> {$aItems[$lang]['lib_form_cat']} </label>";
                    echo "<select class='form-control' name='cat' maxlength='30'>";
                        echo "<option value='0'> {$aItems[$lang]['txt_opt_cat']} </option>";
                            foreach ($alistCat as $ligne=>$val){
                                if (!isset($_POST['cat'])) echo '<option value=\''.$val['id_cat'].'\'>' . $val['nom_cat'] . '</option>';
                                else {
                                    if ($_POST['cat'] == $val['id_cat']) echo '<option value=\''.$val['id_cat'].'\' selected>' . $val['nom_cat'] . '</option>';
                                    else echo '<option value=\''.$val['id_cat'].'\'>' . $val['nom_cat'] . '</option>';
                                }
                            }
                    echo "</select>";

            echo "</div>";

        echo "</div>";	
        echo "<div class='col-xs-12 col-sm-6 col-md-6 col-lg-3'>";	
            echo "<label for='sel2'>&nbsp</label> <br />";
            echo "<button class='btn btn-primary' type='submit'> {$aItems[$lang]['lib_btt_submit']} </button>";
        echo "</div>";		

    echo "</form>";	