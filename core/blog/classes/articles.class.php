<?php
require_once 'core/images.class.php';
require_once 'core/Pagination.class.php';

require_once 'core/messageAlert.trait.php';
require_once 'core/fichier.trait.php';

require_once 'core/Exception.class.php';
require_once 'core/CommunDbRequest.trait.php';

/**
 * Classe Articles
 * @category Class modele du module "Blog"
 * @package BLOG
 * @author Ludovic <ludovicinaction@yahoo.fr>
 * @version 1.0
 * @todo :Dans le __construct faire appel aux paramétres de configuration (affichage et comportement) du blog
 * @todo Vérifier que la fonction sauvDonneesArticle() est utilisé.	
 * Boutons sociaux ( {@link https://github.com/ozdemirburak/jquery-floating-social-share} )
 * @property int $nbrTotalAffiche Utilisé pour la pagination de l'affichage.
 * @property int valeur xs  
 *
 */
class Articles
{	
	use MessageAlert, Fichiers, CommunDbRequest;
	
	//attributs de configuration du blog
	public $nbrTotalAffiche;	
	
	public $aff_xs;
	public $aff_sm;
	public $aff_md;
	public $aff_lg;
	public $art_page;
	public $ctrl_comm;
	public $mail_exp;
	public $name_exp;
	public $mail_reply;
	public $name_reply;
	public $mail_obj;
	public $mail_txt;
	
	public function __construct(){
		
		//nombre total d'article à afficher pour les internautes
		$sReq = 'SELECT count(id_art) as nbrTotArt FROM articles WHERE DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null';

		//Détermination de $this->nbrTotalAffiche 
		$this->nbrTotalArtAfficher($sReq);

		//Lecture de la configuration
		$aConfig = $this->lecture_config();

	}

	//Détermination de $this->nbrTotalAffiche en fonction de la requête de pagination
	private function nbrTotalArtAfficher($sreq){
		$result = SPDO::getInstance()->query($sreq);
		$atotArt = $result->fetch(PDO::FETCH_ASSOC);
		$this->nbrTotalAffiche = $atotArt['nbrTotArt'];
	}


	/** 
	* N'affiche pas l'année sauf si l'année de création de l'article est antérieur à l'année en cours.
	*
	* @param date $dDate Date à traiter 
	* @return string date traitée
	*/	
	private function TraiteDateCreation($dDate){

		if (Admin::$lang == 'FR'){
			if (date('Y') == date('Y', strtotime($dDate))) 
				$sDateTraite = strftime('%d %B', strtotime($dDate));
			else $sDateTraite = strftime('%d %B %Y', strtotime($dDate));
		}
		elseif(Admin::$lang == 'EN'){
			if (date('Y') == date('Y', strtotime($dDate))) $sDateTraite = date('F d', strtotime($dDate));
			else $sDateTraite = date('F d, Y', strtotime($dDate));
		}
		return strtoupper((string) $sDateTraite);
	}
	

	
	/**
	  * Lecture des statistiques d'un articles (nbr commentaires, visites, partages...)
	  *
	  * @param int $id_art ID de l'article
	  * @return int Nombre total de commentaires pour l'article
	  * @todo : pour les autres stats, il faudra faire un tableau contenand les tableau des résultats pour chaque type de stats;
	  */	
	private function LireStatsArticle($id_art){
		$sreq = 'select sum(tot) as somme from ( select count(*) as tot from commentaires_rep where (ctrl_aff=0 or valid_rep=1) and id_commentaire in (select id_com from commentaires_blog where (ctrl_aff=0 or valid_com=1) and id_art='.$id_art.') union select count(*) as tot from commentaires_blog where (ctrl_aff=0 or valid_com=1) and id_art='.$id_art.' ) AS tab_tot';
		$result = SPDO::getInstance()->query($sreq);
		$aSomCom = $result->fetch(PDO::FETCH_ASSOC);	//$aSomCom['somme'] = Total des commentaires+réponses.
		return $aSomCom;
	}
	
	
	/**
	  * Lecture d'un article
	  *
	  * @param int $id ID de l'article
	  * @return array Les différentes données de l'article
	  */
	public function LireUnArticle($id){
		$aResult = array();
		$sReqSelect = 'SELECT id_art, titre_art, date_crea_art, DATE_FORMAT( date_pub_art , \'%d/%m/%Y\')  AS date_pub_art, vignette_art, resum_art, contenu, id_categorie, keywords_art FROM articles WHERE id_art='.$id;
		$aResult = SPDO::getInstance()->query($sReqSelect);
		
		$aUnArt = $aResult->fetch(PDO::FETCH_ASSOC);
		//Traitement de l'affichage de la date de création.	
		$sDateTraite = $this->TraiteDateCreation($aUnArt['date_crea_art']);
		$aUnArt['date_crea_art'] = $sDateTraite;
		
		//Lectures des statistiques de l'articles
		$aStats = $this->LireStatsArticle($id);
		
		$aArticle = array('art'=>$aUnArt, 'somme-com'=>$aStats);
		
		return $aArticle;
	}	
	
