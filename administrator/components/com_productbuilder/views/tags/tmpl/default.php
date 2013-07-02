<?php
/**
* product builder component
* @package productbuilder
* @version $Id: views/groups/tmpl/default.php 2012-2-16 sakisTerz $
* @author Sakis Terz (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
defined('_JEXEC') or die('Restricted access'); 
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<div id="editcell">
<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=tags');?>" method="post" name="adminForm">
 <div id="totals"><?php echo $this->pagination->getResultsCounter() ;?></div>
		<br clear="all" />
		<fieldset id="filter-bar">
		<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_PRODUCTBUILDER_FILTER_SEARCH'); ?>" />

				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

			</div>
		</fieldset>
		<table class="adminlist">
    <thead>
        <tr>

           <th width="2%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
            <th width="40%"> <?php echo JHtml::_('grid.sort','COM_PRODUCTBUILDER_TAG','name',$listDirn,$listOrder);?></th>
            <th> <?php echo JHtml::_('grid.sort','JSTATUS','published',$listDirn,$listOrder);?></th>      
        </tr>            
    </thead>
    <tbody>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		$link = JRoute::_( 'index.php?option=com_productbuilder&view=tags&task=tag.edit&id='. (int)$row->id );		
        ?>                    
        <tr class="<?php echo "row$k"; ?>">
           <td align="center"><?php echo $checked;?></td>
            <td> <div class="tagnames" style="background-color:#<?php echo $row->color?>;"><a style="color:#fff;" href="<?php echo $link; ?>"><?php echo $row->name;?></a></div></td>
            <td class="center"><?php echo JHtml::_('jgrid.published', $row->published, $i,'tags.'); ?></td>           
        </tr>
        <?php } ?>
       </tbody>
       
       	<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
    </table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>
</div>
