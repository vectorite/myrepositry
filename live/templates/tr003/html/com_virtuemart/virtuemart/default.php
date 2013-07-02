<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 4354 2011-10-11 23:04:16Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JHTML::_( 'behavior.modal' );
include_once dirname(__FILE__) . '/../assets/includes/flexibleMartGlobal.php';

?>

<?php # Vendor Store Description
if (!empty($this->vendor->vendor_store_desc)) { ?>
<p class="vendor-store-desc">
	<?php echo $this->vendor->vendor_store_desc; ?>
</p>
<?php } ?>

<?php

# load categories from front_categories if exist
if ($this->categories) echo $this->loadTemplate('categories');

# Show template for : topten,Featured, Latest Products if selected in config BE
if (!empty($this->products) ) { ?>
	<?php echo $this->loadTemplate('products');
}

?>

 