	/**
	  * Lecture des catégories d'articles
	  *
	  * @return array Les différentes catégories.
	  */
	public function LireCategories(){
		
		$req = 'SELECT * from cat_article order by nom_cat';
		$aResult = SPDO::getInstance()->query($req);
		$aListeCat = $aResult->fetchAll(PDO::FETCH_ASSOC);	
		return $aListeCat;
	}
	
	
	
	/**
	  * Lecture des articles en fonction de critères de recherche et du mode ('front' ou 'back' office) 
	  *
	  * @param string $mode mode ('util' ou 'admin'). mode 'util' les articles sont affichés en fct de la date de publication.
	  * @param int $parpage nombre d'articles à recherche pour une page
	  * @return array $aListeArticles les paramétres des articles trouvés
      *	@todo : Gérer les erreurs ou si aucun articles n'est trouvé
	  */
	public function LireLesArticles($mode, $parpage=0)
	{
		
		if ( (isset($_POST['cat'])) && ($_POST['cat'] !=0)  ) {
			$aCrit['cat'] = $_POST['cat'];
			$cat = (int) $_POST['cat'];
			$sReq = "SELECT count(id_art) as nbrTotArt FROM articles WHERE id_categorie=$cat and DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null";
		}
		elseif ( (!isset($_POST['cat'])) || ($_POST['cat']==0) ) {
			$sReq = "SELECT count(id_art) as nbrTotArt FROM articles WHERE DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null";	

			$cat = 0;
		} 
		

		//Récupération des critères de tri s'ils existent.
		//$aCrit = array();
		if (isset($_GET['tri'])){
			if (isset($_POST['datedebut'])) $aCrit['datedebut'] = $_POST['datedebut'];
			if (isset($_POST['datefin'])) $aCrit['datefin'] = $_POST['datefin'];
		}
		
		//Initialisation des variables de tri
		if (isset($aCrit))
		{   
			if (isset($aCrit['datedebut']) && $aCrit['datedebut'] != '' ) {
					$dt = \DateTime::createFromFormat('d/m/Y', $aCrit['datedebut']);
					$datdebut = $dt->format('Y-m-d');		
			}
			else $datdebut = '';
			
			if (isset($aCrit['datefin']) && $aCrit['datefin'] != ''){
					$dt = \DateTime::createFromFormat('d/m/Y', $aCrit['datefin']);
					$datfin = $dt->format('Y-m-d');	
			}
			else $datfin = '';
			
			if (isset($aCrit['cat'])) {
				if ($aCrit['cat'] != 0) $cat = $aCrit['cat'];
				elseif ($aCrit['cat'] == '0')  $cat=0;
			}
			else $cat='';

			//Construction de la requete
			if ($cat != '' || $cat!=0){
				$sReqWhere = ' where id_cat='.$cat;
				if ($datdebut != '' && $datfin == ''){
					$sReqWhere .= ' and date_crea_art >= \''.$datdebut.'\'';	
				}elseif ($datfin!='' && $datdebut==''){
					$sReqWhere .= ' and date_crea_art <= \''.$datfin.'\'';
				}elseif ($datdebut!='' && $datfin!=''){
					$sReqWhere .= ' and date_crea_art between \''.$datdebut . '\' and \'' . $datfin.'\'';
				}elseif ($datdebut=='' && $datfin==''){
					$sReqWhere .= '';
				}
				
			}else
			{
				if ($datdebut != '' && $datfin==''){
					$sReqWhere = ' where date_crea_art >= \'' . $datdebut.'\'';	
				}elseif($datfin!='' && $datdebut==''){
					$sReqWhere = ' where date_crea_art <= \''.$datfin.'\'';
				}elseif($datfin!='' && $datdebut!=''){
					$sReqWhere = ' date_crea_art between \''.$datdebut . '\' and \'' . $datfin.'\'';
				}elseif ($datdebut!='' && $datfin!=''){
					$sReqWhere = '';
				}elseif ($datdebut=='' && $datfin == ''){
					$sReqWhere = '';
				}		
			}
		}
		else $sReqWhere = '';
		

		$aResult = array();	

		$this->nbrTotalArtAfficher($sReq);

		$NbrTotArt = $this->nbrTotalAffiche;

		//Déterminer l'index de pagination
		if (isset($_GET['page']) && $_GET['page']>0 && $_GET['page']< $NbrTotArt)
			$cPage = $_GET['page'];
		else $cPage = 1;
		
		 if ($mode == 'util') {
			$sReqSelect = 'SELECT id_art, titre_art, vignette_art, date_crea_art, date_pub_art, resum_art, keywords_art ';
			$sReqFrom 	= 'FROM articles inner join CAT_ARTICLE on id_categorie=id_cat';
			
			if ($cat == 0) $sReqWhere = ' WHERE DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null';
			else $sReqWhere = " WHERE id_categorie=$cat AND DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null";
			$sReqOrder 	= ' order by date_crea_art desc LIMIT ' . (($cPage-1)*$parpage) . ', ' . $parpage;
		 }
		elseif($mode == 'admin') {
			$sReqSelect = 'SELECT id_art, titre_art, vignette_art, date_crea_art, DATE_FORMAT( date_pub_art , \'%d/%m/%Y\') AS date_pub_art, resum_art, keywords_art';			
			$sReqFrom = ' FROM articles inner join cat_article on id_categorie=id_cat';
			$sReqOrder = ' order by date_crea_art desc';
		}
		
		$sReqSelectArticles = $sReqSelect . $sReqFrom . $sReqWhere . $sReqOrder;

		$aResult = SPDO::getInstance()->query($sReqSelectArticles);		

		//On sauvegarde toutes les données dans un tableau pour pouvoir les traiter les "date de création" (méthode 'TraiteDateCreation')
		//Sinon suite à foreach d'un objet PDOStatement, les données sont supprimées.
		$aListeArticles = $aResult->fetchAll(PDO::FETCH_ASSOC);

		// Traitement de la 'date de création' pour son affichage et enregistrement des stats
		foreach ($aListeArticles as $index => $value)
		{ 
			$sDateTraite = $this->TraiteDateCreation($value['date_crea_art']);
			//foreach gére les tableaux par valeur et non pas référence (autre possibilité mettre '&$value' dans le foreach)
			$aListeArticles[$index]['date_crea_art'] = $sDateTraite;
			
			//Ajout des statistiques
			$aStats = $this->LireStatsArticle($value['id_art']);
			$aListeArticles[$index]['som-comm'] = $aStats['somme'];
		}
		
		//Détermination du nbr d'article à afficher (pour la pagination)
		//$this->nbrTotalAffiche = count($aListeArticles);





 	    return $aListeArticles;
	}
	
