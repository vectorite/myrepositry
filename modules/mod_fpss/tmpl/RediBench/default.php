<?php
/**
 * @version		$Id: default.php 766 2012-01-05 12:05:29Z lefteris.kavadas $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="fpssContainer<?php echo $module->id; ?>" class="fpss-container fpss-template-simple textEffectSlideUp">
	<div class="slides-wrapper">
		<div class="slide-loading"></div>
		<div class="slides">
			<?php foreach($slides as $slide): ?>
			<div class="slide">
				<a<?php echo $slide->target; ?> href="<?php echo $slide->link; ?>" class="slide-link">
					<span style="background:url(<?php echo $slide->mainImage; ?>) no-repeat;">
						<img src="<?php echo $slide->mainImage; ?>" alt="<?php echo $slide->altTitle; ?>" />
					</span>
				</a>
				<?php if($slide->content): ?>
				<div class="slidetext">
					<?php if($slide->params->get('title')): ?>
					<h1><a<?php echo $slide->target; ?> href="<?php echo $slide->link; ?>"><?php echo $slide->title; ?></a></h1>
					<?php endif; ?>
	
					<?php if($slide->params->get('category') && $slide->category): ?>
					<h2><?php echo $slide->category; ?></h2>
					<?php endif; ?>
	
					<?php if($slide->params->get('tagline') && $slide->tagline): ?>
					<h3><?php echo $slide->tagline; ?></h3>
					<?php endif; ?>
					
					<?php if($slide->params->get('author') && $slide->author): ?>
					<h4><?php echo JText::_('FPSS_MOD_BY'); ?> <?php echo $slide->author; ?></h4>
					<?php endif; ?>
	
					<?php if($slide->params->get('text') && $slide->text): ?>
					<p><?php echo $slide->text; ?></p>
					<?php endif; ?>
	
					<?php if($slide->params->get('readmore') && $slide->link): ?>
					<a<?php echo $slide->target; ?> href="<?php echo $slide->link; ?>" class="fpssReadMore" title="<?php echo JText::_('FPSS_MOD_READ_MORE_ABOUT'); ?> <?php echo $slide->altTitle; ?>"><?php echo JText::_('FPSS_MORE'); ?></a>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="fpssTimer"></div>
	</div>
	<div class="navigation-wrapper"<?php if($params->get('hideNavigation')): ?> style="display:none;"<?php endif; ?>>
		<div class="navigation-background"></div>
		<ul class="navigation">
			<li class="navigation-previous"><a href="#" title="<?php echo JText::_('FPSS_PREVIOUS'); ?>">&laquo;</a></li>
			<?php foreach($slides as $key => $slide): ?>
			<li class="navigation-button">
				<a href="<?php echo $slide->link; ?>" title="<?php echo $slide->altTitle; ?>"><?php echo $slide->counter; ?></a>
			</li>
			<?php endforeach; ?>
			<li class="navigation-next"><a href="#" title="<?php echo JText::_('FPSS_NEXT'); ?>">&raquo;</a></li>
			<li class="navigation-control fpssPause"><a href="#" title="<?php echo JText::_('FPSS_PLAYPAUSE_SLIDE'); ?>"><?php echo JText::_('FPSS_PAUSE'); ?></a></li>
			<li class="fpss-clr">&nbsp;</li>
		</ul>
		<div class="fpss-clr"></div>
	</div>
</div>