<?php

class TakBin extends BatchAbstract{	

	// Template lilypond par defaut
	const DEFAULT_TEMPLATE = 'A4AutoPortrait';

	// Police sonore par defaut
	const DEFAULT_SF = SOUNDFONTS_DIR . 'Bagpipes401.SF2';

	// template courant
	private $template;

	// Nom du fichier de log
	protected $logFileName = 'pg-generate.log';

	// Action du batch
	protected $batchAction = 'Generation';


	//--------------------------------
	// CONSTRUCTEUR
	//--------------------------------

	// Constructeur
    public function __construct($argv)
    {
    	// constructeur parent
    	parent::__construct($argv);
		
		// Détermine le template courant
		$this->getTemplate();

		// Vérifie l'existence du template
		$this->checkTemplate();
    }
	
	// Détermine le template
	private function getTemplate()
	{
		if (count($this->argv) == 2) {
			$this->template = self::DEFAULT_TEMPLATE;
		} else {
			$this->template = $this->argv[2];
		}
	}
	
    // Retourne les commandes de lancement valides
    protected function getUsage()
    {
    	$cmd = basename($this->argv[0]);
    	return "Usage : $cmd -all [template]  / $cmd dirName [template]";
    }	

	
	//--------------------------------
	// TRAITEMENT
	//--------------------------------

	// Traite la convertion d'un dossier	
	protected function traiterDossier($dir)
	{
		// Verification dossier
		$this->checkDirExists($dir);

		// Initialise le dossier
		$this->resetTmpOutput($dir);
		$this->resetExpectedOutput($dir);

		// Assemble les differentes parties du fichier
		$this->importTemplate($dir);		
		$this->overloadTemplate($dir);
		$this->replaceVariables($dir);

		// Traitement des fichiers générés via lilypond
		$this->lilyPond($dir);
		$this->fluidSynth($dir);
		$this->lame($dir);

		//Nettoyage
		$this->resetTmpOutput($dir);
		$this->dispatchOutput($dir);
	}


	//--------------------------------
	// ASSEMBLAGE TEMPLATE
	//--------------------------------		
	
	// Vérifie l'existence du template
	private function checkTemplate()
	{
		$templateFull = TEMPLATE_DIR . $this->template;
		if (!is_dir($templateFull)){
			throw new Exception("Le template demandé n'existe pas (" . $this->template . ")");
		}
	}


	// Importe les fichiers du template dans le dossier courant
	private function importTemplate($dir)
	{
		$templatePath = TEMPLATE_DIR . $this->template . DIRECTORY_SEPARATOR . "*.ly";
		foreach (glob($templatePath) as $srcFile) {
		  $dstFile = $this->workdir . $dir . DIRECTORY_SEPARATOR . basename($srcFile);
		  copy($srcFile, $dstFile);
		}					
	}
	
	// Retourne la liste des fichiers d'un template
	private function getTemplateFiles()
	{
		// Recup de la liste des fichiers du template
		$templatePath = TEMPLATE_DIR . $this->template . DIRECTORY_SEPARATOR . "*.ly";
		$templateFiles = array();
		foreach (glob($templatePath) as $srcFile) {
		  $templateFiles[] = basename($srcFile);
		}
		return $templateFiles;		
	}
	
	// Surcharge les fichier importés du template
	private function overloadTemplate($dir)
	{		
		// Ecrasement des fichiers redéfinis
		$templateFiles = $this->getTemplateFiles();
		foreach ($templateFiles as $fileName){
			$srcFile = $this->workdir . $dir . DIRECTORY_SEPARATOR . "$dir.$fileName";
			if (is_file($srcFile)){
				$dstFile = $this->workdir . $dir . DIRECTORY_SEPARATOR . $fileName;
				copy($srcFile,$dstFile);
			}
		}
	}	
	
	// Surcharge les fichier importés du template
	private function replaceVariables($dir)
	{
		// Ecrasement des fichiers redéfinis
		$templateFiles = $this->getTemplateFiles();
		foreach ($templateFiles as $fileName){
			$fileName = $this->workdir . $dir . DIRECTORY_SEPARATOR . "$fileName";
			$fContent = file_get_contents($fileName);
			$fContent = str_replace('[TITRE]',basename($dir),$fContent);
			file_put_contents($fileName,$fContent);			
		}		
	}			