	public function LireMetaDonnees($id_art){
		$sResult = '';
		$sMeta = '';
		
		$sReq = 'SELECT titre_art, resum_art, keywords_art FROM articles WHERE id_art='.$id_art;		
		$sResult = SPDO::getInstance()->query($sReq);
		$sMeta = $sResult->fetch(PDO::FETCH_ASSOC);
		return $sMeta;
	}



	/**
	 * 
	 * /// Gestion des commentaires ///
	 *
	 */

	 /**
	  * Lecture d'un commentaire
	  *
	  * @param int id du commentaire
	  * @param string 'com' (commentaire) ou 'rep' (réponse)
	  * @return array informations
	  */
	 public function LireCommentaire($id, $type){
	 	if ($type == 'com')	$sReq = "SELECT date_com,nom_com, photo_com, email_com, texte_com, id_com, id_art, photo_com, photo_type, ctrl_aff, email_valid FROM commentaires_blog WHERE id_com=$id ";
	 	elseif ($type == 'rep') $sReq = "SELECT * FROM commentaires_rep where id_rep = $id";

		$sRequete = SPDO::getInstance()->query($sReq);		
		$aRequete = $sRequete->fetch(PDO::FETCH_ASSOC); 
		return $aRequete;

	 }


	 /**
	  * Lecture des commentaires de l'article demandé
	  *
	  * @param int id_art ID de l'article demandé
	  * @return array $aRequete Données des commentaires trouvés pour l'article demandé.
	  */
	 public function LireCommentaires($id_art){		
		$sReq = 'SELECT date_com,nom_com, photo_com, texte_com, id_com, photo_com, photo_type, ctrl_aff, email_valid FROM commentaires_blog WHERE ctrl_aff=0 or valid_com=1 and id_art='.$id_art . ' order by date_com';
		$sRequete = SPDO::getInstance()->query($sReq);
		$aRequete = $sRequete->fetchAll(PDO::FETCH_ASSOC);
		//var_dump($aRequete);
		return $aRequete;
	 }

	 /**
	  * Lecture de tous les commentaires
	  *
	  * @return array $aRequete Tableau contenand les commentaires
	  */
	 public function LireTousLesCommentaires(){
	 	//on affiche tout de même ceux validé par l'administrateur (valid_com=1) pour afficher les réponses qui en dépendent et qui sont peut être à valider 
	 	//(permet aussi de voir la chrologie des commentaires/réponses.
	 	$sReq = "SELECT date_com,nom_com, email_com, texte_com, id_com, id_art, ctrl_aff, email_valid, valid_com FROM commentaires_blog WHERE ctrl_aff=1 order by id_art, date_com desc";
		$sRequete = SPDO::getInstance()->query($sReq);		
		$aRequete = $sRequete->fetchAll(PDO::FETCH_ASSOC); 
		
		return $aRequete;
	 }

