<?php
/**
 * Trait "MessageAlert".
 * Methodes d'affichage de message d'alert de type bootstrap ("success, "warning", "danger")
 * @package COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr> 
 */
trait MessageAlert{
	
	/**
	  * Affiche un message d'alert avec une confirmation par un bouton
	  * 
      *
	  * @param string $typeAlert success, info, warning ou danger
	  * @param string $titreAlert Titre de l'alerte
	  * @param string $MsgAlert Contenu du message d'alert.
	  * @lienBtn strinf $lienBtn url du bouton pour passer à l'étape suivante.
	  */
	public function AfficheAlert($typeAlert, $titreAlert, $MsgAlert, $lienBtn){	
		echo "<div class='margintop70'></div>";
		echo "<div class='text-center alert alert-$typeAlert fade in'>";
		echo "<h4> $titreAlert </h4>";
		echo "<a class='btn btn-$typeAlert' data-dismiss='alert' href='$lienBtn'>Continuer</a>";
		echo "<p>$MsgAlert</p>";
		echo "</div>";	
	}	
	
	/**
	 * Affichage d'une demande de confirmation avec deux deux boutons (Ok ou annuler)
	 * L'affichage est géré par une alert bootstrap : colorié en fonction des niveaux 'warning', 'success' ou 'alert' 
	 *
	 * @param $action string 'warning', 'success' ou 'alert' 
	 * @param $titreAlert string Titre de l'alerte
	 * @param $lienBtnOk string url du lien OK
	 * @param $lienBtnAnnule string url du lien d'annulation
	 */
	 public function DemanderConfirmation($action, $titreAlert, $lienBtnOk, $lienBtnAnnule ){
		 $MsgAlert = ''; //optionnel
		 
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
			echo "<h4> $titreAlert </h4>";
			echo "<a class='btn btn-$typeAlert' data-dismiss='alert' href='$lienBtnAnnule'>Annuler</a>";
			echo "  ";
			echo "<a class='btn btn-$typeAlert' data-dismiss='alert' href='$lienBtnOk'>Confirmer</a>";
			echo "<p>$MsgAlert</p>";
		echo "</div>";		 
	 }








	
}

