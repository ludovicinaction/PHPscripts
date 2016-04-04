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
		$parts = explode("@", $from_adr);

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
		$message_txt = strip_tags($message_html);
		$message_html = utf8_decode($message_html);

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: ".imap_rfc822_write_address($parts[0], $parts[1], $sujet)."\r\n";
		$headers.= "Reply-to: \"$replay_name\" <$replay_adr>" . $passage_ligne;
		$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";


		 $message .= "\r\n\r\n--" . $boundary . "\r\n";
		 $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
		 $message .= $message_txt;

		 $message .= "\r\n\r\n--" . $boundary . "\r\n";
		 $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
		 $message .= $message_html;

		 $message .= "\r\n\r\n--" . $boundary . "--";

		//return mail($to, $sujet, $message, $headers);
		return mail($to, $sujet, $message, $headers);
	}





} // fin classe
