<?php

// Chargement de la conf
include('config.php');

// Chargement des librairies
include('LogTools.php');
include('PathTools.php');
include('BatchAbstract.php');
include('TakBin.php');

try {

	// Lancement du traitement
	$takbin = New TakBin($argv);
	$takbin->goTraitement();

} catch (Exception $ex) {
	LogTools::log("Erreur lors du traitement : " . $ex->getMessage(),'red');
}

?>