<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="ce-search<?php echo $pageClass;?>">

<h1>
	<?php 
	echo $this->escape(
					$this->params->get('search_heading',
						$this->params->get('page_heading',JText::_('COM_CONTACTENHANCED_SEARCH_HEADING') )
					)
			); 
	?>
</h1>
<?php if($this->params->get('show_category_title', 1)) : ?>

<?php endif; ?>

<?php echo $this->loadTemplate('items'); ?>

</div>