<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 
?>
<div class="contact-sidebar">
<?php
	echo $this->contact->sidebar; 
	
	if ($this->params->get('show_contact_details','beforeform') == 'sidebar'){
		echo $this->loadTemplate('details');
	}	
	if ( $this->params->get( 'show_map') == 'sidebar' ){
		echo $this->loadTemplate('map');
	}
?>
</div>