	//--------------------------------
	// APPEL DES VENDORS
	//--------------------------------	

	// Convertion d'un fichier (ly vers pdf et midi)
	private function lilyPond($dir)
	{
		// Composition de la commande
		$dirFull = $this->workdir . $dir;
		$cmd  = '"' . LP_BIN . '"';
		$cmd .= ' -o "' . $dirFull . '"';
		$cmd .= ' "' . $dirFull . DIRECTORY_SEPARATOR . 'render.ly"';
		$cmd .= ' > "' . $dirFull . DIRECTORY_SEPARATOR . 'lilypond.log" 2>&1';
		
		$this->execCmd($cmd);
	}


	// Convertion d'un fichier (midi vers wav)
	private function fluidSynth($dir)
	{
		// Détermine les chemins
		$dirFull = $this->workdir . $dir;
		$srcFile = $dirFull . DIRECTORY_SEPARATOR . 'render.midi';

		if (is_file($srcFile)) {

			// Composition de la commande
			$cmd  = FLUIDSYNTH_BIN;
			$cmd .= ' -F "' . $dirFull . DIRECTORY_SEPARATOR . 'render.wav"';  
			$cmd .= ' "' . self::DEFAULT_SF . '"';
			$cmd .= ' "' . $srcFile . '"'; 
			$cmd .= ' > "' . $dirFull . DIRECTORY_SEPARATOR . 'fluidsynth.log" 2>&1';
			
			$this->execCmd($cmd);
		}
	}

	// Convertion d'un fichier (wav vers mp3)
	private function lame($dir)
	{
		// Détermine les chemins
		$dirFull = $this->workdir . $dir;
		$srcFile = $dirFull . DIRECTORY_SEPARATOR . 'render.wav';

		if (is_file($srcFile)) {

			// Composition de la commande
			$dirFull = $this->workdir . $dir;
			$cmd  = LAME_BIN;
			$cmd .= ' "' . $srcFile . '"';  
			$cmd .= ' > "' . $dirFull . DIRECTORY_SEPARATOR . 'lame.log" 2>&1';
		
			$this->execCmd($cmd);
			@unlink($srcFile);
		}	
	}


	//--------------------------------
	// NETTOYAGE
	//--------------------------------	

	// Supprime les output temporaires
	private function resetTmpOutput($dir)
	{	
		// Nettoyage de la liste des objets du template
		$templatePath = TEMPLATE_DIR . $this->template . DIRECTORY_SEPARATOR . "*.ly";
		foreach (glob($templatePath) as $srcFile) {
		  $dstFile = $this->workdir . $dir . DIRECTORY_SEPARATOR . basename($srcFile);
		  @unlink($dstFile);
		}		

		// Suppression des logs et fichiers intermediaires
		$files = array(
			$this->logFileName,
			'lilypond.log',
			'fluidsynth.log',
			'lame.log',
			'render.wav',
			'render.midi'
			);
		foreach ($files as $file)
		{
			$filepath = $this->workdir . $dir . DIRECTORY_SEPARATOR . $file;		
			@unlink($filepath);
		}
	}
	
	// Supprime les output attendus
	private function resetExpectedOutput($dir)
	{
		foreach (array('pdf','midi','mp3') as $ext) {
			$filepath = $this->workdir . $dir. DIRECTORY_SEPARATOR . "$dir.$ext";
			@unlink($filepath);
		}		
	}	
	
	// Dispatch les fichiers produits de temp vers output
	private function dispatchOutput($dir)
	{
		foreach (array('pdf','midi','mp3') as $ext) {
			$srcFile = $this->workdir . $dir. DIRECTORY_SEPARATOR . "render.$ext";
			if (is_file($srcFile)) {
				$dstFile = $this->workdir . $dir. DIRECTORY_SEPARATOR . "$dir.$ext";
				rename($srcFile ,$dstFile);
			}
		}	
	}	

}

?>