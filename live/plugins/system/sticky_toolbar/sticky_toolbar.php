<?php
/*-------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com
# -------------------------------------------------------------------------
# @ author    Roland Soos
# @ copyright Copyright (C) 2012 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class  plgSystemSticky_Toolbar extends JPlugin
{
	function plgSystemSticky_Toolbar(&$subject, $config){
		parent::__construct($subject, $config);
	}

	function onAfterDispatch(){
    $app = &JFactory::getApplication();
    if($app->isSite()) return;
    $document =& JFactory::getDocument();
    $j17 = 0;
    DojoLoader::r('dojo.dojo');
    if(version_compare(JVERSION,'1.6.0','ge')) {
      DojoLoader::addScriptFile('/plugins/system/'.$this->_name.'/'.$this->_name.'.js');
      $document->addStyleSheet(JURI::base().'../plugins/system/'.$this->_name.'/'.$this->_name.'.css');
      $j17 = 1;
    }else{
      DojoLoader::addScriptFile('/plugins/system/'.$this->_name.'.js');
      $document->addStyleSheet(JURI::base().'../plugins/system/'.$this->_name.'.css');
      $j17 = 0;
    }
    DojoLoader::addScript('
      var stickyToolbar = new StickyToolbar({
        joomla17 : '.$j17.'
      });
    ');
  }
    
}