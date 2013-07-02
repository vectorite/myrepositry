<?php
/**
 * @version		1.6.3
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="ce-search-category">
	<h2><?php echo $this->category->title; ?></h2>
	<?php 
		foreach ($this->items as $this->item ){
			if($this->item->catid == $this->category->id){
				echo $this->loadTemplate('item');
			}
		}
	?>
</div>
