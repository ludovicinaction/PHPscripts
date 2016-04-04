<?php
//set_include_path('/live_demo/core/;core/');

require_once 'core/images.class.php';

require_once 'core/Pagination.class.php';
require_once 'core/messageAlert.trait.php';
require_once 'core/fichier.trait.php';
require_once 'core/Exception.class.php';
require_once 'core/CommunDbRequest.trait.php';

/*
require_once 'Pagination.class.php';
require_once 'messageAlert.trait.php';
require_once 'fichier.trait.php';
require_once 'Exception.class.php';
require_once 'CommunDbRequest.trait.php';
*/

/**
 * Articles Class
 * @category Class modele du module "Blog"
 * @package BLOG
 * @author Ludovic <ludovicinaction@yahoo.fr>
 * @version 1.0
 * Boutons sociaux ( {@link https://github.com/ozdemirburak/jquery-floating-social-share} )
 * @property int $TotalNbrDisplay Used for pagination display.
 *
 */
class Articles
{	
	use MessageAlert, Fichiers, CommunDbRequest;
	
	// Blog configuration attributes
	
	private $TotalNbrDisplay; // Total number of post to display 
	// BLOG settings
	private $aff_xs;	// Cellphone display
	private $aff_sm;	// Tablet
	private $aff_md;	// Laptop
	private $aff_lg;	// Desktop
	private $art_page;	// Post number to display per page
	private $ctrl_comm;	// Checking comments
	private $mail_exp;	// Sender mail
	private $name_exp;	// Return address
	private $mail_reply;//Address answer
	private $name_reply;//Response name
	private $mail_obj;	// Object
	private $mail_txt;	// email texte

	//Data contents
	private $_aPostData;	// Article Data
	private $_aCatData;		// Category Data
	private $_aComments;	// Comments Data	
	private $_aAnswer;		// Answer Data	


 /**
  * get and filter post data from private attributs
  *
  */
	public function getPostData() {
		$aDataClean = array();
		$aFilter = array('id_art'=>FILTER_VALIDATE_INT
			, 'contenu'=>FILTER_SANITIZE_STRING
			, 'titre_art'=>FILTER_SANITIZE_STRING
			, 'vignette_art'=>FILTER_UNSAFE_RAW
			, 'date_crea_art'=>FILTER_SANITIZE_STRING
			, 'date_pub_art'=>FILTER_SANITIZE_STRING
			, 'resum_art'=>FILTER_SANITIZE_STRING
			, 'keywords_art'=>FILTER_SANITIZE_STRING
			, 'id_categorie'=>FILTER_SANITIZE_NUMBER_INT
			, 'somme-com'=>FILTER_SANITIZE_NUMBER_INT);
	
		foreach ($this->_aPostData as $key => $aPost) {
			$aDataClean[$key] = filter_var_array($aPost, $aFilter);
		}

		return $aDataClean;
		
	}

	
	public function __construct(){	
		//Total number of articles to display for users
		$sReq = 'SELECT count(id_art) as nbrTotArt FROM blog_articles WHERE DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null';

		// feed $this->TotalNbrDisplay 
		$this->nbrTotalArtDisplayed($sReq);

		// Read configuration
		$aConfig = $this->ReadBlogConfig();
	}

 /**
  * Determination of $this->TotalNbrDisplay (according to the paging request)
  *
  * @param string $sReq SQL Request 
  *
  */ 
	private function nbrTotalArtDisplayed($sreq){
		$result = SPDO::getInstance()->query($sreq);
		$atotArt = $result->fetch(PDO::FETCH_ASSOC);
		$this->TotalNbrDisplay = $atotArt['nbrTotArt'];
	}