	 /**
	  * Lecture des réponses d'un commentaire
	  *
	  * @param int $id_comm Id du commentaire
	  * @return array $aRequete Données des réponses.
	  * @
	  */
	 public function LireReponses($id_comm, $use){

		if ($use == 'admin') $sReq = 'SELECT * FROM commentaires_rep WHERE ctrl_aff=1 and valid_rep=0 and id_commentaire='.$id_comm . ' order by id_rep';
		elseif ($use == 'util') $sReq = 'SELECT * FROM commentaires_rep WHERE (ctrl_aff=0 or valid_rep=1) and id_commentaire='.$id_comm . ' order by id_rep';
		
		$aRequete = SPDO::getInstance()->query($sReq);
		 
		return $aRequete;
	 }
	 
	 /**
	  * Valider un commentaire
	  *
	  * @param int id commentaire
	  * @return string 'com' (ccmmentaire) ou 'rep'
	  */
 		public function ConfirmValideCommentaire($id, $type, $email_valid, $msg_email_ok, $msg_email_ko){
			$btnOK = "admin.php?p=gest_art&a=gest_com&id=$id&t=$type&c=valid&v=$email_valid&eng=yes";
 			if ($email_valid == 0) {
 				$this -> DemanderConfirmation('modif', $msg_email_ko, $btnOK, 'admin.php?p=gest_art&a=gest_com&c=init', Admin::$lang );
 			}
 			elseif ($email_valid == 1){
 				$this -> DemanderConfirmation('creer', $msg_email_ok, $btnOK, 'admin.php?p=gest_art&a=gest_com&c=init', Admin::$lang );	
 			}
 		}


	 /**
	  * Validation en base d'un commentaire
	  *
	  * @param type comentaire_ici
	  * @return type comentaire_ici
	  */
	 public function ValiderCommentaire($id, $type, $successTitle, $dangerTitle){
		 if ($type == 'com') $sReq = "UPDATE commentaires_blog SET valid_com = :valid WHERE id_com=$id"; 
		 elseif ($type == 'rep') $sReq = "UPDATE commentaires_rep SET valid_rep = :valid WHERE id_rep=$id";

		 $update = SPDO::getInstance()->prepare($sReq);
		 $update -> bindValue(':valid', 1);

		 try {
			 $resultOK = $update->execute();
		 } catch(PDOException $e){
			 echo $e->getMessage();
		 }

		 //if ($resultOK) $this->AfficheAlert('success', 'Commentaire validé avec succés', '', "admin.php?p=gest_art&a=gest_com&c=init");
		 $this->AfficherResultatRqt($resultOK, 'admin.php?p=gest_art&a=gest_com&c=init', $successTitle, $dangerTitle);
		  
	
	}



