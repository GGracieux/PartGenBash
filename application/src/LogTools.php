<?php

class LogTools {

	/**
	* Log un message avec une couleur (mode php cli)
	*/
	public static function log($message, $color = 'white', $addCR = true)
	{
		if (!is_array($message)) $message = array($message);
		foreach ($message as $ligne) {		 
		  echo self::getColoredString($ligne, $color);
		  if ($addCR) echo chr(10);
		} 
	}

	/**
	* Obtiens la chaine avec le code couleur
	*/
	private static function getColoredString($message, $color) {

		// Définition des couleurs
		$colors['black'] = '0;30';
		$colors['dark_gray'] = '1;30';
		$colors['blue'] = '0;34';
		$colors['light_blue'] = '1;34';
		$colors['green'] = '0;32';
		$colors['light_green'] = '1;32';
		$colors['cyan'] = '0;36';
		$colors['light_cyan'] = '1;36';
		$colors['red'] = '0;31';
		$colors['light_red'] = '1;31';
		$colors['purple'] = '0;35';
		$colors['light_purple'] = '1;35';
		$colors['brown'] = '0;33';
		$colors['yellow'] = '1;33';
		$colors['light_gray'] = '0;37';
		$colors['white'] = '1;37';

		// Retourne la chaine colorée
		return "\033[" . $colors[$color] . "m" . $message . "\033[0m";
	}

}

?>