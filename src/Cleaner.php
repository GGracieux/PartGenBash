<?php

class Cleaner extends BatchAbstract{	

	// Nom du fichier de log
	protected $logFileName = 'pg-cleaner.log';

	// Action du batch
	protected $batchAction = 'Nettoyage';


	//--------------------------------
	// CONSTRUCTEUR
	//--------------------------------
	
    // Retourne les commandes de lancement valides
    protected function getUsage()
    {
    	$cmd = basename($this->argv[0]);
    	return "Usage : $cmd -all / $cmd dirName";
    }	

	
	//--------------------------------
	// TRAITEMENT
	//--------------------------------

	// Traite la convertion d'un dossier	
	protected function traiterDossier($dir)
	{
		$keepList = array();

		// On conserve tous les fichiers $dir.*.ply
		$patern = $this->workdir . $dir . DIRECTORY_SEPARATOR . "$dir.*.ply";
		foreach (glob($patern) as $file) {
		  $keepList[] = basename($file);
		}

		// On conserve tous les fichiers $dir.*.ly qui n'ont pas de ply associé
		$patern = $this->workdir . $dir . DIRECTORY_SEPARATOR . "$dir.*.ly";
		foreach (glob($patern) as $file) {
		  $fileName = basename($file);
		  $matchingPly = substr($fileName,0,strlen($fileName)-3) . '.ply';
		  if (!in_array($matchingPly, $keepList)) {
  			$keepList[] = basename($file);
		  }
		}

		// Suppression des fichiers
		$patern = $this->workdir . $dir . DIRECTORY_SEPARATOR . "*";
		foreach (glob($patern) as $file) {
		  $fileName = basename($file);
		  if (!in_array($fileName, $keepList)) {
		  	$filePath = $this->workdir . $dir . DIRECTORY_SEPARATOR . $fileName;
  			@unlink($filePath);
		  }
		}
	}

}

?>