	 /**
	  * Enregistrenent un commentaire ou réponse (soit un nouveau message soit une réponse à un commentaire)
	  * Insert soit dans 'commentaires_blog' soit 'commentaires_rep' 
	  *
	  * @param string $nom nom du commentateur
	  * @param string $mail son mail
	  * @param string $siteweb eventuellement son site web
	  * @param string $contenu le contenu du commentaire
	  * @param int $id SOIT l'id de l'article (pour un commentaire) SOIT l'id du commentaire (pour une réponse à un commentaire)
	  * @param string $type_comm 'new'=>cas du commentaire ; 'rep'=>cas d'une réponse.
	  **/
	public function EngNouvComm($nom, $mail, $siteweb, $contenu, $id, $type_comm, $aMsg){
		//Filtrage des données de type $_POST
		$aPost = array('nom'=>$nom, 'mail'=>$mail, 'web'=>$siteweb, 'contenu'=>$contenu ,'id'=>$id);
		$sFiltres = array('nom'=>FILTER_SANITIZE_STRING,
						'mail'=>FILTER_VALIDATE_EMAIL,
						'web'=>FILTER_SANITIZE_STRING,
						'contenu'=>FILTER_SANITIZE_STRING,
						'id'=>FILTER_VALIDATE_INT);
						
		$aDataclean = filter_var_array($aPost, $sFiltres);
		$nom  = $aDataclean['nom'];
		$mail = $aDataclean['mail'];
		$siteweb = $aDataclean['web'];
		$contenu = $aDataclean['contenu'];
		$id = $aDataclean['id'];
		$id_art = (int) $_GET['id'];
		//Fin filtrage

		//Vérification si les commentaires sont controlé
		$result = SPDO::getInstance()->query('select * from blog_config');
		$aConfig = $result->fetch(PDO::FETCH_ASSOC);
		$ctr_comm = $aConfig['control_comm'];

		$dDateJour = date('Y-m-d H:i:s');
		$iValid = 0; // Par défaut, le commentaire est à valider.

		$sNomInputImg = 'imagefichier';
		if($_FILES['imagefichier']['name'] != ''){
			$max_width = 50;
			$max_height = 50;
			$oImg = new Images;
			$image = $oImg -> Redim($sNomInputImg, $max_width, $max_height);
			$typeimg = $image['type_image'];
			$img = $image['ressource_img'];
		}
		else {
			$img = null;
			$typeimg= null;
		}

		//Construction de la requête insert en fonction de si c'est un nouveau message ou une réponse.
		if ($type_comm == 'new') {
			$sReq = "INSERT INTO commentaires_blog (date_com, nom_com, email_com, siteweb_com, texte_com, valid_com, id_art, photo_com, photo_type, ctrl_aff) ";
			$sReq .= "VALUES (:datej, :nom, :mail, :web, :txt, :valid, :id, :photo, :type_photo, :ctrl_aff)";
		}
		elseif ($type_comm == 'rep' ) {		
			//Détermination du "ref_rep" (soit pour une réponse à un commentaire initial soit )
			if (isset($_POST['ref_rep']))
			{  
				//Réponses à une réponse déjà donné
				$ref=$_POST['ref_rep'];	
				//Recherche du ref_rep à utiliser pour une nouvelle réponse.
				for ($i=1 ; $i<100 ; $i++){
					$reqCount = 'select count(id_rep) from commentaires_rep where id_commentaire=' . $id . ' and ref_rep='.$ref.$i;
					$pRes = SPDO::getInstance()->query($reqCount);
					$iNum = $pRes->fetchAll();
					$iNum = $iNum[0][0];
					if ($iNum == 0) 
					{
						$ref_rep = (int)$ref.$i;
						break;
					}	
				}
			}
			else 
			{
				//Réponse à un commentaire initial. Dans ce cas "ref_rep" égal l'id suivant
				//$id correspond à id_com
				$iNbrRep = SPDO::getInstance()->query('select count(id_rep) from commentaires_rep where id_commentaire='.$id);			
				$ref_rep =  $iNbrRep->fetchAll();
				$ref_rep = (int)$ref_rep[0][0];
			}
				
			$sReq = "INSERT INTO commentaires_rep (date_rep, nom_rep, email_rep, siteweb_rep, texte_rep, valid_rep, id_commentaire, ref_rep, photo_rep, photo_type, ctrl_aff) ";
			$sReq .= "VALUES (:datej, :nom, :mail, :web, :txt, :valid, :id, :ref_rep, :photo, :type_photo, :ctrl_aff)";
		}		
		
		//Execution de la requête insert 
		$rqt = SPDO::getInstance()->prepare($sReq);
		
		$rqt -> bindValue(':datej', $dDateJour);
		$rqt -> bindValue(':nom', $nom);
		$rqt -> bindValue(':mail', $mail);
		$rqt -> bindValue(':web', $siteweb);
		$rqt -> bindValue(':txt', $contenu);
		$rqt -> bindValue(':valid', $iValid);
		$rqt -> bindValue(':id', $id);
		if ($type_comm == 'rep' ) $rqt -> bindValue(':ref_rep', $ref_rep);
		$rqt -> bindValue(':photo', $img, PDO::PARAM_LOB);
		$rqt -> bindValue(':type_photo', $typeimg);
		$rqt -> bindValue(':ctrl_aff', $ctr_comm);

		try {
			$resultOK = $rqt->execute();
		}catch (PDOException $e){
			echo $e->getMessage();
		}
		
		unset($oImg);

		//Message en fonction du contrôle des commentaires
		if ($resultOK) {
			if ($ctr_comm == 1) {
				//Si controle des commentaires=>envoyer un email
				$MsgAlert = $aMsg[Admin::$lang]['msg_comments_ctrl'];
				$this->AfficheAlert('success', $aMsg[Admin::$lang]['msg_confirm'], $MsgAlert, "blog.php?id=$id_art");

				$this->lecture_config();
				
				$obj 		= $this->mail_obj; 
				$from_name		= $this->name_exp;
				$from_adr		= $this->mail_exp;
				$replay_name	= $this->name_reply;
				$replay_adr		= $this->mail_reply;

				//Insertion du jeton dans l'email
				$sJeton = md5(uniqid(rand(), true)) . date('YmdHis');

				if ($type_comm == 'new') {
					//Détermination du id_com (qui vient d'être créé)
					$query = SPDO::getInstance()->query('select max(id_com) as id_com from commentaires_blog');
					$result =  $query->fetchAll();
					$id = $result[0]['id_com'];

					$sLien = "http://localhost/phpscripts/valid_comm.php?com=$id&t=$sJeton";
				}
				elseif($type_comm == 'rep'){
					//Détermination du id_rep (qui vient d'être créé)
					$query = SPDO::getInstance()->query('select max(id_rep) as id_rep from commentaires_rep');
					$result = $query->fetchAll();
					$id = $result[0]['id_rep'];	

					$sLien = "http://localhost/phpscripts/valid_comm.php?rep=$id&t=$sJeton";
				}
				
				//Contruction du contenu : remplacement de 'VALID' par le lien
				
				//$sLink = "<a href='$sLien'>".'Confirmez votre commentaire</a>';
				$sLink = "<a href='$sLien'>". $aMsg[Admin::$lang]['msg_publish_confirm'] . '</a>';
				$mail_txt = $this->mail_txt;
				$pos = strpos($mail_txt, 'VALID');
				$mail_exp = str_replace ('VALID' , $sLink, $mail_txt );			

				//Enregistrement du jeton
				if ($type_comm == 'new') $this->EngJetonCommentaire ($id, 'com', $sJeton);
				elseif ($type_comm == 'rep') $this->EngJetonCommentaire ($id, 'rep', $sJeton);

				//Envoi du mail
				$oUtil = new Utilitaires();
				$bSendOK = $oUtil -> sendEmail($mail, $obj, nl2br($mail_exp), $from_name, $from_adr, $replay_name, $replay_adr);
				//if(!$bSendOK) $this->AfficheAlert('danger', 'Le mail de confirmation n\'a pas pu être envoyé', '', "blog.php?id=$id_art");
				if(!$bSendOK) $this->AfficheAlert('danger', $aMsg[Admin::$lang]['msg_conf_not_send'], '', "blog.php?id=$id_art");

			}
			elseif ($ctr_comm == 0){
				//$this->AfficheAlert('success', 'Merci pour votre commentaire', '', "blog.php?id=$id_art");
				$this->AfficheAlert('success', $aMsg[Admin::$lang]['msg_thank_comment'], '', "blog.php?id=$id_art");
			}
        }       
        //redirection (suppresion dans l'url de la partie "&new=x') 
        //Evite en cas de rechargement de la page de créer un nouvel enregistrement        
    /*
        $urlCourante=$_SERVER["REQUEST_URI"];
        $urlGet = explode("&",$urlCourante);
        $pos = strpos($urlCourante, '&');
        $chaine = substr($urlCourante, 0, $pos);
        header("location:$chaine");
      */          
	}	  

