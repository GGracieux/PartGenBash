<?php

class Exporter extends BatchAbstract{	

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
    	return "Usage : $cmd -all destDir/ $cmd dirName destDir";
    }	

	
	//--------------------------------
	// TRAITEMENT
	//--------------------------------

	// Traite la convertion d'un dossier	
	protected function traiterDossier($dir)
	{
		// Vérification du dossier de destination
		$dstDir = $this->argv[2] . DIRECTORY_SEPARATOR;
		if (!is_dir($dstDir)) {
			mkdir($dstDir,0777, true);
		}

		// Liste des fichiers a traiter
		$outputFiles = array(
			"$dir.mp3",
			"$dir.pdf"
		);

		// Déplacement
		foreach ($outputFiles as $srcFileName) {
			$srcFilePath = $this->workdir . $dir . DIRECTORY_SEPARATOR . $srcFileName;
			if (is_file($srcFilePath)) {
				$dstFilePath = $dstDir . $srcFileName;
				rename($srcFilePath,$dstFilePath);
			}
		}		
	}

}

?>