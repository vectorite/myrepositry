<?php 
/*------------------------------------------------------------------------
# mod_ajax_dockcart - AJAX Dock Cart for VirtueMart 
# ------------------------------------------------------------------------
# author    Balint Polgarfi 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php

class JElementImageRadio extends JElement{
	
  var	$_name = 'ImageRadio';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		// path to images directory
		$path		= JPATH_ROOT.DS.$node->attributes('directory');
		$size = $node->attributes('size');
		$filter		= '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
		$exclude	= $node->attributes('exclude');
		$stripExt	= $node->attributes('stripext');
		$files		= JFolder::files($path, $filter);

		$options = array ();

		
    $imageurl = JURI::root().$node->attributes('directory').'/';

		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
				if ($exclude)
				{
					if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $file ))
					{
						continue;
					}
				}
				if ($stripExt)
				{
					$file = JFile::stripExt( $file );
				}
				$options[] = JHTML::_('select.option', $file, '<img '.($size? "width='$size' height='$size'" : '').' style="cursor:pointer;" src="'.str_replace('\\','/',$imageurl.$file).'" />');
			}
		}


		if (!$node->attributes('hide_none'))
		{
			$options[] = JHTML::_('select.option', '-1', '- '.JText::_('None').' -');
		}
		
		return JHTML::_('select.radiolist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
	
}