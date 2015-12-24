<?php
    $iXs = (int) $oArticles->aff_xs;
    $iSm = (int) $oArticles->aff_sm;
    $iMd = (int) $oArticles->aff_md;
    $iLg = (int) $oArticles->aff_lg;
    $iArtPage = (int) $oArticles->art_page;
    $bCtrl = (bool) $oArticles->ctrl_comm;

    $mail_exp = htmlentities($oArticles->mail_exp, ENT_QUOTES, "UTF-8");
    $mail_obj = htmlentities($oArticles->mail_obj, ENT_QUOTES, "UTF-8");
    $mail_txt = htmlentities($oArticles->mail_txt, ENT_QUOTES, "UTF-8");

    $name_exp = htmlentities($oArticles->name_exp, ENT_QUOTES, "UTF-8");
    $name_reply = htmlentities($oArticles->name_reply, ENT_QUOTES, "UTF-8");
    $mail_reply = htmlentities($oArticles->mail_reply, ENT_QUOTES, "UTF-8");
   
    echo "<form class='form-horizontal' action='admin.php?p=gest_art&a=config&eng' method='post' class='col-md-16 col-lg-16 well'>";

     // Contrôle d'affichage.
     echo "<div class=margintop70></div>";
     echo "<legend>Configuration d'affichage des vignettes : </legend>";
        echo "<p class='help-block'>Configuration de la grille bootstrap pour chaque résolution:</p>";
        echo "<div class='form-group'>";

            echo "<label for='inputEmail' class='control-label col-xs-2'>Portable : </label>";
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
            echo "<label for='inputPassword' class='control-label col-xs-2'>Tablette : </label>";
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
            echo "<label for='inputPassword' class='control-label col-xs-2'>Ordinateur portable : </label>";
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
            echo "<label for='inputPassword' class='control-label col-xs-2'>Ordinateur de bureau : </label>";
            echo "<div class='col-xs-4'>";
                echo "<select name='aff_lg'>";
                for ($i=1 ; $i<13 ; $i++){
                    if ($iLg == $i) echo "<option value=$i selected> $iLg </option>";
                    else echo "<option value=$i> $i </option>";
                }
                echo "</select>";
                echo "<p class='help-block'>Nombre de colonnes attribués (au total 12) par ligne d'affichage.</p>";
            echo "</div>";
        echo "</div>";

    // Pagination    
    echo "<legend>Pagination : </legend>";
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Nombre d'article à afficher par page : </label>";
        echo "<div class='col-xs-1'>";
            echo "<input type='text' class='form-control input-xs' name='nbr_art' maxlength='2' value=$iArtPage required />";
        echo "</div>";    
    echo "</div>";
    
    //Gestion des commentaires O/N
    echo "<legend>Gestion des commentaires : </legend>";
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Contrôle des commentaires: </label>";
        echo "<label class='radio-inline'>";
            if ($bCtrl) echo "<input type='radio' name='ctrl_comm' id='opt_oui' value='1' checked>";
            else echo "<input type='radio' name='ctrl_comm' id='opt_oui' value='1'>";
            echo "Oui";
        echo "</label>";
        echo "<label class='radio-inline'>";
            if (!$bCtrl) echo "<input type='radio' name='ctrl_comm' id='opt_non' value='0' checked>";
            else echo "<input type='radio' name='ctrl_comm' id='opt_non' value='0'>";
            echo "Non";
        echo "</label>";
    echo "</div>";

    // configuration mail de controle commentaire
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Nom expéditeur : </label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='text' class='form-control input-md' name='name_exp' maxlength='50' value='$name_exp' />";
        echo "</div>"; 
    echo "</div>";
 
    //Mail expéditeur
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Adresse expéditeur : </label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='email' class='form-control input-md' name='mail_exp' maxlength='100' value='$mail_exp' />";
        echo "</div>"; 
    echo "</div>";
 
    //Nom de réponse
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Nom de réponse : </label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='text' class='form-control input-md' name='name_reply' maxlength='50' value='$name_reply' />";
        echo "</div>"; 
    echo "</div>";

    //email de réponse
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Adresse de réponses : </label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='email' class='form-control input-md' name='mail_reply' maxlength='100' value='$mail_reply' />";
        echo "</div>"; 
    echo "</div>";
           
    // Objet de l'email
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Objet: </label>";
        echo "<div class='col-xs-3'>";
            echo "<input type='text' class='form-control input-md' name='mail_obj' maxlength='70' value='$mail_obj' />";
        echo "</div>"; 
    echo "</div>";

    // Texte de l'email
    echo "<div class='form-group'>"; 
        echo "<label class='control-label col-xs-2'>Contenu: </label>";
        echo "<div class='col-xs-4'>";
            echo "<textarea name='mail_texte' maxlength='3000' rows='7' class='form-control'>$mail_txt</textarea>";
        echo "</div>";
    echo "</div>";        

    //Bouton submit
    echo "<div class='form-group'>"; 
        echo "<div class='col-xs-2'>";
        echo "</div>";
        echo "<div class='col-xs-4'>";
            echo "<button class='btn btn-primary' type='submit'>Sauvegarder</button>";            
        echo "</div>";    
    echo "</div>";    
  echo "</form>";  
