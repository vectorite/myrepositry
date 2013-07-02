<?php
// kein direkter Zugriff
defined('_JEXEC') or die('Restricted access');
// Einlesen des basis Controllers
require_once (JPATH_COMPONENT.DS.'controller.php');
// Einlesen weitere Controller
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
// Einen eigenen Controller erzeugen
$classname	= 'searchuserlistController'.$controller;
$controller = new $classname( );
// Nachsehen, ob Parameter angekommen sind (Requests)
$controller->execute( JRequest::getVar('task'));
// Umleitung innerhalb des Controllers
$controller->redirect();
?>