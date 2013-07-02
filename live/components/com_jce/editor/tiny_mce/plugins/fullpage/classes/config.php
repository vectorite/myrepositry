<?php
/**
* @version		$Id: config.php 221 2011-06-11 17:30:33Z happy_noodle_boy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class WFFullpagePluginConfig 
{
	public function getConfig(&$settings)
	{
		$wf 	= WFEditor::getInstance();
		$model 	= JModel::getInstance('editor', 'WFModel');
		
		$doctypes = array(
			'XHTML 1.0 Transitional' 	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'XHTML 1.0 Frameset' 		=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
			'XHTML 1.0 Strict' 			=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			'XHTML 1.1' 				=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
			'HTML 4.01 Transitional' 	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
			'HTML 4.01 Strict' 			=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
			'HTML 4.01 Frameset' 		=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
			'HTML 5' 					=> '<!DOCTYPE HTML>'
		);
		
		$doctype = $wf->getParam('fullpage.default_doctype', 'HTML 4.01 Transitional', 'HTML 4.01 Transitional');
		
		$settings['fullpage_fonts'] 				= $model->getEditorFonts();
		$settings['fullpage_fontsizes'] 			= $wf->getParam('editor.theme_advanced_font_sizes', '8pt,10pt,12pt,14pt,18pt,24pt,36pt');		
		$settings['fullpage_default_doctype'] 		= isset($doctypes[$doctype]) ? addslashes($doctypes[$doctype]) : '';
		$settings['fullpage_hide_in_source_view']	= $wf->getParam('fullpage.hide_in_source_view', 0, 0);
		$settings['fullpage_default_encoding']		= $wf->getParam('fullpage.default_encoding');
		$settings['fullpage_default_xml_pi']		= $wf->getParam('fullpage.default_xml_pi', 0, 0);
		$settings['fullpage_default_font_family'] 	= $wf->getParam('fullpage.default_font_family');
		$settings['fullpage_default_title']			= $wf->getParam('fullpage.default_title', 'Untitled Document', 'Untitled Document');
		$settings['fullpage_default_font_size']		= $wf->getParam('fullpage.default_font_size');
		$settings['fullpage_default_text_color']	= $wf->getParam('fullpage.default_text_color');
	}
}
?>