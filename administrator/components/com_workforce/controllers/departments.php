<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controlleradmin');

class WorkforceControllerDepartments extends JControllerAdmin
{
	protected $text_prefix = 'COM_WORKFORCE';

    function __construct($config = array())
	{
		parent::__construct($config);
	}

    public function getModel($name = 'Department', $prefix = 'WorkforceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
?>
