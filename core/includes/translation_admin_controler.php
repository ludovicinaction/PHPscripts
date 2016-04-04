<?php
  
  //Filtering POST values  
  $search_module = filter_input(INPUT_POST, 'search_module', FILTER_SANITIZE_STRING);
  $search_lang   = filter_input(INPUT_POST, 'search_lang', FILTER_SANITIZE_STRING);
  $search_office = filter_input(INPUT_POST, 'search_office', FILTER_SANITIZE_STRING);
  $search_type   = filter_input(INPUT_POST, 'search_type', FILTER_SANITIZE_STRING);

  $c2            = filter_input(INPUT_GET, 'c2', FILTER_SANITIZE_STRING);
  $sGetMod       = filter_input(INPUT_GET, 'mod', FILTER_SANITIZE_STRING);
  $sGetLang      = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
  $sGetOffice    = filter_input(INPUT_GET, 'office', FILTER_SANITIZE_STRING);
  $sGetType      = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

 //  *** TRANSLATION MENU ***
 $aMsg = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_TRANS'); 
 
 if ( (!isset ($valid)) ) {

     if (isset($c) && 'search' === $c OR $c2==='update'){
         if ($c2 === 'update') $aTrans = $oAdmin->getSearchTranslations($sGetMod, $sGetLang, $sGetOffice, $sGetType); 
         else $aTrans = $oAdmin->getSearchTranslations($search_module, $search_lang, $search_office, $search_type);
     }
     else{
         $aTrans = $oAdmin->getAllTranslations();
     }
   
     include 'core/admin/view/adm-translation.php';
 }
 else{
     $oAdmin->filterPostValues();

     if (isset($c) && 'update' === $c){                    
         if ( isset($valid) && 'no' === $valid ){
             $btOk = "admin.php?p=trans&a=adm_trans&c=update&valid=yes";
             $oAdmin->RequestConfirmation('modif', $aMsg[$lang]['msg_update_confirm'], $btOk, 'admin.php?p=trans&a=adm_trans&c=init', $lang);
         }
         else $oAdmin -> UpdateTranslation();      
     }
     elseif (isset($c) && 'add' === $c){
         if ( isset($valid) && 'no' === $valid ){
             $btOk = "admin.php?p=trans&a=adm_trans&c=add&valid=yes";
             $oAdmin->RequestConfirmation('modif', $aMsg[$lang]['msg_creat_confirm'], $btOk, 'admin.php?p=trans&a=adm_trans&c=init', $lang );
         }
         else{
             $oAdmin->CreateTranslation();
         }    
     }
     elseif (isset($c) && 'delete' === $c) {
         $sReq = 'delete from adm_translation where id=' . $id;
         if ( isset ($valid) && 'no' === $valid ){
             $sToken = $oSecure->create_token();
             $btOk = 'admin.php?p=trans&a=adm_trans&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];  
             $oAdmin->RequestConfirmation('supp',  $aMsg[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=transt&a=adm_trans&c=init', $lang);
         }
         else{    
             $oAdmin-> DeleteInformation($sReq, 'admin.php?p=trans&a=adm_trans&c=init');
         }    

     }    

 }
