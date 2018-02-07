<?php

abstract class BatchAbstract {	

	// arguments de lancement
	protected $argv;

	// mode d'execution
	protected $execMode;
	
	// dossier de travail
	protected $workdir;

	// Nom du fichier de log
	protected $logFileName;

	// Action du batch
	protected $batchAction;
	

	//--------------------------------
	// CONSTRUCTEUR
	//--------------------------------

	// Constructeur
    public function __construct($argv)
    {
    	// Archive les arguments
    	$this->argv = $argv;

    	// Vérifie les arguments
    	$this->execMode = $this->getExecMode();
    	if ($this->execMode == 'err') {
    		throw new Exception('Syntaxe incorrecte. ' . $this->getUsage());
    	}
		
		// Détermine le dossier de travail
		$this->getWorkDir();
		
    }

    // Retourne le mode d'execution
    protected function getExecMode()
    {
    	if (count($this->argv) == 0) return 'err';
		if (count($this->argv) > 3) return 'err';
    	if ($this->argv[1] == '-all') return 'all';
    	return 'one';
    }

	// Détermine le dossier de travail
	protected function getWorkDir()
	{
		$this->workdir = getcwd() . DIRECTORY_SEPARATOR;		
	}


	//--------------------------------
	// INITIALISATION
	//--------------------------------
	
	// Vérifie l'existence du dossier de travail
	protected function checkDirExists($dir)
	{
		$fullDir = $this->workdir . $dir;
		if (!is_dir($fullDir)) {
			throw new Exception("Dossier introuvable : " . $fullDir);
		}
	}		
	
	// retourne la liste des dossiers a convertir
	protected function getDirList()
	{
		$dirList = array();
		if ($this->execMode == 'one'){
			$dirList[] = $this->argv[1];
		} else {
			$dirList = array_filter(glob('*'), 'is_dir');
		}
		return $dirList;
	}
	

	// Execute une commande, exception si code retour <> 0
	protected function execCmd($cmd)
	{

		// Execution
		$output = array();
		$retVal = null;
		exec($cmd, $output, $retVal);
		
		// Exception si erreur
		if ($retVal != 0){
			throw new Exception('Retour commande en erreur (voir log)');
		}
	}


	//--------------------------------
	// TRAITEMENT
	//--------------------------------
	
	// Lancement du traitement
	public function goTraitement()
	{

		// Récup la liste des dossiers a convertir
		$dirs = $this->getDirList();
		
		// Parcours tous les dossiers a convertir
		foreach ($dirs as $dir)
		{
			LogTools::log($this->batchAction . ' - ' . $dir . ' : ', 'white', false);
			try {			
				$this->traiterDossier($dir);
				LogTools::log('OK', 'light_green');
			} catch (Exception $ex) {
				LogTools::log('ERREUR', 'light_red');
				$this->logError($dir,$ex);
			}
		}		
	}


	//--------------------------------
	// GESTION D'ERREUR
	//--------------------------------
	
	// Log une erreur de traitement
	protected function log($dir, $msg)
	{
		$dstFile = $this->workdir . $dir . DIRECTORY_SEPARATOR . $this->logFileName;
		@file_put_contents($dstFile, $msg, FILE_APPEND);
	}	

	// Log une erreur de traitement
	protected function logError($dir,$ex)
	{
		$this->log($dir,print_r($ex,true));
	}


	//--------------------------------
	// FONCTIONS ABSTRAITES
	//--------------------------------

    // Lancement du traitement
	abstract protected function traiterDossier($dir);

    // Retourne les commandes de lancement valides
    abstract protected function getUsage();

}

?>