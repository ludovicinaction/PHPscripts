<?php
$datedeb = '12/12/2016';
$datefin = '21/12/2016';
$nom	= 'image';
$type  = 'PDO::PARAM_LOB';
$notype='NO-PDO';


$nom2	= 'image2';

$aBindVar = array(
	 array(':datedebut'=>$datedeb, 'type'=>$notype)
	 , array(':datefin'=>$datefin, 'type'=>$notype)
	 , array(':nom'=>$nom, 'type'=>$type));	

array_push($aBindVar, array(':nom2'=>$nom2, 'type'=>$notype));

//var_dump($aBindVar);