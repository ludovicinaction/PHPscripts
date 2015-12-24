<?php

/**
 * Singleton de connexion à la base.
 * @package COMMON
 * @see http://www.apprendre-php.com/tutoriels/tutoriel-47-classe-singleton-d-accs-aux-sgbd-intgrant-pdo.html
 * @see https://github.com/DaRkD0G/Singleton_PDO_PHP/blob/master/SPDO.php
 */
class SPDO
{
  /**
   * Instance de la classe PDO
   *
   * @var PDO
   * @access private
   */ 
  private $PDOInstance = null;
 
   /**
   * Instance de la classe SPDO
   *
   * @var SPDO
   * @access private
   * @static
   */ 
  private static $instance = null;
 
  /**
   * Constante: nom d'utilisateur de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_USER = 'connect_user';
 
  /**
   * Constante: hôte de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_HOST = 'localhost';
 
  /**
   * Constante: hôte de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_PASS = 'azerty';
 
  /**
   * Constante: nom de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_DTB = 'site_bdd';
 
  /**
   * Constructeur
   *
   * @param void
   * @return void
   * @see PDO::__construct()
   * @access private
   */
  private function __construct()
  {
	$this->PDOInstance = new PDO('mysql:dbname=' . self::DEFAULT_SQL_DTB.';host=' . self::DEFAULT_SQL_HOST,self::DEFAULT_SQL_USER, self::DEFAULT_SQL_PASS, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 
    $this->PDOInstance -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
 
   /**
    * Crée et retourne l'objet SPDO
    *
    * @access public
    * @static
    * @param void
    * @return SPDO $instance
    */
  public static function getInstance()
  {  
    if(is_null(self::$instance))
    {
      self::$instance = new SPDO();
    }
    return self::$instance;
  }
 
  /**
   * Exécute une requête SQL avec PDO
   *
   * @param string $query La requête SQL
   * @return PDOStatement Retourne l'objet PDOStatement
   */
  public function query($query)
  {
    return $this->PDOInstance->query($query);
  }
 

  public function exec($query)
  {
    return $this->PDOInstance->exec($query);
  }

  public function prepare($query)
  {
    return $this->PDOInstance->prepare($query);
  }
  

  

  
  
 /* 
  public function prepare($req, $tbValeur)
  { //Il faut s'inspirer pour les requete de maj (update/insert) de la méthode "queryMany" du fichier SPDO2.class.php
	  $bdd = self::getInstance();
	  $res = $bdd->prepare($req);   
	  $res = execute($tbValeur);
	  $res->setFetchMode(PDO::FETCH_BOTH);
	  return $res;
  }
  */
  
}