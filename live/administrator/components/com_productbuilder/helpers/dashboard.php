<?php

/**
* product builder component
* @package productbuilder
* @version $Id:1 helpers/helper.php  11-Nov-2010 sakisTerz $
* @author Sakis Terz (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/
class pbDashboardHelper{
	
	   	function quickIconButton( $image,$link, $text,$class='',$rel='' ) {

		$lang	= &JFactory::getLanguage();
		$button = '';
		$classStr=$class ?' class="'.$class.'"':'';
		$relStr=$rel ?' rel="'.$rel.'"':'';
		if ($lang->isRTL()) {
			$button .= '<div style="float:right;">';
		} else {
			$button .= '<div style="float:left;">';
		}
		$button .=	'<div class="icon">'
				   .'<a href="'.$link.'"'.$classStr.$relStr.'>'
				   .JHTML::_('image.site',  $image, '/components/com_productbuilder/assets/images/', NULL, NULL, $text )
				   .'<span>'.$text.'</span></a>'
				   .'</div>';
		$button .= '</div>';

		return $button;
	}
  }
