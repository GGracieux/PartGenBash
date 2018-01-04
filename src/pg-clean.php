<?php

// Chargement de la conf
include('config.php');

// Chargement des librairies
include('LogTools.php');
include('PathTools.php');
include('BatchAbstract.php');
include('Cleaner.php');

try {

	// Lancement du traitement
	$takbin = New Cleaner($argv);
	$takbin->goTraitement();

} catch (Exception $ex) {
	LogTools::log("Erreur lors du traitement : " . $ex->getMessage(),'red');
}

?>