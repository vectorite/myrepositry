<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'image.php');
?>
<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_CONTACTENHANCED_NO_ARTICLES'); ?>	 </p>
<?php else : ?>

<?php echo $this->loadTemplate('contact'); ?>

<ul id="ce-thumbnails">
	<?php foreach($this->items as $i => $item) : ?>
		<?php 
		if (!$item->image){
			$item->image	= 'components/com_contactenhanced/assets/images/no-contact-image.png';
		}
		$images[]	= JURI::root().$item->image;
		$item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catid));
		$image		= ceRenderImage(	$item->name,
										$item->image,
										$this->params, 
										$this->params->get('thumbnail_width',130).'px',
										$this->params->get('thumbnail_height',0).'px'
										);
		$attributes	= array(
							'class'		=> 'ce-contact-id-'.$item->id,
							"onclick"	=> "ceCatThumb.getInfo({$item->id},".JRequest::getVar("Itemid").",'".JURI::base()."'); return false;"
						);
		?>
		<li><?php
			if($this->params->get('show_name_heading')){
				echo '<h3>'.$item->name.'</h3>';
			}
			echo JHtml::_('link', $item->link, $image,$attributes ); 
		?></li>

	<?php endforeach; ?>
</ul>
<?php 
$this->doc->addScriptDeclaration("window.addEvent('domready',function(){var img=Asset.images(['".(implode("','",$images))."']);});");
?>
<?php endif; ?>

<br style="clear:both" />

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