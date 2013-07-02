<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::core();

JHTML::_('behavior.mootools');
JHTML::_('behavior.tooltip');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_CONTACTENHANCED_NO_CONTACTS'); ?>	 </p>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->params->get('show_pagination_limit')) : ?>
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</fieldset>
<?php endif; ?>
	<table class="category">
		<?php if ($this->params->get('show_headings')) : ?>
		<thead><tr>
			
			<th class="item-title">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_CONTACT_EMAIL_NAME', 'a.name', $listDirn, $listOrder); ?>
			</th>
			<?php if ($this->params->get('show_position_headings')) : ?>
			<th class="item-position">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_POSITION', 'a.con_position', $listDirn, $listOrder); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_email_headings')) : ?>
			<th class="item-email">
				<?php echo JText::_('JGLOBAL_EMAIL'); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_telephone_headings')) : ?>
			<th class="item-phone">
				<?php echo JText::_('COM_CONTACTENHANCED_TELEPHONE'); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_mobile_headings')) : ?>
			<th class="item-phone">
				<?php echo JText::_('COM_CONTACTENHANCED_MOBILE'); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_fax_headings')) : ?>
			<th class="item-phone">
				<?php echo JText::_('COM_CONTACTENHANCED_FAX'); ?>
			</th>
			<?php endif; ?>
			
			<?php if ($this->params->get('show_street_address_headings')) : ?>
			<th class="item-street-address">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_ADDRESS', 'a.address', $listDirn, $listOrder); ?>
			</th>
			<?php endif; ?>
					
					
			<?php if ($this->params->get('show_suburb_headings')) : ?>
			<th class="item-suburb">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_SUBURB', 'a.suburb', $listDirn, $listOrder); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_state_headings')) : ?>
			<th class="item-state">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_STATE', 'a.state', $listDirn, $listOrder); ?>
			</th>
			<?php endif; ?>
			
			
			<?php if ($this->params->get('show_postcode_headings')) : ?>
			<th class="item-postcode">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_POSTCODE', 'a.postcode', $listDirn, $listOrder); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_country_headings')) : ?>
			<th class="item-country">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_COUNTRY', 'a.country', $listDirn, $listOrder); ?>
			</th>
			<?php endif; ?>
			
			<?php if ($this->params->get('show_webpage_headings')) : ?>
			<th class="item-webpage">
				<?php echo JText::_('COM_CONTACTENHANCED_WEBPAGE'); ?>
			</th>
			<?php endif; ?>
			
			
			</tr>
		</thead>
		<?php endif; ?>

		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<?php if ($this->items[$i]->published == 0) : ?>
					<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
				<?php else: ?>
					<tr class="cat-list-row<?php echo $i % 2; ?>" >
				<?php endif; ?>

					<td class="item-title">
						<?php 
						$item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catid));
						if($this->params->get( 'show_contact_image' ) == '1' AND $item->image ){
							$image = JHTML::_('image',  JURI::base(). $item->image, JText::sprintf('COM_CONTACTENHANCED_CONTACT_IMAGE_ALT',$item->name ), array('align' => 'middle', 'class'=> 'ce-contact-img-cat'));
							echo JHTML::_('link',$item->link,$image, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
							echo JHTML::_('link',$item->link,$item->name, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
						}elseif ($this->params->get( 'show_contact_image','tooltip' ) == 'tooltip' AND $item->image){
							$image = JHTML::_('image',  JURI::base(). $item->image, JText::sprintf('COM_CONTACTENHANCED_CONTACT_IMAGE_ALT',$item->name ), array('align' => 'middle', 'class'=> 'ce-contact-img-cat'));
							$image	= JHTML::tooltip($image,$item->name,'',$item->name);
							echo JHTML::_('link',$item->link,$image, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
						}else{
							echo JHTML::_('link',$item->link,$item->name, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
						} 
					?>
					</td>

					<?php if ($this->params->get('show_position_headings')) : ?>
						<td class="item-position">
							<?php echo $item->con_position; ?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_email_headings')) : ?>
						<td class="item-email">
							<?php echo $item->email_to; ?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_telephone_headings')) : ?>
						<td class="item-phone">
							<?php echo $item->telephone; ?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_mobile_headings')) : ?>
						<td class="item-phone">
							<?php echo $item->mobile; ?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_fax_headings')) : ?>
					<td class="item-phone">
						<?php echo $item->fax; ?>
					</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_street_address_headings')) : ?>
					<td class="item-street-address">
						<?php echo $item->address; ?>
					</td>
					<?php endif; ?>
					
					<?php if ($this->params->get('show_suburb_headings')) : ?>
					<td class="item-suburb">
						<?php echo $item->suburb; ?>
					</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_state_headings')) : ?>
					<td class="item-state">
						<?php echo $item->state; ?>
					</td>
					<?php endif; ?>
					
					
					<?php if ($this->params->get('show_postcode_headings')) : ?>
					<td class="item-postcode">
						<?php echo $item->postcode; ?>
					</td>
					<?php endif; ?>
					
					<?php if ($this->params->get('show_country_headings')) : ?>
					<td class="item-country">
						<?php echo $item->country; ?>
					</td>
					<?php endif; ?>
					
					<?php if ($this->params->get('show_webpage_headings')) : ?>
					<td class="item-webpage">
						<?php if ($item->webpage): ?>
							<a href="<?php echo $item->webpage; ?>" title="<?php echo $item->webpage; ?>" target="_blank">
							<?php 
								if($this->params->get('show_webpage_headings') == 'trim'){
									 echo ceHelper::trimURL($item->webpage); 
								}elseif($this->params->get('show_webpage_headings') == 'label'){
									 echo JText::_('COM_CONTACTENHANCED_WEBPAGE_LABEL'); 
								}else{
									echo $item->webpage;
								}
							?></a>
						<?php endif;  ?>
					</td>
					<?php endif; ?>

				</tr>
			<?php endforeach; ?>

		</tbody>
	</table>

	<?php if ($this->params->get('show_pagination')) : ?>
	<div class="pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<p class="counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
		<?php endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</div>
</form>
<?php endif; ?>