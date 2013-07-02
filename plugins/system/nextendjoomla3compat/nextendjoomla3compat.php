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

/* Loading only for Joomla 3 and greater */
if(version_compare(JVERSION,'3.0.0','l')){
  if(!function_exists('Nextendjimport')){
    function Nextendjimport($key, $base = null){
      return jimport($key);
    }
  }
  return;
}

defined('DS') or define( 'DS', DIRECTORY_SEPARATOR );
defined('NEXTENDCOMPAT') or define( 'NEXTENDCOMPAT', dirname(__FILE__).DS.'compat'.DS.'libraries');

if(version_compare(JVERSION,'3.0.0','ge')){
  function Nextendjimport($path){
    defined('NEXTENDCOMPAT') or define( 'NEXTENDCOMPAT', dirname(__FILE__).DS.'compat'.DS.'libraries');
    $path = str_replace('joomla', 'coomla', $path);
    return JLoader::import($path, NEXTENDCOMPAT);
  }
}

class plgSystemNextendJoomla3compat extends JPlugin {
  
  var $cache = 0;

	function plgSystemNextendJoomla3compat(& $subject) {
		parent::__construct($subject);
 	}
  
  function onAfterInitialise(){
    
  }
  
}