	// A rendre privat par la suite
	public function EngJetonCommentaire ($id, $type, $jeton){
		 if ($type == 'com') $sReq = "UPDATE commentaires_blog SET jeton = :jeton WHERE id_com=$id"; 
		 elseif ($type == 'rep') $sReq = "UPDATE commentaires_rep SET jeton = :jeton WHERE id_rep=$id";

		 $update = SPDO::getInstance()->prepare($sReq);
		 $update -> bindValue(':jeton', $jeton);

		 try {
			 $result = $update->execute();
		 } catch(PDOException $e){
			 echo $e->getMessage();
		 }
	}




	// *** Fin de gestion des commentaires
	public function multi_sort($array, $akey)
	{  
	  if (!function_exists('compare')){	  
		  function compare($a, $b)
		  {
			 global $key;
			 return strcmp($a[$key], $b[$key]);
		  } 
	  }  
	  usort($array, "compare");
	  return $array;
	}
	  
	  



	  
	 /*
	  * ////////////
	  * BACK OFFICE
	  * ///////////
	  */
	  


	 /**
	  * Création d'une catégorie
	  *
	  * @param type comentaire_ici
	  * @return type comentaire_ici
	  */
	 public function CreateCategory(){
		$nom_cat = $_SESSION['nom_cat'];
	 	$sReq = 'insert into cat_article (nom_cat) VALUES (:nom_cat)';

		 $Insert = SPDO::getInstance()->prepare($sReq);
		 $Insert -> bindValue(':nom_cat', $nom_cat);
	
		 try {
			 $result = $Insert->execute();
		 } catch(PDOException $e){
			 echo $e->getMessage();
		 }
	
		$this->AfficherResultatRqt($result, 'admin.php?p=gest_art&a=gest_cat&c=init');

	 }





	 /**
	  * Mise à jour d'une catégorie
	  *
	  *	
	  */
	 public function UpdateCategory(){
		$nom_cat = $_SESSION['nom_cat'];
        $id_cat = $_SESSION['id_cat'];

	 	$sReq = 'UPDATE cat_article set nom_cat = :nom_cat where id_cat=' . $id_cat;

		 $Update = SPDO::getInstance()->prepare($sReq);
		 $Update -> bindValue(':nom_cat', $nom_cat);
	
		 try {
			 $result = $Update->execute();
		 } catch(PDOException $e){
			 echo $e->getMessage();
		 }
	
		$this->AfficherResultatRqt($result, 'admin.php?p=gest_art&a=gest_cat&c=init');	 	
	 
	 }	 

	  
	/**
	 * Sauvegarde des données articles en provenance du formulaire en session
	 *
     */ 	 
	public function sauvDonneesArticle(){

		if (isset($_FILES['vignette'])) $_SESSION['vignette'] = $_FILES['vignette'];  

		$_SESSION['cat'] 		= filter_input(INPUT_POST, 'cat', FILTER_SANITIZE_NUMBER_INT);
		$_SESSION['id'] 		= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
		$_SESSION['titre'] 		= filter_input(INPUT_POST,'titre', FILTER_SANITIZE_STRING);
		$_SESSION['desc'] 		= filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_STRING);
		$_SESSION['keyword']	= filter_input(INPUT_POST,'keyword', FILTER_SANITIZE_STRING);
		$_SESSION['date_pub']	= filter_input(INPUT_POST, 'date_pub', FILTER_SANITIZE_STRING); 
		$_SESSION['texte_article'] = filter_input(INPUT_POST, 'texte_article', FILTER_SANITIZE_STRING);

