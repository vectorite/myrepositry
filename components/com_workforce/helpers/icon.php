<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class JHtmlIcon
{
	static function create()
	{
		$uri = JFactory::getURI();

		$url = 'index.php?option=com_workforce&task=employeeform.add&return='.base64_encode($uri).'&id=0';
		$text = JHtml::_('image','system/new.png', JText::_('JNEW'), NULL, true);

		$button =  JHtml::_('link',JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('COM_WORKFORCE_CREATE_ITEM').'">'.$button.'</span>';
		return $output;
	}

	static function edit($object, $text_only = false, $show_over = true)
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();

		// Ignore if the state is negative (trashed).
		if ($object->state < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');

		$url	= 'index.php?option=com_workforce&task=employeeform.edit&id='.$object->id.'&return='.base64_encode($uri);
		
        if($text_only){
            $text = JText::_('JGLOBAL_EDIT');
        }else{
            $icon	= $object->state ? 'edit.png' : 'edit_unpublished.png';
            $text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);
        }

		if ($object->state == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$button = JHtml::_('link', JRoute::_($url), $text);

		if($show_over){
            $output = '<span class="hasTip" title="'.JText::_('JGLOBAL_EDIT').' :: '.$overlib.'">'.$button.'</span>';
        }else{
            $output = $button;
        }
		return $output;
	}
}