<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class AdmintoolsViewHtmaker extends JView
{
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$htaccess = $model->makeHtaccess();

		$this->assign('htaccess', $htaccess);

		$this->setLayout('plain');

		parent::display();
	}
}