		$_SESSION['texte_article'] = $_POST['texte_article'];


		$image = $_FILES['vignette']['name'];
		
		if ( $image != '' ) $this->DeplacerFichier('vignette');
	}
	    
	  
	  
	/**
	 * Récupération des données sessions d'un article puis insert ou update d'un article
	 *
	 * @param string 'creer'=>insert into articles ; 'modif'=>update articles
     */ 	  
	 public function SauveArticle($action, $msg_result_ok, $msg_result_ko){
	
		//Sauvegarde du formulaire dans des variables de session
	
		//Initialisation des variables
        $id_cat		= $_SESSION['cat'];
		$id_art		= $_SESSION['id'];
		$titre		= $_SESSION['titre'];
		$desc		= $_SESSION['desc'];
		$keyword	= $_SESSION['keyword'];
		$date_pub	= $_SESSION['date_pub'];
                
        $chaine = $_SESSION['texte_article'];
        $texte_article = htmlspecialchars($chaine, ENT_QUOTES, 'UTF-8');

		//formatage de la date de publication au format YYYY-MM-DD
		if ($date_pub != ''){
			$dt = \DateTime::createFromFormat('d/m/Y', $date_pub);
			$date_pub_art = $dt->format('Y-m-d');
		}
		else $date_pub_art = null;
		
		// Re-dimenssionnement de l'image.
		if($_SESSION['vignette']['size'] != 0){
			$max_width=350;
			$max_height=234;
			$oImage = new Images;
			$nom_file='vignette';
			$aImg_traite = $oImage->Redim($nom_file, $max_width, $max_height);
			$img_vign = $aImg_traite['ressource_img'];
			$bVignette = true;
		}	 
		else $bVignette = false;
			 
		if ($action == 'creer') {
			$date_crea = date('Y-m-d H:i:s');
			$sReq = "INSERT INTO articles (titre_art, date_crea_art, date_pub_art, vignette_art, resum_art, keywords_art, contenu, id_categorie)";
			$sReq.= " VALUES (:titre, :date_crea, :date_pub, :vignette_art, :resum, :keywords, :contenu, :id_cat)";
	
		}elseif ($action == 'modif'){
			if ($bVignette) {
				$sReq = "UPDATE articles SET titre_art=:titre, date_pub_art=:date_pub, vignette_art=:vignette_art, resum_art=:resum, keywords_art=:keywords, contenu=:contenu, id_categorie=:id_cat";
				$sReq .= " WHERE id_art=$id_art"; 
			}else{
				$sReq = "UPDATE articles SET titre_art=:titre, date_pub_art=:date_pub, resum_art=:resum, keywords_art=:keywords, contenu=:contenu, id_categorie=:id_cat";
				$sReq .= " WHERE id_art=$id_art"; 
			}
		 }
		 
		 $Insert = SPDO::getInstance()->prepare($sReq);
		 $Insert -> bindValue(':titre', $titre);
		 if ($action == 'creer') 
			 $Insert -> bindValue(':date_crea', $date_crea);
		 $Insert -> bindValue(':date_pub', $date_pub_art);	
		 if ($bVignette) 
			 $Insert -> bindValue(':vignette_art', $img_vign);
		 $Insert -> bindValue(':resum', $desc);
		 $Insert -> bindValue(':keywords', $keyword);
		 $Insert -> bindValue(':contenu', $texte_article);
		 $Insert -> bindValue(':id_cat', $id_cat);	
		 
		 try {
			 $result = $Insert->execute();
		 } catch(PDOException $e){
			 echo $e->getMessage();
		 }
		unset($oImage);
		//$this->AfficherResultatRqt($result, 'admin.php?p=gest_art&a=modif');

		$this-> AfficherResultatRqt($result, 'admin.php?p=gest_art&a=modif', $msg_result_ok, $msg_result_ko);

	 }
	 
	 


