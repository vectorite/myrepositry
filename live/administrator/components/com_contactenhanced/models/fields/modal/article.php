<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 

$lang	= JFactory::getLanguage();
$lang->load('com_content',JPATH_ADMINISTRATOR);

$doc	= JFactory::getDocument();
$doc->addStyleDeclaration('#jform_params_media_article_id_name{width:160px}');
		
require_once JPATH_ADMINISTRATOR.'/components/com_content/models/fields/modal/article.php';