<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

class WorkforceHelper
{
	public static function addSubmenu($vName)
	{
		$canDo	= WorkforceHelper::getActions();

        JSubMenuHelper::addEntry(
            JText::_( 'COM_WORKFORCE_DEPARTMENTS' ),
            'index.php?option=com_workforce&view=departments',
            $vName == 'departments'
        );

        JSubMenuHelper::addEntry(
            JText::_( 'COM_WORKFORCE_EMPLOYEES' ),
            'index.php?option=com_workforce&view=employees',
            $vName == 'employees'
        );

        if($canDo->get('core.admin')){
            JSubMenuHelper::addEntry(
                JText::_( 'COM_WORKFORCE_BACK_UP' ),
                'index.php?option=com_workforce&view=backup',
                $vName == 'backup'
            );

            JSubMenuHelper::addEntry(
                JText::_( 'COM_WORKFORCE_RESTORE' ),
                'index.php?option=com_workforce&view=restore',
                $vName == 'restore'
            );

            JSubMenuHelper::addEntry(
                JText::_( 'JTOOLBAR_EDIT_CSS' ),
                'index.php?option=com_workforce&view=editcss&layout=edit',
                $vName == 'editcss'
            );
        }        
	}

    public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, 'com_workforce'));
		}

		return $result;
	}
}