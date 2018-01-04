<?php

class PathTools {

	/**
	* Crée un dossier s'il n'existe pas
	*/
	public static function createDirIfNotExist($path)
	{
		if (is_dir($path)) return true;
		if (!mkdir($path, 0777, true)) throw new Exception("Impossible de créer le dossier $path");
		return true;
	}

	/**
	* Suppression récursive d'un dossier et de son contenu
	*/
	public static function rrmDir($path)
	{
	    if (is_dir($path) === true) 
	    {
	        $files = array_diff(scandir($path), array('.', '..'));
	        foreach ($files as $file) {
	            self::rrmDir(realpath($path) . '/' . $file);
	        }
	        return rmdir($path);
	    } 
	    else if (is_file($path) === true)
	    {
	        return unlink($path);
	    }
	    return false;
	}

	/**
	* suprime et recrée un dossier
	*/
	public static function resetDir($path) 
	{
		self::rrmDir($path);
		self::createDirIfNotExist($path);
	}

}

?>