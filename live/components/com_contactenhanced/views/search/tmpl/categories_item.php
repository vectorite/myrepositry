<?php
/**
 * @version		1.6.3
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$this->item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($this->item->slug, $this->item->catid));
	
?>
<div class="ce-search-category-item">
<?php 
	if($this->params->get( 'show_contact_image' )  AND $this->item->image ){
		$image = JHTML::_('image',  JURI::base(). $this->item->image, JText::sprintf('COM_CONTACTENHANCED_CONTACT_IMAGE_ALT',$this->item->name ), array('align' => 'middle', 'class'=> 'ce-contact-img-cat'));
		echo JHTML::_('link',$this->item->link,$image, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
	} 
?>
<h3><?php echo JHTML::_('link',$this->item->link,$this->item->name, array('class'=>'ce-contact'.$this->params->get( 'pageclass_sfx' )) ); ?></h3>


	<?php if ($this->params->get('show_position_headings') AND $this->item->con_position) : ?>
		<p><span class="contact-position">
			<?php echo $this->item->con_position; ?>
		</span></p>
	<?php endif; ?>

	<?php if ($this->params->get('show_email_headings') AND $this->item->email_to) : ?>
		<p>
			<span class="<?php echo $this->params->get('marker_class'); ?>" >
				<?php echo $this->params->get('marker_email'); ?>
			</span>
			<span class="contact-emailto">
				<?php echo $this->item->email_to; ?>
			</span>
		</p>
	<?php endif; ?>

	<?php if ($this->params->get('show_telephone_headings') AND $this->item->telephone) : ?>
		<p>
			<span class="<?php echo $this->params->get('marker_class'); ?>" >
				<?php echo $this->params->get('marker_telephone'); ?>
			</span>
			<span class="contact-telephone">
				<?php echo $this->item->telephone; ?>
			</span>
		</p>
	<?php endif; ?>

	<?php if ($this->params->get('show_mobile_headings') AND $this->item->mobile) : ?>
		<p>
			<span class="<?php echo $this->params->get('marker_class'); ?>" >
				<?php echo $this->params->get('marker_mobile'); ?>
			</span>
			<span class="contact-mobile">
				<?php echo $this->item->mobile; ?>
			</span>
		</p>
	<?php endif; ?>

	<?php if ($this->params->get('show_fax_headings') AND $this->item->fax) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_fax'); ?>
		</span>
		<span class="contact-fax">
			<?php echo $this->item->fax; ?>
		</span>
	</p>
	<?php endif; ?>
	<?php if (
					($this->params->get('show_street_address_headings',1)	AND $this->item->address)
				OR	($this->params->get('show_suburb_headings')		AND $this->item->suburb)
				OR 	($this->params->get('show_state_headings')		AND $this->item->state)
				OR	($this->params->get('show_country_headings')	AND $this->item->country)
			) : ?>
	<div class="contact-address">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_address'); ?>
		</span>
		<?php if ($this->params->get('show_street_address_headings') AND $this->item->address) : ?>
		<span class="contact-street">
			<?php echo $this->item->address; ?>
		</span>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_suburb_headings') AND $this->item->suburb) : ?>
		<span class=contact-suburb>
			<?php echo $this->item->suburb; ?>
		</span>
		<?php endif; ?>
	
		<?php if ($this->params->get('show_state_headings') AND $this->item->state) : ?>
		<span class="contact-state">
			<?php echo $this->item->state; ?>
		</span>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_postcode_headings') AND $this->item->postcode) : ?>
		<span class="contact-postcode">
			<?php echo $this->item->postcode; ?>
		</span>
		<?php endif; ?>
	
		<?php if ($this->params->get('show_country_headings') AND $this->item->country) : ?>
		<span class="contact-country">
			<?php echo $this->item->country; ?>
		</span>
		<?php endif; ?>
	</div>		
	<?php endif; ?>
</div>