<?php

 $aConfigValues = $oArticles ->getConfigValues();   

    $iXs = (int) $aConfigValues['aff_xs'];
    $iSm = (int) $aConfigValues['aff_sm'];
    $iMd = (int) $aConfigValues['aff_md'];
    $iLg = (int) $aConfigValues['aff_lg'];
    $iArtPage = (int) $aConfigValues['art_page'];
    $bCtrl = (bool) $aConfigValues['ctrl_comm'];

    $mail_exp = htmlentities($aConfigValues['mail_exp'], ENT_QUOTES, "UTF-8");
    $mail_obj = htmlentities($aConfigValues['mail_obj'], ENT_QUOTES, "UTF-8");
    $mail_txt = htmlentities($aConfigValues['mail_txt'], ENT_QUOTES, "UTF-8");

    $name_exp = htmlentities($aConfigValues['name_exp'], ENT_QUOTES, "UTF-8");
    $name_reply = htmlentities($aConfigValues['name_reply'], ENT_QUOTES, "UTF-8");
    $mail_reply = htmlentities($aConfigValues['mail_reply'], ENT_QUOTES, "UTF-8");
   
    $aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'SUBMENU_SET');

    echo "<form class='form-horizontal' action='admin.php?p=gest_art&a=config&c=submit' method='post' class='col-md-16 col-lg-16 well'>";

     // Display control
     echo "<div class=margintop70></div>";

     echo "<legend>{$aItems[$lang]['lib_title_display_thum']} </legend>";
        echo "<p class='help-block'>{$aItems[$lang]['cmt_display_thum']}</p>";
        echo "<div class='form-group'>";

            echo "<label for='inputEmail' class='control-label col-xs-2'>{$aItems[$lang]['lib_input_cellphone']}</label>";
            echo "<div class='col-xs-1'>";
                echo "<select name='aff_xs'>";
                    for ($i=1 ; $i<13 ; $i++){
                        if ($iXs == $i) echo "<option value=$i selected> $iXs </option>";
                        else echo "<option value=$i> $i </option>";
                    }
                echo "</select>";
            echo "</div>";

        echo "</div>";

        echo "<div class='form-group'>";
            echo "<label for='inputPassword' class='control-label col-xs-2'>{$aItems[$lang]['lib_input_tablet']}</label>";
            echo "<div class='col-xs-1'>";
                echo "<select name='aff_sm'>";
                for ($i=1 ; $i<13 ; $i++){
                    if ($iSm == $i) echo "<option value=$i selected> $iSm </option>";
                    else echo "<option value=$i> $i </option>";
                }
                echo "</select>";
            echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
            echo "<label for='inputPassword' class='control-label col-xs-2'>{$aItems[$lang]['lib_input_laptop']}</label>";
            echo "<div class='col-xs-1'>";
                echo "<select name='aff_md'>";
                for ($i=1 ; $i<13 ; $i++){
                    if ($iMd == $i) echo "<option value=$i selected> $iMd </option>";
                    else echo "<option value=$i> $i </option>";
                }
                echo "</select>";
            echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
            echo "<label for='inputPassword' class='control-label col-xs-2'>{$aItems[$lang]['lib_input_desktop']}</label>";
            echo "<div class='col-xs-6'>";
                echo "<select name='aff_lg'>";
                for ($i=1 ; $i<13 ; $i++){
                    if ($iLg == $i) echo "<option value=$i selected> $iLg </option>";
                    else echo "<option value=$i> $i </option>";
                }
                echo "</select>";
                echo "<p class='help-block'>{$aItems[$lang]['cmt_display_grid']}</p>";
            echo "</div>";
        echo "</div>";

    // Pagination    
    echo "<legend>{$aItems[$lang]['lib_paging']}</legend>";
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>{$aItems[$lang]['lib_input_nbrpostperpage']}</label>";
        echo "<div class='col-xs-1'>";
            echo "<input type='text' class='form-control input-xs' name='nbr_art' maxlength='2' value=$iArtPage required />";
        echo "</div>";    
    echo "</div>";
    
    // Comments management Yes/No
    echo "<legend>{$aItems[$lang]['lib_title_mail_manag']}</legend>";
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>{$aItems[$lang]['lib_check_comments']}</label>";
        echo "<label class='radio-inline'>";
            if ($bCtrl) echo "<input type='radio' name='ctrl_comm' id='opt_oui' value='1' checked>";
            else echo "<input type='radio' name='ctrl_comm' id='opt_oui' value='1'>";
            echo "{$aItems[$lang]['lib_yes']}";
        echo "</label>";
        echo "<label class='radio-inline'>";
            if (!$bCtrl) echo "<input type='radio' name='ctrl_comm' id='opt_non' value='0' checked>";
            else echo "<input type='radio' name='ctrl_comm' id='opt_non' value='0'>";
            echo "{$aItems[$lang]['lib_no']}";
        echo "</label>";
    echo "</div>";

    // Email controle configuration 
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>{$aItems[$lang]['lib_input_sendermail']}</label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='text' class='form-control input-md' name='name_exp' maxlength='50' value='$name_exp' />";
        echo "</div>"; 
    echo "</div>";
 
    // Email exp
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>{$aItems[$lang]['lib_input_returnadr']}</label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='email' class='form-control input-md' name='mail_exp' maxlength='100' value='$mail_exp' />";
        echo "</div>"; 
    echo "</div>";
           
    // Email object
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>{$aItems[$lang]['lib_object']}</label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='text' class='form-control input-md' name='mail_obj' maxlength='70' value='$mail_obj' />";
        echo "</div>"; 
    echo "</div>";

    // Email Text
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>{$aItems[$lang]['lib_contents']}</label>";
        echo "<div class='col-xs-4'>";
            echo "<textarea name='mail_texte' maxlength='3000' rows='7' class='form-control'>$mail_txt</textarea>";
        echo "<p class='help-block'>{$aItems[$lang]['cmt_email_txt']}</p>";
        echo "</div>";

    echo "</div>";        

    // Submit button
    echo "<div class='form-group'>"; 
        echo "<div class='col-xs-2'>";
        echo "</div>";
        echo "<div class='col-xs-4'>";
            echo "<button class='btn btn-primary' type='submit'>{$aItems[$lang]['lib_btt_save']}</button>";            
        echo "</div>";    
    echo "</div>";    
  echo "</form>";  
