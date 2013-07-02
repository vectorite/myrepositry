<?php
/*------------------------------------------------------------------------
# plg_mainpage - Pushes the component's content to a DIV tag
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ) ;

jimport( 'joomla.plugin' ) ;
	$app = JFactory::getApplication();
if (!$app->isAdmin()) {
  // Register search function inside Joomla's API
  $app->registerEvent( 'onAfterDispatch', 'addIdToMainPage' );
}

function addIdToMainPage() {
  if (@$_REQUEST['format'] != 'raw') {
  	$document =& JFactory::getDocument();
  	$buff = $document->getBuffer('component');
  	$document->setBuffer('<div id="WWMainPage">'.$buff.'</div>', 'component');
	} 
  
  if(@$_REQUEST['only_page'] == 1) {
    echo $document->getBuffer('component');
    exit;
  }
}