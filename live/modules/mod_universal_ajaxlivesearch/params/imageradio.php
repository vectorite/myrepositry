<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.5 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php

class JElementImageRadio extends JOfflajnFakeElementBase{
	
  var	$_name = 'ImageRadio';
	
	function universalfetchElement($name, $value, &$node)
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		// path to images directory
		$path		= JPATH_ROOT.DS.$node->attributes('directory');
		$filter		= '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
		$exclude	= $node->attributes('exclude');
		$stripExt	= $node->attributes('stripext');
		$files		= JFolder::files($path, $filter);

		$options = array ();
    
    $document =& JFactory::getDocument();
    $document->addStyleDeclaration('
      li fieldset.panelform label.radiobtn{
        width: auto;
      }
    ');

		
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
				$options[] = JHTML::_('select.option', $file, '<img style="" src="'.str_replace('\\','/',$imageurl.$file).'" />');
			}
		}


		if (!$node->attributes('hide_none'))
		{
			$options[] = JHTML::_('select.option', '-1', '- '.JText::_('None').' -');
		}
		
		return JHTML::_('select.radiolist',  $options, $name, 'class="inputbox"', 'value', 'text', $value, $this->generateId($name));
	}
	
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormImageRadio extends JElementImageRadio {}
}