<?php
/**
 * @version		$Id: mod_fpss.php 769 2012-01-05 17:03:14Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

defined('_JEXEC') or die('Restricted access');

// JoomlaWorks reference parameters
$mod_copyrights_start = "\n\n<!-- JoomlaWorks \"Frontpage Slideshow\" (v3.1.0) starts here -->\n";
$mod_copyrights_end		= "\n<!-- JoomlaWorks \"Frontpage Slideshow\" (v3.1.0) ends here -->\n\n";

jimport('joomla.filesystem.folder');

if(!JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss')){
	JError::raiseWarning('', JText::_('FPSS_YOU_NEED_TO_INSTALL_THE_FRONTPAGE_SLIDESHOW_COMPONENT_AS_WELL'));
	return;
}
require_once(JPATH_SITE.DS.'components'.DS.'com_fpss'.DS.'helpers'.DS.'slideshow.php');
$slides = FPSSHelperSlideshow::render($params, 'module', $module->id);

$moduleTitle = $module->title;

if(!count($slides)) return;

$document = &JFactory::getDocument();

if($document->getType() == 'html') {
	$document->addHeadLink(JRoute::_('index.php?option=com_fpss&task=module&id='.$module->id.'&format=feed&type=rss'), 'alternate', 'rel', array('type'=>'application/rss+xml', 'title'=>$moduleTitle.' '.JText::_('FPSS_MOD_RSS_FEED')));
	$document->addHeadLink(JRoute::_('index.php?option=com_fpss&task=module&id='.$module->id.'&format=feed&type=atom'), 'alternate', 'rel', array('type'=>'application/atom+xml', 'title'=>$moduleTitle.' '.JText::_('FPSS_MOD_ATOM_FEED')));
}

// Output content with template
echo $mod_copyrights_start;
require(JModuleHelper::getLayoutPath('mod_fpss', $params->get('template','Default').DS.'default'));
echo FPSSHelperSlideshow::setCrd();
echo $mod_copyrights_end;
