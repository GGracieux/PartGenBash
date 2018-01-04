<?php

// Chargement de la conf
include('config.php');

// Chargement des librairies
include('LogTools.php');
include('PathTools.php');
include('BatchAbstract.php');
include('LilyGen.php');

try {

	// Lancement du traitement
	$takbin = New LilyGen($argv);
	$takbin->goTraitement();

} catch (Exception $ex) {
	LogTools::log("Erreur lors du traitement : " . $ex->getMessage(),'red');
}

?>