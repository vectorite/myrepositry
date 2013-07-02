<?php

 /**
 * @version		/** $Id: default_form.php 11845 2009-05-27 23:28:59Z robs
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 
?>
<?php if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>


	 
<?php
$formWidth	= 99; 
if($this->params->get('show_sidebar')){
	$formWidth	= $formWidth - $this->params->get('sidebar_width',40);
	?>
	<div class="contact-sidebar" 
		style="width:<?php echo ($this->params->get('sidebar_width',40)); ?>%;float:<?php echo $this->params->get('show_sidebar','right'); ?>">
		<?php  echo $this->loadTemplate('sidebar');  ?>
	</div>
	<?php 
}

?>
<div style="width:<?php echo $formWidth; ?>%;float:<?php 
		echo ($this->params->get('show_sidebar','right') == 'left' ? 'right' : 'left'); ?>">
	<div class="contact-form">
		<?php echo ceHelper::loadForm($this); ?>
	</div>
</div>
<br style="clear:both" />