	/** 
	* Does not display the year except the article creation year is older than the current year.
	*
	* @param date $dDate Date  
	* @return string treated date
	*/	
	private function TraiteDateCreation($dDate){
		date_default_timezone_set('Europe/Paris');
		
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
	  * Reading the statistical items (nbr comments, visits, sharing ...)
	  *
	  * @param int $id_art ID de l'article
	  * @return int Total Comments for article
	  * @todo : pour les autres stats, il faudra faire un tableau contenand les tableau des résultats pour chaque type de stats;
	  */	
	public function ReadStatsArticle($id_art){
		$sreq = 'select sum(tot) as somme from ( select count(*) as tot from blog_reply where (ctrl_aff=0 or valid_rep=1) and id_commentaire in (select id_com from blog_comments where (ctrl_aff=0 or valid_com=1) and id_art='.$id_art.') union select count(*) as tot from blog_comments where (ctrl_aff=0 or valid_com=1) and id_art='.$id_art.' ) AS tab_tot';
		$result = SPDO::getInstance()->query($sreq);
		$aSomCom = $result->fetch(PDO::FETCH_ASSOC);	//$aSomCom['somme'] = Total des commentaires+réponses.
		return $aSomCom;
	}
	
	
	/**
	  * Read one article
	  *
	  * @param int $id Article ID
	  * @return array Articles data
	  */
	public function ReadOneArticle($id){
		$aResult = array();
		$sReqSelect = 'SELECT id_art, titre_art, date_crea_art, DATE_FORMAT( date_pub_art , \'%d/%m/%Y\')  AS date_pub_art, vignette_art, resum_art, contenu, id_categorie, keywords_art FROM blog_articles WHERE id_art='.$id;
		$aResult = SPDO::getInstance()->query($sReqSelect);
		
		$this->_aPostData = $aResult->fetch(PDO::FETCH_ASSOC);
		// Treatment of displaying the creation date.
		$sDateTraite = $this->TraiteDateCreation($this->_aPostData['date_crea_art']);
		$this->_aPostData['date_crea_art'] = $sDateTraite;
		
		// Readings statistics Articles
		$aStats = $this->ReadStatsArticle($id);		
		$this->_aPostData = array('art'=>$this->_aPostData, 'somme-com'=>$aStats);
		
	}	
	
	/**
	  * Read post categories from database
	  *
	  * @return array The categories.
	  */
	private function ReadCategoryData(){
		
		$req = 'SELECT * from blog_cat_article order by nom_cat';
		$aResult = SPDO::getInstance()->query($req);
		$aListCat = $aResult->fetchAll(PDO::FETCH_ASSOC);	
		foreach ($aListCat as $key => $aCat) {
			$this->_aCatData[$key]['id_cat'] = $aCat['id_cat'];
			$this->_aCatData[$key]['nom_cat'] = $aCat['nom_cat'];
		}
		
	}
	


 /**
  * get and filter category data
  *
  */	
public function getCategoryData(){
	$this->ReadCategoryData();

	foreach ($this->_aCatData as $key => $aCat) {
		$aCleanCat[$key]['id_cat']  = filter_var($aCat['id_cat'], FILTER_SANITIZE_NUMBER_INT);
		$aCleanCat[$key]['nom_cat'] = filter_var($aCat['nom_cat'], FILTER_SANITIZE_STRING);
	}

	return $aCleanCat;

	}

	
	/**
	  * Reading articles according to search criteria and mode ('front' or 'back' office) 
	  *
	  * @param string $mode mode ('util' ou 'admin') Posts are displayed according to the date of publication.
	  * @param int $parpage Number of items to be searched for a page
	  * @param int $cat Id category (sort of filter). if $cat=0 => All category.
	  * @return array $aListeArticles The parameters of articles found
      *	@todo : Gérer les erreurs ou si aucun articles n'est trouvé
	  */
	public function ReadAllArticles($mode, $cat)
	{
		if (isset($_GET['tri'])) $get_tri = filter_var($_GET['tri'], FILTER_SANITIZE_STRING);

		//get $this->art_page
		$aConfig=  $this->getConfigValues();
		$parpage = (int) $aConfig['art_page'];
		
		$cat = (int) $cat;

		if ( isset($cat) && $cat !=0  ) {
			$aCrit['cat'] = $cat;

			$sReq = "SELECT count(id_art) as nbrTotArt FROM blog_articles WHERE id_categorie=$cat and (DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null)";
		}
		elseif ( (!isset($cat)) || ($cat == 0) ) {
			$sReq = "SELECT count(id_art) as nbrTotArt FROM blog_articles WHERE DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null";	

			$cat = 0;
		} 

		// Retrieving sorting criteria if they exist.
		if (isset($get_tri)){
			if (isset($_POST['begindate'])) $aCrit['datedebut'] = filter_var($_POST['begindate'], FILTER_SANITIZE_STRING);
			if (isset($_POST['enddate'])) $aCrit['datefin'] = filter_var($_POST['enddate'], FILTER_SANITIZE_STRING);
		}
		

		// Initialize sort variables
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

			// Request Construction 
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
		
		//update TotalNbrDisplay (total number of post to display ) according a category or not
		$this->nbrTotalArtDisplayed($sReq);
		$NbrTotArt = $this->TotalNbrDisplay;


		// set pagination index
		if (isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] < $NbrTotArt)
			$cPage = $_GET['page'];
		else $cPage = 1;
		
		 if ($mode == 'util') {
			$sReqSelect = 'SELECT id_art, titre_art, vignette_art, date_crea_art, date_pub_art, resum_art, keywords_art ';
			$sReqFrom 	= 'FROM blog_articles inner join blog_cat_article on id_categorie=id_cat';
			
			if ($cat == 0) $sReqWhere = ' WHERE DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null';
			else $sReqWhere = " WHERE id_categorie=$cat AND (DATE(date_pub_art) < DATE(NOW()) OR date_pub_art is null)";
			$sReqOrder 	= ' order by date_crea_art desc LIMIT ' . (($cPage-1)*$parpage) . ', ' . $parpage;
		 }
		elseif($mode == 'admin') {
			$sReqSelect = 'SELECT id_art, titre_art, vignette_art, date_crea_art, DATE_FORMAT( date_pub_art , \'%d/%m/%Y\') AS date_pub_art, resum_art, keywords_art';			
			$sReqFrom = ' FROM blog_articles inner join blog_cat_article on id_categorie=id_cat';

			//if ($cat !=0) $sReqWhere = " WHERE id_categorie=$cat";

			$sReqOrder = ' order by date_crea_art desc';
		}
		
		$sReqSelectArticles = $sReqSelect . $sReqFrom . $sReqWhere . $sReqOrder;

		$aResult = SPDO::getInstance()->query($sReqSelectArticles);		

		//On sauvegarde toutes les données dans un tableau pour pouvoir les traiter les "date de création" (méthode 'TraiteDateCreation')
		//Sinon suite à foreach d'un objet PDOStatement, les données sont supprimées.
		$this->_aPostData = $aResult->fetchAll(PDO::FETCH_ASSOC);

		// Treatment of 'creation date' for display and recording stats
		foreach ($this->_aPostData as $index => $value)
		{ 
			$sDateTraite = $this->TraiteDateCreation($value['date_crea_art']);
			//foreach manage tables by value, not reference (alternative to '& $ value' in the foreach)
			$this->_aPostData[$index]['date_crea_art'] = $sDateTraite;
			
			//Add statistics
			$aStats = $this->ReadStatsArticle($value['id_art']);
			$this->_aPostData[$index]['som-comm'] = $aStats['somme'];
		}

	}
	
