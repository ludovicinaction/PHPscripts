<?php
       /*
         * *****************************
         * "BLOG" MODULE ADMINISTATION
         * *****************************
         */
        
            $oArticles = new Articles;
            // Get message with translation
            $aMsgPost = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_POSTS_ADMIN'); 
            
            //"BLOG" Menu => Viewing post management table
            if (isset($a) && 'modif' == $a) {
                
                // If no item is chosen then display management table
                if (!isset($id)) {

                    // Search All posts without pagination and for all category
                    $oArticles->ReadAllArticles('admin', $cat);

                    // Display form
                    if ('yes' != isset($eng) && ('modif' == isset($a))) {
                        include 'core/blog/views/admin-blog-articles.php';
                    }
                }

                //  *** POSTS MODIFICATION Mode *** 

                if (isset($id)) {

                    if (!isset($eng)) { // Does not display the form after having validated.
                        $oArticles->ReadOneArticle($id);
                        $aArticle = $oArticles->getPostData();
                        include 'core/blog/views/form-create-article.php';
                    }
   
                    // Save data post and request confirmation of registration after validation post
                    if ((isset($eng) && 'yes' == $eng) && (!isset($conf))) {
                        $oArticles->SaveArticlesData();
                        $btOk = 'admin.php?p=gest_art&a=modif&id=' . $id . '&eng=yes&conf=yes';
                        $oArticles->RequestConfirmation('modif', $aMsgPost[$lang]['msg_update_confirm'], $btOk, 'admin.php?p=gest_art&a=modif', $lang);
                    }

                    // If confirmation recording then save base.  
                    if (isset($conf) && 'yes' == $conf) {                
                       $bSauveOK = $oArticles->SaveArticle('modif', $aMsgPost[$lang]['msg_result_ok'], $aMsgPost[$lang]['msg_result_ko']);
                    }               
                }
            } // Post administration end
            elseif (isset($a) && 'lire' === $a) {

                // *** DISPLAY POST Mode ***

                $oArticles->ReadOneArticle($id);
                include 'core/blog/views/display-one-article.php';
                exit;
            } elseif (isset($a) && 'creer' == $a) {

                // *** CREATE POST MODE ***	

                if (!isset($eng)) {
                    include 'core/blog/views/form-create-article.php';
                }

                if (isset($eng) && (!isset($conf))) {
                    // Data backup only after validation form
                    $oArticles->SaveArticlesData();
                    $oArticles->RequestConfirmation('creer', $aMsgPost[$lang]['msg_creat_confirm'], 'admin.php?p=gest_art&a=creer&eng=yes&conf=yes', 'admin.php?p=gest_art&a=modif', $lang);
                } elseif (isset($eng) && isset($conf)) {
                    $bSauveOK = $oArticles->SaveArticle('creer', $aMsgPost[$lang]['msg_result_ok'], $aMsgPost[$lang]['msg_result_ko']);
                }
            } elseif (isset($a) && 'supp' == $a) {

                // 'DELETE POST' mode	
                if (!isset($conf)) {
					$sToken = $oSecure->create_token();
					
                    $btOk = 'admin.php?p=gest_art&a=supp&id=' . $id . '&conf=yes&token='.$_SESSION['token'];
                    $oArticles->RequestConfirmation('supp', $aMsgPost[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=gest_art&a=modif', $lang);
                } else {
                    $req = 'delete from blog_articles where id_art=' . $id;
                    $oAdmin->DeleteInformation($req, 'admin.php?p=gest_art&a=modif');
                }
            }
            
            // *** "Configuration" Submenu ***

            // Configuration form display
            elseif (isset($a) && 'config' === $a) {
                if (isset($c) && 'init' === $c){
                    $oArticles->ReadBlogConfig();  
                    include 'core/blog/views/form-setting-blog.php';
                }
                elseif (isset($c) && 'submit' === $c){
                    $oArticles->SaveConfig();
                    $oArticles->ReadBlogConfig();  
                }
            }   // config

            // *** Menu "Comments management" ***
            elseif (isset($a) && 'gest_com' === $a  ){
                $aMsgCmt = array();
                $aMsgCmt = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_COMMENTS_ADMIN'); 
                // Submenu initial
                if ( isset($c) && 'init' === $c ){
                    $aComm = $oArticles->ReadAllComments();
                    include 'core/blog/views/admin-blog-comments.php';
                // Delete comments
                }elseif ( isset($c) && 'delete' === $c ) {
                    if(!isset($valid)) {
                        $sToken = $oSecure->create_token();
                        $btOk = 'admin.php?p=gest_art&a=gest_com&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];
                        $oArticles->RequestConfirmation('supp', $aMsgCmt[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_com&c=init', $lang);
                    }
                    else{ // Delete confirm
                        if ($t == 'com') $req = 'delete from blog_comments where id_com=' . $id;
                        elseif ($t == 'rep') $req = 'delete from blog_reply where id_rep=' . $id;
                        $oAdmin->DeleteInformation($req, 'admin.php?p=gest_art&a=gest_com&c=init');
                    }        
                }
                // Display a comment
                elseif ( isset($c) && 'display' === $c){
                    $aComm =  $oArticles -> ReadOneComment($id, $t);
                    include 'core/blog/views/display-one-comments.php';
                }
                // Comments validation
                elseif (isset ($c) && 'valid' === $c){
                    $msg_email_ok = $aMsgCmt[$lang]['msg_cmt_email_ok'];
                    $msg_email_ko = $aMsgCmt[$lang]['msg_cmt_email_ko'];
                    if ( !isset($eng) ) $oArticles -> ConfirmValidateComments($id, $t, $v, $msg_email_ok, $msg_email_ko);
                    else $oArticles -> ValidateComment($id, $t, $aMsgCmt[$lang]['lib_resultOK'], $aMsgCmt[$lang]['lib_resultKO']);
                }
            } 
                
            // *** Submenu "Category admnistration" ***    
            elseif (isset($a) && 'gest_cat' === $a){
                $aMsg = array();
                $aMsg = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_CAT_ADMIN'); 
                $nom_cat = filter_input(INPUT_POST, 'nom_cat', FILTER_SANITIZE_STRING);
                if (isset($nom_cat)) $_SESSION['nom_cat'] = $nom_cat;

                if ( (!isset ($valid)) ) {
                    $aCat = $oArticles -> getCategoryData();
                    include 'core/blog/views/admin-blog-category.php';
                }   
                else{
                    if (isset($c) && 'update' === $c){
                        if ( isset($valid) && 'no' === $valid ){
                            $btOk = "admin.php?p=gest_art&a=gest_cat&c=update&valid=yes";
                            $oArticles->RequestConfirmation('modif', $aMsg[$lang]['msg_update_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init', $lang);                        
                        }
                        else $oArticles -> UpdateCategory();      
                    }
                    elseif (isset($c) && 'add' === $c){
                        if ( isset($valid) && 'no' === $valid ){
                            $btOk = "admin.php?p=gest_art&a=gest_cat&c=add&valid=yes";
                            $oArticles->RequestConfirmation('modif', $aMsg[$lang]['msg_creat_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init', $lang);
                        }
                        else $oArticles -> CreateCategory();
                    }
                    elseif (isset($c) && 'delete' === $c){
                        if ( isset ($valid) && 'no' === $valid ){
                            $sToken = $oSecure->create_token();
                            $btOk = 'admin.php?p=gest_art&a=gest_cat&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];        
                            $oArticles->RequestConfirmation('supp', $aMsg[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init', $lang);                            
                        }
                        else{
                            $sReq = 'delete from blog_cat_article where id_cat=' . $id;
                            $oAdmin -> DeleteInformation($sReq, 'admin.php?p=gest_art&a=gest_cat&c=init');
                        }
                    }                            
                }
            }  
