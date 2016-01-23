<?php
/**
 * Class Utilitaires
 * @package COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr>
 */
Class Utilitaires{
	
	public function DisplayPageMetaData($titre, $description, $keywords){
		echo '<title>' . $titre . '</title>';
		echo "\n";
		echo '<meta name="description"' . ' content="' . $description . '"'.' />' ;
		echo "\n";
		echo '<meta name="keywords"' . ' content="' . $keywords . '"'.' />' ;	
	}




	 /**
	  * Send a multipart email (html and text-plain are supported)
	  *
	  * @param string $to 
	  * @param string $sujet
	  * @param string $message_html ( html message version )  
	  * @param string $from_name 
	  * @param string $from_adr
	  * @param string $replay_name   
	  * @param string $replay_adr
	  * @return bool Email send return (true=send is ok / false=send is ko)
	  */
	public function sendEmail($to, $sujet, $message_html, $from_name, $from_adr, $replay_name, $replay_adr){

		//filter_var à utiliser pour chaque arguments passé en paramétre.


		if (preg_match("#@(hotmail|live|msn).[a-z]{2,4}$#", $to))
		{
			$passage_ligne = "\n";
		}
		else
		{
			$passage_ligne = "\r\n";
		}

		$boundary = "-----=".md5(rand());
		//$message_html = nl2br($message_html);
		$message_txt = strip_tags($message_html);

		$message_html = utf8_decode($message_html);


		$headers = "From: \"$from_name\"<$from_adr>" . $passage_ligne;
		$headers.= "Reply-to: \"$replay_name\" <$replay_adr>" . $passage_ligne;
		$headers.= "MIME-Version: 1.0" . $passage_ligne;
		$headers.= "Content-Type: multipart/alternative;" . $passage_ligne . " boundary=\"" . $boundary . "\"" . $passage_ligne;

		$message = $passage_ligne . $boundary . $passage_ligne;

		$message .= "Content-Type: text/plain; charset=\"ISO-8859-1\"" . $passage_ligne;
		$message .= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
		$message .= $passage_ligne . $message_txt . $passage_ligne;

		$message .= $passage_ligne . "--" . $boundary . $passage_ligne;

		$message .= "Content-Type: text/html; charset=\"ISO-8859-1\"" . $passage_ligne;
		$message .= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
		$message .= $passage_ligne . $message_html . $passage_ligne;

		$message .= $passage_ligne . "--" . $boundary . "--" . $passage_ligne;
		$message .= $passage_ligne . "--" . $boundary . "--" . $passage_ligne;

		return mail($to, $sujet, $message, $headers);
	}





} // fin classe