	public function ReadMetaData($id_art){
		$sResult = '';
		$sMeta = '';
		
		$sReq = 'SELECT titre_art, resum_art, keywords_art FROM blog_articles WHERE id_art='.$id_art;		
		$sResult = SPDO::getInstance()->query($sReq);
		$sMeta = $sResult->fetch(PDO::FETCH_ASSOC);
		return $sMeta;
	}



	/**
	 * 
	 * /// Comments administration ///
	 *
	 */

	 /**
	  * Reading commnents
	  *
	  * @param int id du commentaire
	  * @param string 'com' (comment) ou 'rep' (response)
	  * @return array informations
	  */
	 public function ReadOneComment($id, $type){
	 	if ($type == 'com')	$sReq = "SELECT date_com,nom_com, photo_com, email_com, texte_com, id_com, id_art, photo_com, photo_type, ctrl_aff, email_valid FROM blog_comments WHERE id_com=$id ";
	 	elseif ($type == 'rep') $sReq = "SELECT * FROM blog_reply where id_rep = $id";

		$sRequete = SPDO::getInstance()->query($sReq);		
		$aRequete = $sRequete->fetch(PDO::FETCH_ASSOC); 
		return $aRequete;

	 }


	 /**
	  *	Reading the comments of the article requested
	  *	Use in the front office to display the post comments
	  *
	  * @param int $id_art Article ID 
	  * @return array $aRequete Data comments found for the request.
	  */
	 public function ReadComments($id_art){		
		$aDataClean = array();
		$sReq = 'SELECT date_com,nom_com, photo_com, texte_com, id_com, photo_type, ctrl_aff, email_valid FROM blog_comments WHERE ctrl_aff=0 or valid_com=1 and id_art='.$id_art . ' order by date_com';
		$sRequete = SPDO::getInstance()->query($sReq);
		$this->_aComments = $sRequete->fetchAll(PDO::FETCH_ASSOC);

		$aFilter = array('date_com'=>FILTER_SANITIZE_STRING
			, 'nom_com'=>FILTER_SANITIZE_STRING
			, 'photo_com'=>FILTER_UNSAFE_RAW
			, 'texte_com'=>FILTER_SANITIZE_STRING
			, 'id_com'=>FILTER_SANITIZE_NUMBER_INT
			, 'photo_type'=>FILTER_SANITIZE_STRING
			, 'ctrl_aff'=>FILTER_SANITIZE_NUMBER_INT
			, 'email_valid'=>FILTER_SANITIZE_NUMBER_INT);	
		
		foreach ($this->_aComments as $key => $aComment) {
			$aDataClean[$key] = filter_var_array($aComment, $aFilter);
		}

		return $aDataClean;

	 }