/*
	 private function AfficherResultatRqt($bSauveOK, $lienBtn)
	 {
		$MsgAlert = '';
	
		if ($bSauveOK) {
			$typeAlert = 'success';
			$titreAlert = "Opération effectuée avec succés";
		}
		else {
			$typeAlert = 'danger';
			$titreAlert = "Erreur lors de l'enregistrement";
		}
		$this -> AfficheAlert($typeAlert, $titreAlert, $MsgAlert, $lienBtn);		 
	 }
	 
*/


	 
	 /*
	  *   Configuration
	  */

	 public function lecture_config(){
		$sReq = 'SELECT * FROM blog_config';
		$sRequete = SPDO::getInstance()->query($sReq);
		$aRequete = $sRequete->fetch(PDO::FETCH_ASSOC);

		$this->aff_xs = $aRequete['aff_xs'];
		$this->aff_sm = $aRequete['aff_sm'];
		$this->aff_md = $aRequete['aff_md'];
		$this->aff_lg = $aRequete['aff_lg'];
		$this->art_page = $aRequete['nbr_art_page'];
		$this->ctrl_comm = $aRequete['control_comm'];
		$this->mail_exp = $aRequete['email_from'];
		$this->mail_obj = $aRequete['email_objet'];
		$this->mail_txt = $aRequete['email_text'];
		$this->name_exp = $aRequete['name_from'];
		$this->name_reply = $aRequete['name_reply'];
		$this->mail_reply = $aRequete['email_reply'];




	 }


	 public function sauv_config(){
	 	
	 	//Filtrage des données $_POST
	 	$aPost = array('xs'=>$_POST['aff_xs']
	 		,'sm'=>$_POST['aff_sm']
	 		,'md'=>$_POST['aff_md']
	 		,'lg'=>$_POST['aff_lg']
	 		,'nbr_art'=>$_POST['nbr_art']
	 		,'ctrl_comm'=>$_POST['ctrl_comm']
	 		,'mail_exp'=>$_POST['mail_exp']
	 		,'mail_obj'=>$_POST['mail_obj']
	 		,'mail_texte'=>$_POST['mail_texte']
	 		,'name_exp'=>$_POST['name_exp']
	 		,'name_reply'=>$_POST['name_reply']
	 		,'mail_reply'=>$_POST['mail_reply']
	 		);
	 	
	 	$aFiltres = array('xs'=>FILTER_VALIDATE_INT
	 		,'sm'			=>FILTER_VALIDATE_INT
	 		,'md'			=>FILTER_VALIDATE_INT
	 		,'lg'			=>FILTER_VALIDATE_INT
	 		,'nbr_art'		=>FILTER_VALIDATE_INT
	 		,'ctrl_comm'	=>FILTER_VALIDATE_INT
	 		,'mail_exp'		=>FILTER_VALIDATE_EMAIL
	 		,'mail_obj'		=>FILTER_SANITIZE_STRING
	 		,'mail_texte'	=>FILTER_FLAG_NO_ENCODE_QUOTES
	 		,'name_exp'		=>FILTER_SANITIZE_STRING
	 		,'name_reply'	=>FILTER_SANITIZE_STRING
	 		,'mail_reply'	=>FILTER_VALIDATE_EMAIL
	 		);
	 	
	 	$aDataClean = filter_var_array($aPost, $aFiltres);
	 	//Fin filtrage

		$sReq= "update blog_config set aff_xs = :aff_xs, aff_sm = :aff_sm, aff_md = :aff_md, aff_lg = :aff_lg";	
		$sReq .= ", nbr_art_page = :nb_art, control_comm = :ctrl_comm, email_from = :email_from, email_objet = :email_obj, email_text = :email_txt";
		$sReq .= ", name_from = :name_exp, name_reply = :name_reply, email_reply = :mail_reply";
        
		$pUpdate = SPDO::getInstance()->prepare($sReq);
		$pUpdate -> bindValue(':aff_xs', $aDataClean['xs']);
		$pUpdate -> bindValue(':aff_sm', $aDataClean['sm']);
		$pUpdate -> bindValue(':aff_md', $aDataClean['md']);
		$pUpdate -> bindValue(':aff_lg', $aDataClean['lg']);

		$pUpdate -> bindValue(':ctrl_comm', $aDataClean['ctrl_comm']);
		$pUpdate -> bindValue(':email_from', $aDataClean['mail_exp']);
		$pUpdate -> bindValue(':email_obj', $aDataClean['mail_obj']);
		$pUpdate -> bindValue(':email_txt', $aDataClean['mail_texte']);
		$pUpdate -> bindValue(':nb_art', $aDataClean['nbr_art']);
		$pUpdate -> bindValue(':name_exp', $aDataClean['name_exp']);
		$pUpdate -> bindValue(':name_reply', $aDataClean['name_reply']);
		$pUpdate -> bindValue(':mail_reply', $aDataClean['mail_reply']);

		 try {
			 $result = $pUpdate->execute();
		 } catch(PDOException $e){
			 echo $e->getMessage();
		 }	
	 }
}