<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class JViewConfig extends JView
	{
		function display($tpl = null)
		{	
		 
			global $option, $mainframe;
			
			//$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
			//limitstart = JRequest::getVar('limitstart', 0);
			$model = &$this->getModel();
			
			/*
			if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'ps_onepage.php'))
			{
			 $model->install(true);
			}
			*/
			
			
			
			$pms = $model->getPaymentMethods();
			$langs = $model->getLanguages();
			$lang_vars = $model->getLangVars();
			$this->assignRef('lang_vars', $lang_vars); // ok
			$this->assignRef('pms', $pms); //ok		
			$this->assignRef('langs', $langs); //ok
			
			jimport('joomla.html.pagination');

			if (empty($tpl)) $tpl = 'ajax'; 
			
			parent::display($tpl); 
		}
	}
?>