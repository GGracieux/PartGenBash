<?php

// Definition des chemins
define('APP_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);
define('TEMPLATE_DIR', APP_ROOT . 'template' . DIRECTORY_SEPARATOR);
define('SOUNDFONTS_DIR', APP_ROOT . 'soundfonts' . DIRECTORY_SEPARATOR);
define('LP_BIN_WIN', APP_ROOT . 'vendors\win\LilyPond\usr\bin\lilypond');
define('LP_BIN_MAC', APP_ROOT . 'vendors/macos/LilyPond.app/Contents/Resources/bin/lilypond');
define('FLUIDSYNTH_BIN', 'fluidsynth');
define('LAME_BIN', 'lame');

// Définition du chemin de lily bin fonction de l'os
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	define('LP_BIN', LP_BIN_WIN);
} else {
	define('LP_BIN', LP_BIN_MAC);
}

?>