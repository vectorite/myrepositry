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

//echo ceHelper::print_r($this->contact); exit;
?>
<?php if (($this->params->get('address_check') > 0) &&  ($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
	<div class="contact-address">
	<?php 
		if ($this->params->get( 'qr') AND $this->params->get( 'qr-enabled-location', 'address') == 'address') {
			echo $this->loadTemplate('qr');
		}
	?>
	<?php if ($this->params->get('address_check') > 0) : ?>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_address'); ?>
		</span>
		<address>
	<?php endif; ?>
	<?php if ($this->contact->address && $this->params->get('show_street_address',1)) : ?>
		<span class="contact-street">
			<?php echo nl2br($this->contact->address); ?>
		</span>
	<?php endif; ?>
	<?php if ($this->contact->suburb && $this->params->get('show_suburb',1)) : ?>
		<span class="contact-suburb">
			<?php echo $this->contact->suburb; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->contact->state && $this->params->get('show_state',1)) : ?>
		<span class="contact-state">
			<?php echo $this->contact->state; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->contact->postcode && $this->params->get('show_postcode',1)) : ?>
		<span class="contact-postcode">
			<?php echo $this->contact->postcode; ?>
		</span>
	<?php endif; ?>
	<?php if ($this->contact->country && $this->params->get('show_country',1)) : ?>
		<span class="contact-country">
			<?php echo $this->contact->country; ?>
		</span>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->params->get('address_check') > 0) : ?>
	</address>
	</div>
<?php endif; ?>

<?php if($this->params->get('show_email') 
			|| $this->params->get('show_telephone')
			|| $this->params->get('show_fax')
			|| $this->params->get('show_mobile')
			|| $this->params->get('show_webpage')
			|| $this->params->get('show_skype')
			|| $this->params->get('show_twitter')
			|| $this->params->get('show_facebook')
			|| $this->params->get('show_linkedin')  
		) : ?>
	<div class="contact-contactinfo">
	<?php 
		if ($this->params->get( 'qr') AND $this->params->get( 'qr-enabled-location', 'address') == 'phone') {
			echo $this->loadTemplate('qr');
		}
	?>
<?php endif; ?>
<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_email'); ?>
		</span>
		<span class="contact-emailto">
			<?php if($this->params->get('show_email') == 'link'):?>
				<?php echo JHtml::_('link', 'mailto:'.$this->contact->email_to, $this->contact->email_to); ?>
			<?php else: ?>
				<?php echo $this->contact->email_to; ?>
			<?php endif; ?>
		</span>
	</p>
<?php endif; ?>

<?php if ($this->contact->telephone && $this->params->get('show_telephone',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_telephone'); ?>
		</span>
		<span class="contact-telephone">
			<?php echo nl2br($this->contact->telephone); ?>
		</span>
	</p>
<?php endif; ?>
<?php if ($this->contact->fax && $this->params->get('show_fax',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_fax'); ?>
		</span>
		<span class="contact-fax">
		<?php echo nl2br($this->contact->fax); ?>
		</span>
	</p>
<?php endif; ?>
<?php if ($this->contact->mobile && $this->params->get('show_mobile',1)) :?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_mobile'); ?>
		</span>
		<span class="contact-mobile">
			<?php echo nl2br($this->contact->mobile); ?>
		</span>
	</p>
<?php endif; ?>
<?php if ($this->contact->skype && $this->params->get('show_skype',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_skype'); ?>
		</span>
		<span class="contact-skype">
			<a href="skype:<?php echo $this->contact->skype; ?>?call" 
				title="<?php echo JText::_('COM_CONTACTENHANCED_SKYPE_MAKE_A_CALL')?>" 
				target="_blank" rel="nofollow">
			<?php echo $this->contact->skype; ?></a>
		</span>
	</p>
<?php endif; ?>

<?php if ($this->contact->twitter && $this->params->get('show_twitter',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_twitter'); ?>
		</span>
		<span class="contact-twitter"> 
			<a href="<?php echo (substr($this->contact->twitter,0,4) == 'http' 
									? $this->contact->twitter 
									: 'http://twitter.com/#!/'.$this->contact->twitter); ?>" 
				title="<?php echo JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TWITTER_LABEL')?>" 
				target="_blank" rel="nofollow">@<?php echo $this->contact->twitter; ?></a>
		</span>
	</p>
<?php endif; ?>

<?php if ($this->contact->facebook && $this->params->get('show_facebook',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_facebook'); ?>
		</span>
		<span class="contact-facebook">
			<a href="<?php echo (substr($this->contact->facebook,0,4) == 'http' 
									? $this->contact->facebook 
									: 'https://facebook.com/'.$this->contact->facebook); ?>" 
				title="<?php echo JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FACEBOOK_LABEL')?>" 
				target="_blank" rel="nofollow">
			<?php echo $this->contact->facebook; ?></a>
		</span>
	</p>
<?php endif; ?>

<?php if ($this->contact->linkedin && $this->params->get('show_linkedin',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_linkedin'); ?>
		</span>
		<span class="contact-linkedin">
			<a href="<?php echo $this->contact->linkedin; ?>" 
				title="<?php echo JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_LINKEDIN_LABEL')?>" 
				target="_blank" rel="nofollow">
			<?php echo $this->contact->linkedin; ?></a>
		</span>
	</p>
<?php endif; ?>

<?php if ($this->contact->webpage && $this->params->get('show_webpage',1)) : ?>
	<p>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_website'); ?>
		</span>
		<span class="contact-webpage">
			<a href="<?php echo $this->contact->webpage; ?>" title="<?php echo $this->contact->webpage; ?>" target="_blank">
			<?php 
				if($this->params->get('show_webpage') == 'trim'){
					 echo ceHelper::trimURL($this->contact->webpage); 
				}elseif($this->params->get('show_webpage') == 'label'){
					 echo JText::_('COM_CONTACTENHANCED_WEBPAGE_LABEL'); 
				}else{
					echo $this->contact->webpage;
				}
			?></a>
		</span>
	</p>
<?php endif; ?>
<?php if($this->params->get('show_email') 
			|| $this->params->get('show_telephone')
			|| $this->params->get('show_fax')
			|| $this->params->get('show_mobile')
			|| $this->params->get('show_webpage')
			|| $this->params->get('show_skype')
			|| $this->params->get('show_twitter')
			|| $this->params->get('show_facebook')
			|| $this->params->get('show_linkedin')  
		) : ?>
	</div>
<?php endif; ?>
