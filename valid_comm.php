<?php header( 'content-type: text/html; charset=utf-8' ); ?>
<!DOCTYPE html>

<html>

    <head>

        <!-- En-tête de la page -->
		<link rel="stylesheet" href="css2/cssgeneral-s1.css">
        <meta charset="utf-8" />

        <title>Validation</title>

    </head>


    <body>

<?php
		require_once 'core/SPDO.class.php';
		require_once 'core/messageAlert.trait.php';
		require_once 'core/blog/classes/articles.class.php';
		require_once 'core/admin/admin.class.php';

		$oAdmin = new Admin();  
		$aLang = $oAdmin -> getSetting();
		$lang = $aLang['language'];

		$aMsg = $oAdmin->getItemTransation('BLOG', 'FRONT', $lang, 'MSG_VALID_EMAIL');

		$oArticles = new Articles;

		$t = filter_input(INPUT_GET, 't', FILTER_SANITIZE_URL);

		if (isset($_GET['com'])) {
			$id = filter_input(INPUT_GET, 'com', FILTER_SANITIZE_NUMBER_INT);
			$type = 'com';
		}
		elseif (isset($_GET['rep'])) {
			$id = filter_input(INPUT_GET, 'rep', FILTER_SANITIZE_NUMBER_INT);
			$type = 'rep';
		}	

		if ($type =='com') $sReq = "SELECT * FROM blog_comments WHERE id_com=$id";
		elseif ($type =='rep') $sReq = "SELECT * FROM blog_reply WHERE id_rep=$id"; 	

		//Recherche de la valeur du jeton
		$sRequete = SPDO::getInstance()->query($sReq);
		$aResult = $sRequete->fetch(PDO::FETCH_ASSOC);		 

		if ($aResult['jeton'] == $t) {

			//echo utf8_decode("<h2>Merci pour votre validation.<br />Votre commentaire sera validé dans les 48 heures.<br /> A bientôt<h2>");
			$oAdmin->DisplayResultRqt(TRUE, 'blog.php', $aMsg[$lang]['msg_valid_email'], '');

			$val_confirm = 1;
			if ($type == 'com') $sReq = "UPDATE blog_comments SET email_valid = :val WHERE id_com=$id";
			elseif ($type == 'rep') $sReq = "UPDATE blog_reply SET email_valid = :val WHERE id_rep=$id";

			$update = SPDO::getInstance()->prepare($sReq);
			$update -> bindValue(':val', $val_confirm);

			try {
				$result = $update->execute();
			} catch(PDOException $e){
				 echo $e->getMessage();
			}
			
		}
		else $oAdmin->DisplayResultRqt(FALSE, 'blog.php', '', $aMsg[$lang]['msg_notvalid_email']);
		//else utf8_decode("<h2>Désolé, votre mail n'a pas pu être validé.<br />Veuillez contacter le webmaster de ce site.<br /> A bientôt</h2>");
		?>

    </body>

</html>

		