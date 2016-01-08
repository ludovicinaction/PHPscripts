<?php
/**
 * Trait "MessageAlert".
 * Methods for displaying bootstrap alert message ("success, "warning" or "danger")
 * @package COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr> 
 */
trait MessageAlert{
	
	/**
	  * Displays an alert message with a confirmation button
      *
	  * @param string $typeAlert success, info, warning ou danger
	  * @param string $titreAlert Title alert
	  * @param string $MsgAlert Alert message
	  * @lienBtn strinf $lienBtn url link to continue
	  */
	public function DisplayAlert($typeAlert, $titreAlert, $MsgAlert, $lienBtn){	

		$lang = Admin::$lang;

		if ($lang == 'FR') {
			$txt = "Continuer";
			if ($titreAlert == '') $titreAlert = 'Enregistrement effectué avec succés';
		}	
		elseif ($lang='EN') 
			{
				$txt = "Continue";
				if ($titreAlert == '') $titreAlert = 'Recording successfully completed';
			}	


		echo "<div class='margintop70'></div>";
		echo "<div class='text-center alert alert-$typeAlert fade in'>";
		echo "<h4> $titreAlert </h4>";
		echo "<a class='btn btn-$typeAlert' data-dismiss='alert' href='$lienBtn'>$txt</a>";
		echo "<p>$MsgAlert</p>";
		echo "</div>";	
	}	
	
	/**
	 * Viewing a confirmation request with two two buttons (OK or Cancel)
	 * The display is managed by a bootstrap alert: colored according to levels 'warning', 'success' or 'alert'
	 *
	 * @param $action string 'warning', 'success' ou 'alert' 
	 * @param $titreAlert string Alert title
	 * @param $linkBtnOk string Ok url link
	 * @param $linkBtnAnnule string Cancel url link
	 */
	 public function RequestConfirmation($action, $titleAlert, $linkBtnOk, $linkCancelBtn, $lang){
		 $MsgAlert = ''; //optionnel

		 if ($lang == 'FR'){
		 	$confBttTxt = 'Confirmer';
		 	$cancelBttTxt = 'Annuler';	
		 }elseif($lang='ENG'){
		 	$confBttTxt = 'Confirm';
		 	$cancelBttTxt = 'Cancel';			 	
		 }

		 switch ($action){
			 case 'modif';
				$typeAlert = 'warning'; break;	
			 case 'creer';
				$typeAlert = 'success';	break;
			 case 'supp';
				$typeAlert = 'danger'; break;
		 }
		echo "<div class='margintop70'></div>";
		echo "<div class='text-center alert alert-$typeAlert fade in'>";
			echo "<h4> $titleAlert </h4>";
			echo "<a class='btn btn-$typeAlert' data-dismiss='alert' href='$linkCancelBtn'>$cancelBttTxt</a>";
			echo "  ";
			echo "<a class='btn btn-$typeAlert' data-dismiss='alert' href='$linkBtnOk'>$confBttTxt</a>";
			echo "<p>$MsgAlert</p>";
		echo "</div>";		 
	 }



	 /**
	 * Viewing query results by alert via bootstrap method "DisplayAlert"
	 *
	 * @param string $bSauveOK = 'success' ou 'danger'
	 * @param string $lienBrn HTML link to continue.
     */ 
	 public function DisplayResultRqt($bSauveOK, $lienBtn, $successTitle='', $dangerTitle='')
	 {
		$MsgAlert = '';
	
		if ($bSauveOK) {
			$typeAlert = 'success';
			$titreAlert = $successTitle;
		}
		else {
			$typeAlert = 'danger';
			$titreAlert = $dangerTitle;
		}
		$this -> DisplayAlert($typeAlert, $titreAlert, $MsgAlert, $lienBtn);		 
	 }




	
}

