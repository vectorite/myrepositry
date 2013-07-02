<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$app        = JFactory::getApplication();
$document   = JFactory::getDocument();
$currid     = JRequest::getInt('id', 0).':'.JRequest::getInt('Itemid', 0);
$view       = JRequest::getCmd('view');
$current_search = $app->getUserStateFromRequest( 'com_workforce.'.$view.'.search'.$currid, 'search', '', 'string' );

$wf_reset = "
    function resetWFform(){
        document.wfsearch.search.value = '';
        if(document.wfsearch.department){
            document.wfsearch.department.selectedIndex = '';
        }
    }";
$document->addScriptDeclaration($wf_reset);
   
?>

<tr>
    <td colspan="<?php echo $this->settings->get('employee_columns', 1); ?>">
        <form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="wfsearch">
            <div class="wf_quicksearch_optholder">
                <?php
                echo '<strong>' . JText::_('COM_WORKFORCE_NAME') . ':</strong> <input type="text" class="inputbox" onclick="this.value=\'\'" name="search" value="' . $current_search . '" /> ';
                if(JRequest::getVar('view') == 'allemployees') echo $this->lists['department'];
                ?>
                <input type="submit" value="<?php echo JText::_('COM_WORKFORCE_GO'); ?>" />
                <input type="button" value="<?php echo JText::_('COM_WORKFORCE_RESET'); ?>" onclick="resetWFform(); this.form.submit();" />
            </div>
            <div class="wf_quicksearch_sortholder">
                <?php
                    echo '<strong>' . JText::_('COM_WORKFORCE_SORTBY') . ':</strong> ' . $this->lists['sort'] . ' ';
                    echo '<strong>' . JText::_('COM_WORKFORCE_ORDERBY') . ':</strong> ' . $this->lists['order'] . ' ';
                ?>
            </div>
            <input type="hidden" name="option" value="com_workforce" />
            <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
            <input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>" />
            <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>" />
        </form>
    </td>
</tr>