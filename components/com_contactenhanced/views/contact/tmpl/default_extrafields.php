<?php

/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<?php if (	($this->params->get('show_extrafield_1') AND $this->contact->extra_field_1)
			|| ($this->params->get('show_extrafield_2') AND $this->contact->extra_field_2)
			|| ($this->params->get('show_extrafield_3') AND $this->contact->extra_field_3)
			|| ($this->params->get('show_extrafield_4') AND $this->contact->extra_field_4)
			|| ($this->params->get('show_extrafield_5') AND $this->contact->extra_field_5)
			|| ($this->params->get('show_extrafield_6') AND $this->contact->extra_field_6)
			|| ($this->params->get('show_extrafield_7') AND $this->contact->extra_field_7)
			|| ($this->params->get('show_extrafield_8') AND $this->contact->extra_field_8)
			|| ($this->params->get('show_extrafield_9') AND $this->contact->extra_field_9)
			|| ($this->params->get('show_extrafield_10') AND $this->contact->extra_field_10)
			) : ?>
	<div class="contact-extrafields">
	<?php if ($this->params->get('show_extrafield_1') AND $this->contact->extra_field_1) : ?>
		<span class="contact-extrafield-1" >
			<?php echo $this->contact->extra_field_1; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_2') AND $this->contact->extra_field_2) : ?>
		<span class="contact-extrafield-2" >
			<?php echo $this->contact->extra_field_2; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_3') AND $this->contact->extra_field_3) : ?>
		<span class="contact-extrafield-3" >
			<?php echo $this->contact->extra_field_3; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_4') AND $this->contact->extra_field_4) : ?>
		<span class="contact-extrafield-4" >
			<?php echo $this->contact->extra_field_4; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_5') AND $this->contact->extra_field_5) : ?>
		<span class="contact-extrafield-5" >
			<?php echo $this->contact->extra_field_5; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_6') AND $this->contact->extra_field_6) : ?>
		<span class="contact-extrafield-6" >
			<?php echo $this->contact->extra_field_6; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_7') AND $this->contact->extra_field_7) : ?>
		<span class="contact-extrafield-7">
			<?php echo $this->contact->extra_field_7; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_8') AND $this->contact->extra_field_8) : ?>
		<span class="contact-extrafield-8" >
			<?php echo $this->contact->extra_field_8; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_9') AND $this->contact->extra_field_9) : ?>
		<span class="contact-extrafield-9" >
			<?php echo $this->contact->extra_field_9; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->params->get('show_extrafield_10') AND $this->contact->extra_field_10) : ?>
		<span class="contact-extrafield-10" >
			<?php echo $this->contact->extra_field_10; ?>
		</span>
	<?php endif; ?>
	</div>
<?php endif; ?>
