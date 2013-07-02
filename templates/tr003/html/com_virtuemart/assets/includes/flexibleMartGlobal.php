<?php
/**
* 
* @Enterprise: Yagendoo Media GmbH
* @author: Yagendoo Team
* @url: http://www.yagendoo.com
* @copyright: Copyright (C) Yagendoo Media GmbH
* @license: Commercial, see LICENSE.php
* @product: Virtuemart Theme
*
*/
defined('_JEXEC') or die('Restricted access');
$app		=	JFactory::getApplication('site');
$template	=	$app->getTemplate(); 
 


$flexibleGlobalCSSpath		=	'templates/'.$template.'/html/com_virtuemart/assets/css/';
$flexibleGlobalCSSfilename	=	"flexibleVM2Global.css";

//$FlexiblePATH = 'templates/'.$template.'/html/com_virtuemart/assets/Flexible/';
//$JSspotlight = 'spotlight.js'; 
//JHTML::script($JSspotlight, $FlexiblePATH);
$document = JFactory::getDocument();
$document->addStyleSheet($flexibleGlobalCSSpath.$flexibleGlobalCSSfilename);
?>

 