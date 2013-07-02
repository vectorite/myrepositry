<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsToolbar extends FOFToolbar
{
	/**
	 * Disable rendering a toolbar.
	 * 
	 * @return array
	 */
	protected function getMyViews()
	{
		return array();
	}
	
	public function onCpanelsBrowse() {
		// Set the toolbar title
		if(ADMINTOOLS_PRO) {
			JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DASHBOARD_PRO').' <small>'.ADMINTOOLS_VERSION.'</small>','admintools');
		} else {
			JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DASHBOARD_CORE').' <small>'.ADMINTOOLS_VERSION.'</small>','admintools');
		}

		if(ADMINTOOLS_JVERSION == '16') {
			JToolBarHelper::preferences('com_admintools', '265', '400');
		} else {
			JToolBarHelper::preferences('com_admintools', '220', '400');
		}
	}

	public function onEomsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_EOM'),'admintools');
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools');
	}
	
	public function onMasterpwsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_MASTERPW'),'admintools');
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option='.JRequest::getCmd('option'));
	}
	
	public function onAdminpwsBrowse()
	{
		// Set the toolbar title
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_ADMINPW'),'admintools');
		JToolBarHelper::back( (ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option=com_admintools');
	}
	
	public function onHtmakersBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_HTMAKER'),'admintools');
		JToolBarHelper::save('save','ATOOLS_LBL_HTMAKER_SAVE');
		JToolBarHelper::apply('apply','ATOOLS_LBL_HTMAKER_APPLY');
		JToolBarHelper::divider();
		JToolBarHelper::preview('index.php?option=com_admintools&view=htmaker&format=raw');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools');
	}
	
	public function onWafsAdd()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_WAF'),'admintools');
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option='.JRequest::getCmd('option'));
	}
	
	public function onWafconfigsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_WAFCONFIG'),'admintools');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools&view=waf');
	}
	
	public function onWafexceptionsBrowse()
	{
		parent::onBrowse();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_WAFEXCEPTIONS'), 'admintools');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools&view=waf');
	}
	
	public function onWafexceptionsAdd()
	{
		parent::onAdd();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_WAFEXCEPTIONS_EDIT'), 'admintools');
	}
	
	public function onWafexceptionsEdit()
	{
		$this->onWafexceptionsAdd();
	}
	
	public function onIpwlsBrowse()
	{
		if($this->perms->delete) {
			JToolBarHelper::deleteList();
		}
		if($this->perms->edit) {
			JToolBarHelper::editListX();
		}
		if($this->perms->create) {
			JToolBarHelper::addNewX();
		}
		
		$this->renderSubmenu();
		
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_IPWL'), 'admintools');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools&view=waf');
	}
	
	public function onIpwlsAdd()
	{
		parent::onAdd();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_IPWL_EDIT'), 'admintools');
	}
	
	public function onIpwlsEdit() {
		$this->onIpwlsAdd();
	}
	
	public function onIpblsBrowse()
	{
		if($this->perms->delete) {
			JToolBarHelper::deleteList();
		}
		if($this->perms->edit) {
			JToolBarHelper::editListX();
		}
		if($this->perms->create) {
			JToolBarHelper::addNewX();
		}
		
		$this->renderSubmenu();
		
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_IPBL'), 'admintools');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools&view=waf');
	}
	
	public function onIpblsAdd()
	{
		parent::onAdd();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_IPBL_EDIT'), 'admintools');
	}
	
	public function onIpblsEdit()
	{
		$this->onIpblsAdd();
	}
	
	public function onBadwordsBrowse()
	{
		if($this->perms->delete) {
			JToolBarHelper::deleteList();
		}
		if($this->perms->edit) {
			JToolBarHelper::editListX();
		}
		if($this->perms->create) {
			JToolBarHelper::addNewX();
		}
		
		$this->renderSubmenu();
		
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_BADWORDS'), 'admintools');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools&view=waf');
	}
	
	public function onBadwordsAdd()
	{
		parent::onAdd();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_BADWORDS_EDIT'), 'admintools');
	}
	
	public function onBadwordsEdit()
	{
		$this->onBadwordsAdd();
	}
	
	public function onGeoblocksBrowse()
	{
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		
		$subtitle_key = 'ADMINTOOLS_TITLE_'.strtoupper(JRequest::getCmd('view','cpanel'));
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DASHBOARD').' &ndash; <small>'.JText::_($subtitle_key).'</small>','admintools');
	}
	
	public function onLogsBrowse()
	{
		if($this->perms->delete) {
			JToolBarHelper::deleteList();
		}
		
		$this->renderSubmenu();
		
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_LOG'), 'admintools');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option=com_admintools&view=waf');
	}
	
	public function onIpautobansBrowse()
	{
		if($this->perms->delete) {
			JToolBarHelper::deleteList();
		}
		
		$this->renderSubmenu();
		
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_IPAUTOBAN'), 'admintools');
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option=com_admintools&view=waf');
	}
	
	public function onDbprefixesBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DBPREFIX'),'admintools');
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools');
	}
	
	public function onAdminusersBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_ADMINUSER'),'admintools');
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools');
	}
	
	public function onFixpermsconfigsBrowse()
	{
		$subtitle_key = 'ADMINTOOLS_TITLE_'.strtoupper(JRequest::getCmd('view','cpanel'));
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DASHBOARD').' &ndash; <small>'.JText::_($subtitle_key).'</small>','admintools');

		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools');
	}
	
	public function onFixpermsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_FIXPERMS'),'admintools');
	}
	
	public function onFixpermsRun()
	{
		$this->onFixpermsBrowse();
	}
	
	public function onSeoandlinksBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_SEOANDLINK'),'admintools');

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK','index.php?option=com_admintools');
	}
	
	public function onCleantmpsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_CLEANTMP'),'admintools');
	}
	
	public function onCleantmpsRun()
	{
		$this->onCleantmpsBrowse();
	}
	
	public function onPostsetupsBrowse()
	{
		JToolBarHelper::title(JText::_('COM_ADMINTOOLS').': <small>'.JText::_('COM_ADMINTOOLS_POSTSETUP_TITLE').'</small>','admintools');
	}
	
	public function onDbchcolsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DBCHCOL'),'admintools');
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option=com_admintools');
	}
	
	public function onDbtools()
	{
		// Set the toolbar title
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DBTOOLS'),'admintools');
	}
	
	public function onRedirsBrowse()
	{
		parent::onBrowse();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_REDIRS'),'admintools');
	}
	
	public function onRedirsAdd()
	{
		parent::onAdd();
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_REDIRS_EDIT'),'admintools');
	}
	
	public function onRedirsEdit()
	{
		$this->onRedirsAdd();
	}
	
	public function onAclsBrowse()
	{
		JToolBarHelper::title(JText::_('ADMINTOOLS_TITLE_DASHBOARD').' &ndash; <small>'.JText::_('ADMINTOOLS_TITLE_ACL').'</small>','admintools');
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option='.JRequest::getCmd('option'));
	}
	
	public function onScansBrowse()
	{
		// Set toolbar title
		$subtitle_key = FOFInput::getCmd('option','com_foobar',$this->input).'_TITLE_'.strtoupper(FOFInput::getCmd('view','cpanel',$this->input));
		JToolBarHelper::title(JText::_( FOFInput::getCmd('option','com_foobar',$this->input)).' &ndash; <small>'.JText::_($subtitle_key).'</small>', str_replace('com_', '', FOFInput::getCmd('option','com_foobar',$this->input)));

		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$canScan = JFactory::getUser()->authorise('core.manage','com_admintools');
		} else {
			$canScan = true;
		}
		
		if($canScan) {
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Link', 'scan', JText::_('COM_ADMINTOOLS_MSG_SCANS_SCANNOW'), 'javascript:startScan()');
			JToolBarHelper::divider();
		}
		
		// Add toolbar buttons
		if($this->perms->delete) {
			JToolBarHelper::deleteList();
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option='.JRequest::getCmd('option'));
	}
	
	public function onScanalertsBrowse()
	{
		$scan_id = FOFInput::getInt('scan_id', 0, $this->input);
		
		$subtitle_key = FOFInput::getCmd('option','com_foobar',$this->input).'_TITLE_'.strtoupper(FOFInput::getCmd('view','cpanel',$this->input));
		JToolBarHelper::title(JText::_( FOFInput::getCmd('option','com_foobar',$this->input)).' &ndash; <small>'.JText::sprintf($subtitle_key, $scan_id).'</small>', str_replace('com_', '', FOFInput::getCmd('option','com_foobar',$this->input)));

		JToolBarHelper::publishList('publish','COM_ADMINTOOLS_LBL_SCANALERTS_MARKSAFE');
		JToolBarHelper::unpublishList('unpublish','COM_ADMINTOOLS_LBL_SCANALERTS_MARKUNSAFE');
		
		JToolBarHelper::divider();
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'print', JText::_('COM_ADMINTOOLS_MSG_COMMON_PRINT'), 'javascript:printReport()');
		$bar->appendButton('Link', 'csv', JText::_('COM_ADMINTOOLS_MSG_COMMON_CSV'), 'javascript:exportCSV()');
		
		JToolBarHelper::divider();
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option=com_admintools&view=scans');
	}
	
	public function onScanalertsEdit()
	{
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
}