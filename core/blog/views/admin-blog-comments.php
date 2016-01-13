<?php
/**
 * Affichage HTML du formulaire BACK-END. Gestion des commentaires
 * @package BLOG
 * @category Vue du module "Blog"
 */

$aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'SUBMENU_CMT_ADMIN');

if (count($aComm) == 0) {
    echo "Il n'y a aucun commentaire.";
    
}
else{
    echo "<div class='margintop70'></div>";
    echo "<div class='row'>";
        echo "<article class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>"; 
            echo "<table class='table table-hover'>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th>"; echo "{$aItems[$lang]['lib_title_name']}"; echo "</th>";
                        echo "<th>"; echo "Date"; echo "</th>";
                        echo "<th>"; echo "{$aItems[$lang]['lib_name_title']}"; echo "</th>";                        
                        echo "<th>"; echo "Mail"; echo "</th>";
                        echo "<th>"; echo "{$aItems[$lang]['lib_contents_title']}"; echo "</th>";
                        echo "<th>"; echo "Email<br />OK"; echo "</th>";
                        echo "<th>"; echo "Publication<br />OK"; echo "</th>";
                        echo "<th>"; echo "Actions"; echo "</th>";
                    echo "</tr>";
                echo "</thead>";       

        foreach ($aComm as $ligne=>$val)
        {
            $id_art = $val['id_art'];
            
            $oArticles->ReadOneArticle($id_art);
            $aArticle = $oArticles->getPostData();

            $titre_art = $aArticle['art']['titre_art'];

            echo "<tr>";

                echo "<td>"; echo $titre_art; echo "</td>";
                echo "<td>"; echo $val['date_com'];     echo "</td>";
                echo "<td>"; echo $val['nom_com'];      echo "</td>";
                echo "<td>"; echo $val['email_com'];    echo "</td>";

                //  on affiche uniquement les 350 premiers caractères entier du commentaire.                                                 
                $texte_com = $val['texte_com'];
                if (strlen($texte_com) > 350 ) {
                    $pos=mb_strpos($texte_com, ' ', 350);                 
                    echo "<td>"; echo substr($texte_com, 0, $pos ) . '...'; echo "</td>";
                }
                else {
                    echo "<td>"; echo $val['texte_com']; echo "</td>";
                }

                //Colonne "Email OK"
                if ($val['email_valid'] == 0) { echo "<td>"; echo "<span class='glyphicon glyphicon-remove-circle text-danger'></span>"; echo "</td>"; }   
                else { echo "<td>"; echo "<span class='glyphicon glyphicon-ok-circle text-success'></span>"; echo "</td>"; }  

                //Colonne "Publication"
                if ($val['valid_com'] == 1) {echo "<td>"; echo "<span class='glyphicon glyphicon-ok-circle text-success'></span>"; echo "</td>";}
                else {echo "<td>"; echo "<span class='glyphicon glyphicon-remove-circle text-danger'></span>"; echo "</td>";}

                // Action
                echo "<td>";
                    $id_com = $val['id_com'];
                    $mail_ok = $val['email_valid'];
                    echo "<a href='admin.php?p=gest_art&a=gest_com&id=$id_com&t=com&c=display' target='_blank' class='btn-xs btn btn-success'><span class='glyphicon glyphicon-eye-open'></span> </a>";
                    echo " ";
                    echo "<a href='admin.php?p=gest_art&a=gest_com&id=$id_com&t=com&c=valid&v=$mail_ok' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon glyphicon-ok'></span> </a>";
                    echo " ";
                    echo "<a href='admin.php?p=gest_art&a=xxxx' class='btn btn-xs btn-warning'><span class='glyphicon glyphicon glyphicon glyphicon-remove'></span> </a>";
                    echo " ";                
                    echo "<a href='admin.php?p=gest_art&a=gest_com&id=$id_com&t=com&c=delete' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span> </a>";
                echo "</td>";   
            echo "</tr>";
            //echo '</tbody>';

            // affichage des réponses du commentaire
            $pReponses = $oArticles->ReadAnswers($val['id_com'], 'admin');
        
            $aReponses = $pReponses->fetchAll(PDO::FETCH_ASSOC);

            //On trie les données par rapport à la clé "Ref_rep"
            $aRepTrie = $oArticles->multi_sort($aReponses, $key = 'ref_rep');

            //Affichage réponses    
            foreach($aRepTrie as $repval)
            {   
                
                echo "<tr class='bg-success'>";
                    echo "<td>"; echo $titre_art; echo "</td>";
                    echo "<td>"; echo $repval['date_rep']; echo "</td>";
                    echo "<td>"; echo $repval['nom_rep']; echo "</td>";
                    echo "<td>"; echo $repval['email_rep']; echo "</td>";
                   //  on affiche uniquement les 350 permiers caractères entier de la réponse.                       
                        $texte_rep = $repval['texte_rep'];
                        if (strlen($texte_rep) > 350 ) {
                            $pos=mb_strpos($texte_rep, ' ', 350); 
                            echo "<td>"; echo substr($texte_rep, 0, $pos ) . '...'; echo "</td>";
                        }
                        else {
                            echo "<td>"; echo $texte_rep; echo "</td>";            
                        }
                                
                    //Colonne "Email OK"
                    if ($repval['email_valid'] == 0) { echo "<td>"; echo "<span class='glyphicon glyphicon-remove-circle text-danger'></span>"; echo "</td>"; }   
                    else { echo "<td>"; echo "<span class='glyphicon glyphicon-ok-circle text-success'></span>"; echo "</td>"; }      

                    //Colonne "Publication"
                    if ($repval['valid_rep'] == 1) {echo "<td>"; echo "<span class='glyphicon glyphicon-ok-circle text-success'></span>"; echo "</td>";}
                    else  { echo "<td>"; echo "<span class='glyphicon glyphicon-remove-circle text-danger'></span>"; echo "</td>";}

                    echo "<td>";
                        $id_rep = $repval['id_rep'];
                        $mail_ok = $repval['email_valid'];
                        echo "<a href='admin.php?p=gest_art&a=gest_com&id=$id_rep&t=rep&c=display' target='_blank' class='btn-xs btn btn-success'><span class='glyphicon glyphicon-eye-open'></span> </a>";
                        echo " ";
                        echo "<a href='admin.php?p=gest_art&a=gest_com&id=$id_rep&t=rep&c=valid&v=$mail_ok' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon glyphicon-ok'></span> </a>";
                        echo " ";
                        echo "<a href='admin.php?p=gest_art&a=xxxx' class='btn btn-xs btn-warning'><span class='glyphicon glyphicon glyphicon glyphicon-remove'></span> </a>";
                        echo " ";                
                        echo "<a href='admin.php?p=gest_art&a=gest_com&id=$id_rep&t=rep&c=delete' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span> </a>";
                    echo "</td>";   
                echo "</tr>";
               //echo '</tbody>';
            }    

        }

            echo "</table>";
        echo "</article>";
    echo "</div>";

} //fin du else