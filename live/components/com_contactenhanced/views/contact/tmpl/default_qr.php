<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 

if($this->contact->params->get('qr',0)){
	$app		= & JFactory::getApplication();
	$config 	= & JFactory::getConfig();
	$doc		= & JFactory::getDocument();
	
	//Please keep in this order
	$http	= 'http'.(ceHelper::httpIsSecure() ? 's://' : '://');
	//Please keep in this order
	//$doc->addScript($http.'www.google.com/jsapi');
	
	if($config->getValue('config.debug') OR $config->getValue('config.error_reporting') == 6143){
		$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/ce-qrcode-uncompressed.js') ;
	}else{
		$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/ce-qrcode.js') ;
	}
	
	$size		= $this->contact->params->get('qr-enabled-size',	'150');		// graphic dimensions pixels
	$ecc		= $this->contact->params->get('qr-enabled-ecc',		'L');		// ecc mode L,M,Q,H (L is lowest/H is highest)
	$type		= $this->contact->params->get('qr-enabled-type',	'vcard');	// ecc mode L,M,Q,H (L is lowest/H is highest)
	$contactId	= $this->contact->id;
	$imageContainer	= "ce-qrcode-contact-{$contactId}";
	$url		= JURI::root();
	
	$script	= " 
window.addEvent('domready', function() {
		ceQRCode.getInfo('{$imageContainer}','{$size}','{$ecc}','{$url}','{$contactId}','{$type}');
});
";
	
	$doc->addScriptDeclaration($script);
	$style= "width:{$size}px;";
	
	$html	= '<div class="ce-qrcode-container" style="'.$style.'">';
		$html	.= '<div id="'.$imageContainer.'" class="ce-qrcode"></div>';
		if($this->contact->params->get('qr-enabled-tooltip',0)	){
			$html	.= '<div class="qrcode-tootip">'.JHTML::tooltip(
							JText::_('COM_CONTACTENHANCED_QR_CODE_TOOLTIP_TEXT'),
							JText::_('COM_CONTACTENHANCED_QR_CODE_TOOLTIP_TITLE'),
							'',
							JText::_('COM_CONTACTENHANCED_QR_CODE_TOOLTIP_LABEL')
						).'</div>';	
		}
	$html	.= '</div>';
	
	
	echo $html;
} 
