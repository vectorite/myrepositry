<?php
/**
 * VM product builder component
 * @version $Id:cart_modal.php 2.0 2012-3-15 14:17 sakisTerz $
 * @package productbuilder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');
?>
<div id="cart_info" class="cart_info">
<div class="cartmessage"><?php echo JText::_('COM_PRODUCTBUILDER_THE_PRODUCTS_WAS_ADDED_TO_YOUR_CART'); ?></div>
<div class="pbbuttons_area">
<a class="pbcart_btn" href="#pb_mainPage" onclick="SqueezeBox.close();"><?php echo JText::_('COM_PRODUCTBUILDER_CONTINUE_SHOPPING');?></a>
<a class="pbcart_btn" href="<?php echo JRoute::_(JURI::base().'index.php?option=com_virtuemart&view=cart');?>"><?php echo JText::_('COM_PRODUCTBUILDER_SHOW_CART');?></a>
<div style="clear:both;"></div>
</div>
</div>