	 /**
	  * Reading all comments
	  * Use in back-office to display comments list (comments management)
	  *
	  * @return array $aRequete Table containing comments
	  */
	 public function ReadAllComments(){
	 	//We appears nevertheless those validated by the administrator (valid com = 1) to display the answers that depend on and are can be validated
	 	//(also can see the chronology of comments / responses).
	 	$sReq = "SELECT date_com,nom_com, email_com, texte_com, id_com, id_art, ctrl_aff, email_valid, valid_com FROM blog_comments WHERE ctrl_aff=1 order by id_art, date_com desc";
		$sRequete = SPDO::getInstance()->query($sReq);		
		$this->_aComments = $sRequete->fetchAll(PDO::FETCH_ASSOC); 

		$aFilter = array('date_com'=>FILTER_SANITIZE_STRING
			, 'nom_com'=>FILTER_SANITIZE_STRING
			, 'email_com'=>FILTER_VALIDATE_EMAIL
			, 'texte_com'=>FILTER_SANITIZE_STRING
			, 'id_com'=>FILTER_SANITIZE_NUMBER_INT
			, 'id_art'=>FILTER_SANITIZE_NUMBER_INT
			, 'ctrl_aff'=>FILTER_SANITIZE_NUMBER_INT
			, 'email_valid'=>FILTER_SANITIZE_NUMBER_INT
			, 'valid_com'=>FILTER_SANITIZE_NUMBER_INT);
		
		foreach ($this->_aComments as $key => $aComment) {
			$aDataClean[$key] = filter_var_array($aComment, $aFilter);
		}

		return $aDataClean;
	 }

	 /**
	  * Reading answers to comment
	  *
	  * @param int $id_comm comment Id
	  * @return array $aRequete Data answers
	  * @
	  */
	 public function ReadAnswers($id_comm, $use){
	 	$aDataClean = array();

		if ($use == 'admin') $sReq = 'SELECT id_rep, nom_rep, texte_rep, date_rep, photo_rep, email_rep, email_valid, valid_rep, ref_rep, photo_type, id_commentaire FROM blog_reply WHERE ctrl_aff=1 and valid_rep=0 and id_commentaire='.$id_comm . ' order by id_rep';
		elseif ($use == 'util') $sReq = 'SELECT id_rep, nom_rep, texte_rep, date_rep, photo_rep, email_rep, email_valid, valid_rep,ref_rep, photo_type, id_commentaire FROM blog_reply WHERE (ctrl_aff=0 or valid_rep=1) and id_commentaire='.$id_comm . ' order by id_rep';
		
		$sRequete = SPDO::getInstance()->query($sReq);
		$this->_aAnswer = $sRequete->fetchAll(PDO::FETCH_ASSOC);		

		$aFilter = array('id_rep'=>FILTER_SANITIZE_NUMBER_INT
			, 'nom_rep'=>FILTER_SANITIZE_STRING
			, 'texte_rep'=>FILTER_SANITIZE_STRING
			, 'date_rep'=>FILTER_SANITIZE_STRING
			, 'photo_rep'=>FILTER_UNSAFE_RAW
			, 'email_rep'=>FILTER_VALIDATE_EMAIL
			, 'email_valid'=>FILTER_SANITIZE_NUMBER_INT
			, 'valid_rep'=>FILTER_SANITIZE_NUMBER_INT
			, 'ref_rep'=>FILTER_SANITIZE_NUMBER_INT
			, 'photo_type'=>FILTER_SANITIZE_STRING
			, 'id_commentaire'=>FILTER_SANITIZE_NUMBER_INT);

		foreach ($this->_aAnswer as $key => $aAnswer) {
			$aDataClean[$key] = filter_var_array($aAnswer, $aFilter);
		}		

		return $aDataClean;
	 }
	 
	 /**
	  * Validate comment
	  *
	  * @param int id commentaire
	  * @return string 'com' (ccmmentaire) ou 'rep'
	  */
 		public function ConfirmValidateComments($id, $type, $email_valid, $msg_email_ok, $msg_email_ko){
			$btnOK = "admin.php?p=gest_art&a=gest_com&id=$id&t=$type&c=valid&v=$email_valid&eng=yes";
 			if ($email_valid == 0) {
 				$this -> RequestConfirmation('modif', $msg_email_ko, $btnOK, 'admin.php?p=gest_art&a=gest_com&c=init', Admin::$lang );
 			}
 			elseif ($email_valid == 1){
 				$this -> RequestConfirmation('creer', $msg_email_ok, $btnOK, 'admin.php?p=gest_art&a=gest_com&c=init', Admin::$lang );	
 			}
 		}


