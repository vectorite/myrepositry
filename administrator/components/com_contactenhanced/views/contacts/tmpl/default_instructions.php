<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;



?>
<div>
	<fieldset>
		<legend><?php echo JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_NEXT_STEP'); ?></legend>
		<ol>
		<?php 
			if (count($this->categories) < 1) {
				
				
				echo '<li>';
					echo JHtml::_('link'
						,'index.php?option=com_categories&view=categories&extension=com_contactenhanced'
						, JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_CREATE_CATEGORY')
						);
				echo '</li>';
			}
			echo '<li>';
				echo JHtml::_('link'
					,'index.php?option=com_contactenhanced&view=contact&layout=edit'
					, JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_CREATE_CONTACT')
					);
			echo '</li>  ';
			
			echo JText::_('COM_CONTACTENHANCED_OR');
			
			echo '<li>';
					echo JHtml::_('link'
						,'index.php?option=com_contactenhanced&task=import'
						, JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_IMPORT_CONTACTS_AND_CATEGORIES')
						);
				echo '</li>';
				
		?>
		</ol>
	</fieldset>
</div>
