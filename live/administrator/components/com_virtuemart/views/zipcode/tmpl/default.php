<?php

/**

*

* Description

*

* @package	VirtueMart

* @subpackage State

* @author RickG, Max Milbers

* @link http://www.virtuemart.net

* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.

* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php

* VirtueMart is free software. This version may have been modified pursuant

* to the GNU General Public License, and as distributed it includes or

* is derivative of works licensed under the GNU General Public License or

* other free or open source software licenses.

* @version $Id: default.php 6048 2012-05-30 20:18:53Z Milbo $

*/



// Check to ensure this file is included in Joomla!

defined('_JEXEC') or die('Restricted access');



AdminUIHelper::startAdminArea();



?>



<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div id="editcell">

    <div><?php echo JHTML::_('link','index.php?option=com_virtuemart&view=zipcode&virtuemart_state_id='.$this->virtuemart_state_id,JText::sprintf('COM_VIRTUEMART_ZIPCODE_STATES',$this->state_name)); ?></div>

	<table class="adminlist" cellspacing="0" cellpadding="0">

	    <thead>

		<tr>

		    <th width="10">

			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->zipcode ); ?>);" />

		    </th>

		    <th>

			<?php echo   JText::_('COM_VIRTUEMART_STATE_NAME'); ?>

		    </th>

		    <th width="20">

			<?php echo JText::_('COM_VIRTUEMART_PUBLISH'); ?>

		    </th>

		</tr>

	    </thead>

	    <?php

	    $k = 0;



	    for ($i=0, $n=count( $this->zipcode ); $i < $n; $i++) {

		$row = $this->zipcode[$i];



		$checked = JHTML::_('grid.id', $i, $row->virtuemart_zipcode_id,null,'virtuemart_zipcode_id');

		$published = JHTML::_('grid.published', $row, $i);

		$editlink = JROUTE::_('index.php?option=com_virtuemart&view=zipcode&task=edit&virtuemart_zipcode_id=' . $row->virtuemart_zipcode_id);



		?>

	    <tr class="row<?php echo $k ; ?>">

		<td width="10">

			<?php echo $checked; ?>

		</td>

		<td align="left">

		    <a href="<?php echo $editlink; ?>"><?php echo $row->zipcode; ?></a>

		</td>

		<td align="left">

			<?php echo $row->virtuemart_worldzone_id; ?>

		</td>

	    </tr>

		<?php

		$k = 1 - $k;

	    }

	    ?>

	    <tfoot>

		<tr>

		    <td colspan="10">

			<?php echo $this->pagination->getListFooter(); ?>

		    </td>

		</tr>

	    </tfoot>

	</table>

    </div>

    <input type="hidden" name="virtuemart_state_id" value="<?php echo $this->virtuemart_state_id; ?>" />

	<?php echo $this->addStandardHiddenToForm(); ?>

</form>







<?php AdminUIHelper::endAdminArea(); ?>