	 /**
	  * Validate a comment in database
	  *
	  * @param $id Comment id
	  * @param $type Comment type (Comment or answer)
	  * @param $successTitle Success message
	  * @param $dangerTitle Failur message
	  */
	 public function ValidateComment($id, $type, $successTitle, $dangerTitle){
		 if ($type == 'com') $sReq = "UPDATE blog_comments SET valid_com = :valid WHERE id_com=$id"; 
		 elseif ($type == 'rep') $sReq = "UPDATE blog_reply SET valid_rep = :valid WHERE id_rep=$id";

		 $aBindVar = array(array('type'=>PDO::PARAM_INT, ':valid'=>1));

		 $this->executeDbQuery($sReq, $aBindVar, '', 'admin.php?p=gest_art&a=gest_com&c=init', true);	
	}



	 /**
	  * Recording a comment or response (a new message or a reply to a comment)
	  * Insert into 'blog_comments' or 'blog_reply' 
	  *
	  * @param string $nom name commentator
	  * @param string $mail email
	  * @param string $siteweb 
	  * @param string $contents The contents
	  * @param int $id The id of the article (for comment) OR the id of the comment (for reply to a comment)
	  * @param string $type_comm 'new'=>comment case ; 'rep'=>answer case
	  **/
	public function RecordNewComm($nom, $mail, $siteweb, $contents, $id, $type_comm, $aMsg, $host){
		//Filtering data type $_POST
		$aPost = array('nom'=>$nom, 'mail'=>$mail, 'web'=>$siteweb, 'contenu'=>$contents ,'id'=>$id);
		$sFiltres = array('nom'=>FILTER_SANITIZE_STRING,
						'mail'=>FILTER_VALIDATE_EMAIL,
						'web'=>FILTER_SANITIZE_STRING,
						'contenu'=>FILTER_SANITIZE_STRING,
						'id'=>FILTER_VALIDATE_INT);
						
		$aDataclean = filter_var_array($aPost, $sFiltres);
		$nom  = $aDataclean['nom'];
		$mail = $aDataclean['mail'];
		$siteweb = $aDataclean['web'];
		$contents = $aDataclean['contenu'];
		$id = $aDataclean['id'];
		$id_art = (int) $_GET['id'];
		//end filtering

		//Checking if comments are controlled
		$result = SPDO::getInstance()->query('select * from blog_config');
		$aConfig = $result->fetch(PDO::FETCH_ASSOC);
		$ctr_comm = $aConfig['control_comm'];

		date_default_timezone_set('Europe/Paris');
		$dDateJour = date('Y-m-d H:i:s');
		$iValid = 0; // By default, the comment is validated.

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

		// Construction of the insert query based on whether it is a new message or reply.
		if ($type_comm == 'new') {
			$sReq = "INSERT INTO blog_comments (date_com, nom_com, email_com, siteweb_com, texte_com, valid_com, id_art, photo_com, photo_type, ctrl_aff) ";
			$sReq .= "VALUES (:datej, :nom, :mail, :web, :txt, :valid, :id, :photo, :type_photo, :ctrl_aff)";
		}
		elseif ($type_comm == 'rep' ) {		
			// Determination of "ref_rep" (itself a response to an initial comment or reply to another answer)
			if (isset($_POST['ref_rep']))
			{  
				// Answers already given a reply
				$ref=$_POST['ref_rep'];	
				//sarch ref_rep tu use for a new answer.
				for ($i=1 ; $i<100 ; $i++){
					$reqCount = 'select count(id_rep) from blog_reply where id_commentaire=' . $id . ' and ref_rep='.$ref.$i;
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
				//Answer to a initial comment. So "ref_rep" is next id
				//$id is id_com
				$iNbrRep = SPDO::getInstance()->query('select count(id_rep) from blog_reply where id_commentaire='.$id);			
				$ref_rep =  $iNbrRep->fetchAll();
				$ref_rep = (int)$ref_rep[0][0];
			}
				
			$sReq = "INSERT INTO blog_reply (date_rep, nom_rep, email_rep, siteweb_rep, texte_rep, valid_rep, id_commentaire, ref_rep, photo_rep, photo_type, ctrl_aff) ";
			$sReq .= "VALUES (:datej, :nom, :mail, :web, :txt, :valid, :id, :ref_rep, :photo, :type_photo, :ctrl_aff)";
		}


		// Insert query execution
		
		$notype=PDO::PARAM_STR;

		$aBindVar = array(
		array('type'=>$notype, ':datej'=>$dDateJour)
		,array('type'=>$notype, ':nom'=>$nom)
		,array('type'=>$notype, ':mail'=>$mail)
		,array('type'=>$notype, ':web'=>$siteweb)
		,array('type'=>$notype, ':txt'=>$contents)
		,array('type'=>$notype, ':valid'=>$iValid)
		,array('type'=>$notype, ':id'=>$id)
		,array('type'=>PDO::PARAM_LOB, ':photo'=>$img)
		,array('type'=>$notype, ':type_photo'=>$typeimg)
		,array('type'=>$notype,':ctrl_aff'=>$ctr_comm));	

		if ($type_comm == 'rep' ) array_push($aBindVar, array('type'=>$notype, ':ref_rep'=>$ref_rep));

		$resultOK = $this->executeDbQuery($sReq, $aBindVar, '', '', false);	

		unset($oImg);

		// Message according comments control 
		if ($resultOK) {
			if ($ctr_comm == 1) {
				//If comment control => send an email
				$MsgAlert = $aMsg[Admin::$lang]['msg_comments_ctrl'];
				$this->DisplayAlert('success', $aMsg[Admin::$lang]['msg_confirm'], $MsgAlert, "blog.php?id=$id_art");

				$this->ReadBlogConfig();
				
				$obj 		= $this->mail_obj; 
				$from_name		= $this->name_exp;
				$from_adr		= $this->mail_exp;
				$replay_name	= '';
				$replay_adr		= '';

				// Insert token into mail
				date_default_timezone_set('Europe/Paris');
				$sJeton = md5(uniqid(rand(), true)) . date('YmdHis');

				if ($type_comm == 'new') {
					// Determination of id_com (newly created)
					$query = SPDO::getInstance()->query('select max(id_com) as id_com from blog_comments');
					$result =  $query->fetchAll();
					$id = $result[0]['id_com'];

					$sLien = "$host/valid_comm.php?com=$id&t=$sJeton";
				}
				elseif($type_comm == 'rep'){
					// Determination of id_rep (newly created)
					$query = SPDO::getInstance()->query('select max(id_rep) as id_rep from blog_reply');
					$result = $query->fetchAll();
					$id = $result[0]['id_rep'];	

					$sLien = "$host/valid_comm.php?rep=$id&t=$sJeton";
				}
				
				// Content Construction : Replacement 'VALID' into link				
				$sLink = "<a href='$sLien'>". $aMsg[Admin::$lang]['msg_publish_confirm'] . '</a>';
				$mail_txt = $this->mail_txt;
				$pos = strpos($mail_txt, 'VALID');
				$mail_exp = str_replace ('VALID' , $sLink, $mail_txt );			

				// Token register
				if ($type_comm == 'new') $this->RecordTokenComment ($id, 'com', $sJeton);
				elseif ($type_comm == 'rep') $this->RecordTokenComment ($id, 'rep', $sJeton);

				// Send email
				$oUtil = new Utilitaires();
				$bSendOK = $oUtil -> sendEmail($mail, $obj, nl2br($mail_exp), $from_name, $from_adr, $replay_name, $replay_adr);				
				//echo "mail : $mail / from_name : $from_name / from_adr : $from_adr";
				if(!$bSendOK) $this->DisplayAlert('danger', $aMsg[Admin::$lang]['msg_conf_not_send'], '', "blog.php?id=$id_art");

			}
			elseif ($ctr_comm == 0){				
				$this->DisplayAlert('success', $aMsg[Admin::$lang]['msg_thank_comment'], '', "blog.php?id=$id_art");
			}
        }                
	}	  

 /**
  * Record a token for email authentication
  *
  * @param int $id ID comment
  * @param string $type 'com' (comment) or 'rep' (reply)
  * @return type comentaire_ici
  */
	private function RecordTokenComment ($id, $type, $token){
		 if ($type == 'com') $sReq = "UPDATE blog_comments SET jeton = :token WHERE id_com=$id"; 
		 elseif ($type == 'rep') $sReq = "UPDATE blog_reply SET jeton = :token WHERE id_rep=$id";

		 $aBindVar = array( array('type'=>PDO::PARAM_STR, ':token'=>$token) );

		 $this->executeDbQuery($sReq, $aBindVar, '', '', false);
	}
	// *** End comment administration



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
	  * Create a category in database
	  *
	  */
	 public function CreateCategory(){
		$nom_cat = filter_var($_SESSION['nom_cat'], FILTER_SANITIZE_STRING);
	 	$sReq = 'insert into blog_cat_article (nom_cat) VALUES (:nom_cat)';
	 	$aData = array(array('type'=>PDO::PARAM_STR, ':nom_cat'=>$nom_cat));
	 	$this -> executeDbQuery($sReq, $aData, '', 'admin.php?p=gest_art&a=gest_cat&c=init', true);
	 }



 /**
  * Update category in database
  *
  * @param type comentaire_ici
  * @return type comentaire_ici
  */
	public function UpdateCategory(){
		$nom_cat = filter_var($_SESSION['nom_cat'], FILTER_SANITIZE_STRING);
        $id_cat = filter_var ($_SESSION['id_cat'], FILTER_SANITIZE_NUMBER_INT);

	 	$sReq = 'UPDATE blog_cat_article set nom_cat = :nom_cat where id_cat=' . $id_cat;
	 	$aBindVar = array(array('type'=>PDO::PARAM_STR, ':nom_cat'=>$nom_cat));
	 	$this -> executeDbQuery($sReq, $aBindVar, '', 'admin.php?p=gest_art&a=gest_cat&c=init', true);

	 }	 



 /**
  * Save post data in session variables
  *
  *
  */  
	public function SaveArticlesData(){		
		// Thumbail image		
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
		if ( $image != '' ) $this->FileMove('vignette', 'BLOG', 'BACK');
	}
    


	  
	  
	/**
	 * Retrieving data sessions an item and then insert or update data
	 *
	 * @param string 'creer'=>insert into blog_articles ; 'modif'=>update articles
     */ 	  
	 public function SaveArticle($action, $msg_result_ok, $msg_result_ko){
	 	$aFilter = array('name'=>FILTER_SANITIZE_STRING
	 		, 'type'=>FILTER_SANITIZE_STRING
	 		, 'tmp_name'=>FILTER_SANITIZE_STRING
	 		, 'error'=> FILTER_SANITIZE_NUMBER_INT
	 		, 'size'=>FILTER_SANITIZE_NUMBER_INT);

	 	$aVignette = filter_var_array($_SESSION['vignette'], $aFilter);
	 	
		//Variable initialization from session variables
        $id_cat		= filter_var($_SESSION['cat'], FILTER_VALIDATE_INT);
		$id_art		= filter_var($_SESSION['id'], FILTER_VALIDATE_INT);
		$titre		= filter_var($_SESSION['titre'], FILTER_SANITIZE_STRING);
		$desc		= filter_var($_SESSION['desc'], FILTER_SANITIZE_STRING);
		$keyword	= filter_var($_SESSION['keyword'], FILTER_SANITIZE_STRING);
		$date_pub	= filter_var($_SESSION['date_pub'], FILTER_SANITIZE_STRING);
                
        $chaine = $_SESSION['texte_article'];
        $texte_article = htmlspecialchars($chaine, ENT_QUOTES, 'UTF-8');
		
		//Formatting the date of publication in the YYYY-MM-DD
		if ($date_pub != ''){
			$dt = \DateTime::createFromFormat('d/m/Y', $date_pub);
			$date_pub_art = $dt->format('Y-m-d');
		}
		else $date_pub_art = null;
		
		// Resizing image
		if($aVignette['size'] != 0){
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
			date_default_timezone_set('Europe/Paris');
			$date_crea = date('Y-m-d H:i:s');
			$sReq = "INSERT INTO blog_articles (titre_art, date_crea_art, date_pub_art, vignette_art, resum_art, keywords_art, contenu, id_categorie)";
			$sReq.= " VALUES (:titre, :date_crea, :date_pub, :vignette_art, :resum, :keywords, :contenu, :id_cat)";

			$returnLink = 'admin.php';
	
		}elseif ($action == 'modif'){
			if ($bVignette) $sReq = "UPDATE blog_articles SET titre_art=:titre, date_pub_art=:date_pub, vignette_art=:vignette_art, resum_art=:resum, keywords_art=:keywords, contenu=:contenu, id_categorie=:id_cat";
			else $sReq = "UPDATE blog_articles SET titre_art=:titre, date_pub_art=:date_pub, resum_art=:resum, keywords_art=:keywords, contenu=:contenu, id_categorie=:id_cat";

			$sReq .= " WHERE id_art=$id_art"; 

			$returnLink = 'admin.php?p=gest_art&a=modif';
		 }
		 
		//Thumbail 
		if ($bVignette) $img = $img_vign;
		else $img = null;

		// database request 
		if ($action == 'modif'){
			if ($bVignette) {
				$aBindVar = array (array('type'=>PDO::PARAM_STR, ':titre'=>$titre)
							, array('type'=>PDO::PARAM_STR, ':date_pub'=>$date_pub_art)
							, array('type'=>PDO::PARAM_STR, ':vignette_art'=>$img)
							, array('type'=>PDO::PARAM_STR, ':resum'=>$desc)
							, array('type'=>PDO::PARAM_STR, ':keywords'=>$keyword)
							, array('type'=>PDO::PARAM_STR, ':contenu'=>$texte_article)
							, array('type'=>PDO::PARAM_INT, ':id_cat'=>$id_cat));
							//array(':titre'=>$titre, ':date_pub'=>$date_pub_art, ':vignette_art'=>$img, ':resum'=>$desc, ':keywords'=>$keyword, ':contenu'=>$texte_article, ':id_cat'=>$id_cat);
			}
			else {
				$aBindVar = array (array('type'=>PDO::PARAM_STR, ':titre'=>$titre)
					, array('type'=>PDO::PARAM_STR, ':date_pub'=>$date_pub_art)
					, array('type'=>PDO::PARAM_STR, ':resum'=>$desc)					
					, array('type'=>PDO::PARAM_STR, ':keywords'=>$keyword)
					, array('type'=>PDO::PARAM_STR, ':contenu'=>$texte_article)
					, array('type'=>PDO::PARAM_INT, ':id_cat'=>$id_cat));
				//$aBindVar = array(':titre'=>$titre, ':date_pub'=>$date_pub_art, ':resum'=>$desc, ':keywords'=>$keyword, ':contenu'=>$texte_article, ':id_cat'=>$id_cat);
			}		
		}
		elseif($action == 'creer'){
			//$aBindVar = array(':titre'=>$titre, ':date_crea'=>$date_crea,':date_pub'=>$date_pub_art, ':vignette_art'=>$img, ':resum'=>$desc, ':keywords'=>$keyword, ':contenu'=>$texte_article, ':id_cat'=>$id_cat);
			$aBindVar = array(array('type'=>PDO::PARAM_STR, ':titre'=>$titre)
				, array('type'=>PDO::PARAM_STR, ':date_crea'=>$date_crea)
				, array('type'=>PDO::PARAM_STR, ':date_pub'=>$date_pub_art)
				, array('type'=>PDO::PARAM_STR, ':vignette_art'=>$img)
				, array('type'=>PDO::PARAM_STR, ':resum'=>$desc)
				, array('type'=>PDO::PARAM_STR, ':keywords'=>$keyword)
				, array('type'=>PDO::PARAM_STR, ':contenu'=>$texte_article)
				, array('type'=>PDO::PARAM_INT, ':id_cat'=>$id_cat));
		}

		 $this->executeDbQuery($sReq, $aBindVar, '', $returnLink, true);

		unset($oImage);
	 }
	 
	 
	 /*
	  *   Configuration
	  */

 /**
  * Read "blog_conf" in database (The blog configuration parameters)
  * and feed classe attributs
  *
  */
	 public function ReadBlogConfig(){
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
	 }


public function getConfigValues(){
	$aConfigValues['aff_xs']		= $this->aff_xs;
	$aConfigValues['aff_sm']		= $this->aff_sm;
	$aConfigValues['aff_md']		= $this->aff_md;
	$aConfigValues['aff_lg']		= $this->aff_lg;
	$aConfigValues['art_page']		= $this->art_page;
	$aConfigValues['ctrl_comm']		= $this->ctrl_comm;
	$aConfigValues['mail_exp']		= $this->mail_exp;
	$aConfigValues['mail_obj']		= $this->mail_obj;
	$aConfigValues['mail_txt']		= $this->mail_txt;
	$aConfigValues['name_exp']		= $this->name_exp;
	$aConfigValues['name_reply']	= $this->name_reply;
	$aConfigValues['mail_reply']	= $this->mail_reply;
	$aConfigValues['total_nbr_display'] = $this->TotalNbrDisplay;

	return $aConfigValues;

}

 /**
  * Filter $_POST article values 
  *
  */
	 public function SaveConfig(){
	 	
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
	 		);
	 	
	 	$aDataClean = filter_var_array($aPost, $aFiltres);
	 	//Fin filtrage

		$sReq= "update blog_config set aff_xs = :aff_xs, aff_sm = :aff_sm, aff_md = :aff_md, aff_lg = :aff_lg";	
		$sReq .= ", nbr_art_page = :nb_art, control_comm = :ctrl_comm, email_from = :email_from, email_objet = :email_obj, email_text = :email_txt";
		$sReq .= ", name_from = :name_exp";

		$aBindVar = array(
			array('type'=>PDO::PARAM_INT, ':aff_xs'=> $aDataClean['xs'])
			, array('type'=>PDO::PARAM_INT, ':aff_sm'=> $aDataClean['sm'])
			, array('type'=>PDO::PARAM_INT, ':aff_md'=> $aDataClean['md'])
			, array('type'=>PDO::PARAM_INT, ':aff_lg'=> $aDataClean['lg'])			
			, array('type'=>PDO::PARAM_INT, ':ctrl_comm'=>$aDataClean['ctrl_comm'])			
			, array('type'=>PDO::PARAM_STR, ':email_from'=>$aDataClean['mail_exp'])
			, array('type'=>PDO::PARAM_STR, ':email_obj'=>$aDataClean['mail_obj'])
			, array('type'=>PDO::PARAM_STR, ':email_txt'=>$aDataClean['mail_texte'])
			, array('type'=>PDO::PARAM_INT, ':nb_art'=>$aDataClean['nbr_art'])
			, array('type'=>PDO::PARAM_STR, ':name_exp'=>$aDataClean['name_exp']));

		$this->executeDbQuery($sReq, $aBindVar, '', 'admin.php?p=gest_art&a=config&c=init', true);

	 }
}