<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class WorkforceViewEmployees extends JView
{
    protected $departments;
    protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
        $this->departments	= $this->get('DepartmentOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		require_once JPATH_COMPONENT .'/models/fields/department.php';
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/workforce.php';

		$canDo	= WorkforceHelper::getActions($this->state->get('filter.category_id'));

		JToolBarHelper::title('<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE').'</span> <span class="wf_adminSubheader">['.JText::_('COM_WORKFORCE_EMPLOYEES').']</span>', 'workforce');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('employee.add','JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('employee.edit','JTOOLBAR_EDIT');
            JToolBarHelper::divider();
            JToolBarHelper::custom('employees.moveEmployee', 'move.png', 'move.png','COM_WORKFORCE_MOVE_EMPLOYEES', true);
            JToolBarHelper::custom('employees.copyEmployee', 'copy.png', 'copy.png', 'COM_WORKFORCE_COPY_EMPLOYEES', true);
		}

		if ($canDo->get('core.edit.state')) {
			if ($this->state->get('filter.state') != 2){
				JToolBarHelper::divider();
                JToolBarHelper::custom('employees.feature', 'publish.png', 'publish_f2.png', JText::_( 'COM_WORKFORCE_FEATURE' ), true );
                JToolBarHelper::custom('employees.unfeature', 'unpublish.png', 'unpublish_f2.png', JText::_( 'COM_WORKFORCE_UNFEATURE' ), true );
				JToolBarHelper::divider();
                JToolBarHelper::custom('employees.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('employees.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::divider();
            JToolBarHelper::deleteList('', 'employees.delete','JTOOLBAR_EMPTY_TRASH');			
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
            JToolBarHelper::trash('employees.trash','JTOOLBAR_TRASH');			
		}
	}

    function copyEmployee( $option, $cid, $items  )
	{
		$document = JFactory::getDocument();
        $document->addStyleSheet('components/com_workforce/assets/css/workforce_backend.css');

        JToolBarHelper::title( '<span class="wf_adminHeader">'.JText::_( 'COM_WORKFORCE_COPY_EMPLOYEES' ).'</span>', 'workforce' );
		JToolBarHelper::custom( 'employees.copyEmployeeSave', 'save.png', 'save.png', JText::_( 'JTOOLBAR_SAVE' ), false );
        JToolBarHelper::divider();
        JToolBarHelper::cancel();

        require_once JPATH_COMPONENT .'/models/fields/department.php';
        JHtml::_('behavior.formvalidation');
        ?>

		<form action="<?php echo JRoute::_('index.php?option=com_workforce&view=employees'); ?>" method="post" name="adminForm">
            <table class="adminlist">
                <thead><tr><th colspan="2"><?php echo JText::_('COM_WORKFORCE_COPY_EMPLOYEES'); ?></th></tr></thead>
                <tbody>
                    <tr>
                        <td valign="top" align="center" width="40%">
                            <strong><?php echo JText::_( 'COM_WORKFORCE_DEPARTMENT' ); ?>:</strong><br />
                            <select name="department" class="inputbox required">
                                <?php echo JHtml::_('select.options', JFormFieldDepartment::getOptions(), 'value', 'text', '');?>
                            </select><br /><br />
                        </td>
                        <td valign="top" align="center">
                            <strong><?php echo JText::_( 'COM_WORKFORCE_EMPLOYEES' ); ?>:</strong><br />
                            <?php
                                echo "<ol>";
                                foreach ( $items as $item ) {
                                    echo "<li>". $item->full_name ."</li>";
                                }
                                echo "</ol>";
                            ?>
                        </td>
                    </tr>
                </tbody>
                <tfoot><tr><td colspan="2">&nbsp;</td></tr></tfoot>
            </table>
            <br /><br />
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
            <?php
            foreach ($cid as $id) {
                echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
            }
            ?>
		</form>
		<?php
	}

    function moveEmployee( $option, $cid, $items )
	{
		$document = JFactory::getDocument();
        $document->addStyleSheet('components/com_workforce/assets/css/workforce_backend.css');

        JToolBarHelper::title( '<span class="wf_adminHeader">'.JText::_( 'COM_WORKFORCE_MOVE_EMPLOYEES' ).'</span>', 'workforce' );
		JToolBarHelper::custom( 'employees.moveEmployeeSave', 'save.png', 'save.png', JText::_( 'JTOOLBAR_SAVE' ), false );
        JToolBarHelper::divider();
        JToolBarHelper::cancel();

        require_once JPATH_COMPONENT .'/models/fields/department.php';
        JHtml::_('behavior.formvalidation');
        ?>

		<form action="<?php echo JRoute::_('index.php?option=com_workforce&view=employees'); ?>" method="post" name="adminForm">
            <table class="adminlist">
                <thead><tr><th colspan="2"><?php echo JText::_('COM_WORKFORCE_MOVE_EMPLOYEES'); ?></th></tr></thead>
                <tbody>
                    <tr>
                        <td valign="top" align="center" width="40%">
                            <strong><?php echo JText::_( 'COM_WORKFORCE_DEPARTMENT' ); ?>:</strong><br />
                            <select name="department" class="inputbox required">
                                <?php echo JHtml::_('select.options', JFormFieldDepartment::getOptions(), 'value', 'text', '');?>
                            </select><br /><br />
                        </td>
                        <td valign="top" align="center">
                            <strong><?php echo JText::_( 'COM_WORKFORCE_EMPLOYEES' ); ?>:</strong><br />
                            <?php
                                echo "<ol>";
                                foreach ( $items as $item ) {
                                    echo "<li>". $item->full_name ."</li>";
                                }
                                echo "</ol>";
                            ?>
                        </td>
                    </tr>
                </tbody>
                <tfoot><tr><td colspan="2">&nbsp;</td></tr></tfoot>
            </table>
            <br /><br />
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
            <?php
            foreach ($cid as $id) {
                echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
            }
            ?>
		</form>
		<?php
	}
}
?>