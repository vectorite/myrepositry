<?php
/**
* product builder component
* @version $Id:1 views/groups/tmpl/default.php  2012-2-6 sakisTerz $
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
$saveOrder	= $listOrder == 'ordering';
$published_opt=array(array('value'=>1 ,'text' => JText::_('Published')),array('value'=>'0' ,'text' => JText::_('Unpublished'))); 
$oldProd='';
?>
<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=groups');?>" method="post" name="adminForm" id="adminForm">
		<div id="totals"><?php echo $this->pagination->getResultsCounter() ;?></div>
		<br clear="all" />
		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_PRODUCTBUILDER_FILTER_SEARCH'); ?>" />

				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

			</div>
			<div class="filter-select fltrt">
			<?php 
			$all = array();
			$all[] = JHtml::_('select.option', '', JText::_('JOPTION_SELECT_PUBLISHED'));
			echo JHtml::_('select.genericlist', array_merge($all,$published_opt), 'filter_published', 'onchange="this.form.submit();"', 'value', 'text', $this->state->get('filter.published'));
			?>				
			</div>
			<div class="filter-select fltrt">
			<?php 
			$all = array();
			$all[] = JHtml::_('select.option', '', JText::_('COM_PRODUCTBUILDER_SELECT_CONF_PRODUCT'));
			echo JHtml::_('select.genericlist', array_merge($all, $this->filters->bundles), 'filter_pb_prod', 'onchange="this.form.submit();"', 'value', 'text', $this->state->get('filter.pb_prod'));
			?>
			</div>
			
			
	   	</fieldset>
<div id="editcell">
    <table class="adminlist">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( '#' ); ?></th>
					<th width="2%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="20%"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_CONF_PRODUCT', 'product_id', $listDirn, $listOrder); ?></th>
					<th  style="text-align: left;"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_GROUP', 'gr.name', $listDirn, $listOrder); ?></th>
					
					<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'groups.saveorder'); ?>
					<?php endif; ?>
					</th>
					<th width="5"><?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?></th>
					<th width="5"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_EDITABLE', 'editable', $listDirn, $listOrder); ?></th>
					<th width="7%"><?php echo JHtml::_('grid.sort', 'JFIELD_LANGUAGE_LABEL', 'language', $listDirn, $listOrder); ?></th>
					<th width="8"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_ID', 'id', $listDirn, $listOrder); ?></th>				
				</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $item =& $this->items[$i];
        $newProd=$item->product_id;
		$checked    = JHTML::_( 'grid.id', $i, $item->id );
		$link = JRoute::_( 'index.php?option=com_productbuilder&view=groups&task=group.edit&id='. (int)$item->id );
		
        ?>
         <?php if($newProd!=$oldProd){
           //
		 //vars used to create the orderUpIcon and orderDownIcon functions

		  $ordering=pbGroupsHelper::groups_ordering($item);
		  $minOrd=$ordering[0]['Min'];
		  $maxOrd=$ordering[0]['Max'];
		  $countOrd=$ordering[0]['Count'];
		  ?>
           <tr>
               <td class="vmfCatHeader" style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader"  style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader"  style="background-color:#C9E1FC" align="center" ><?php echo $item->bundleName; ?></td>
               <td class="vmfCatHeader"  style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader"  style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader" style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader" style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader" style="background-color:#C9E1FC"></td>
               <td class="vmfCatHeader" style="background-color:#C9E1FC"></td>
          </tr>      
            
            <?php }?>
            
        <tr class="<?php echo "row$k"; ?>">
            <td> <?php echo  $this->state->get('limitstart')+$i+1?></td>
            <td> <?php echo $checked;?></td>
            <td></td>
            <td> <a href="<?php echo $link; ?>"><?php echo $item->name;?></a></td>
            <td class="order" nowrap="nowrap">
            <?php if ($saveOrder) :?>        
				<span><?php echo $this->pagination->orderUpIcon( $i, $item->ordering >$minOrd , 'groups.orderup', 'Move Up', $item->ordering >$minOrd); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $countOrd, $item->ordering<$maxOrd, 'groups.orderdown', 'Move Down', $item->ordering<$maxOrd ); ?></span>
				
            <?php endif; ?>
            	<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?> 
				<input type="text" name="ordering[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" style="text-align: center" />
			</td>
			<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i,'groups.'); ?></td>
			<td class="center"><?php
			if($item->editable) $classname='editable';
			else $classname='noneditable';;
			?> <div class="editablestate <?php echo $classname;?>"></div>
			</td>
			<td><?php echo $item->language=='*'?JText::_('JALL'):$item->language;?></td>
			<td><?php echo $item->id;?></td>
			
        </tr>
        <?php
        $k = 1 - $k;
		$oldProd=$item->product_id;
    }
    ?>
    </table>
    </div>
    
	<input type="hidden" name="option" value="com_productbuilder" />    
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

