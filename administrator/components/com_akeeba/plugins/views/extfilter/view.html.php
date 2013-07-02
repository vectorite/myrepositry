<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 2.1
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * Extension Filter view class
 *
 */
class AkeebaViewExtfilter extends FOFViewHtml
{
	/**
	 * Modified constructor to enable loading layouts from the plug-ins folder
	 * @param $config
	 */
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
		$tmpl_path = dirname(__FILE__).'/tmpl';
		$this->addTemplatePath($tmpl_path);
	}

	public function onDisplay($tpl = null)
	{
		$model = $this->getModel();
		$task = $model->getState('task', 'components');

		// Add submenus (those nifty text links below the toolbar!)
		$toolbar = FOFToolbar::getAnInstance($this->input->get('option','com_foobar','cmd'), $this->config);
		$link = JURI::base().'?option=com_akeeba&view=extfilter&task=components';
		$toolbar->appendLink(JText::_('EXTFILTER_COMPONENTS'), $link, ($task == 'components'));
		$link = JURI::base().'?option=com_akeeba&view=extfilter&task=modules';
		$toolbar->appendLink(JText::_('EXTFILTER_MODULES'), $link, ($task == 'modules'));
		$link = JURI::base().'?option=com_akeeba&view=extfilter&task=plugins';
		$toolbar->appendLink(JText::_('EXTFILTER_PLUGINS'), $link, ($task == 'plugins'));
		$link = JURI::base().'?option=com_akeeba&view=extfilter&task=languages';
		$toolbar->appendLink(JText::_('EXTFILTER_LANGUAGES'), $link, ($task == 'languages'));
		$link = JURI::base().'?option=com_akeeba&view=extfilter&task=templates';
		$toolbar->appendLink(JText::_('EXTFILTER_TEMPLATES'), $link, ($task == 'templates'));

		switch($task)
		{
			case 'components':
				// Pass along the list of components
				$this->assignRef('components', $model->getComponents());
				break;

			case 'modules':
				// Pass along the list of components
				$this->assignRef('modules', $model->getModules());
				break;

			case 'plugins':
				// Pass along the list of components
				$this->assignRef('plugins', $model->getPlugins());
				break;

			case 'templates':
				// Pass along the list of components
				$this->assignRef('templates', $model->getTemplates());
				break;

			case 'languages':
				// Pass along the list of components
				$this->assignRef('languages', $model->getLanguages());
				break;
		}
		$this->setLayout($task);

		// Add live help
		AkeebaHelperIncludes::addHelp('extfilter');

		// Get profile ID
		$profileid = AEPlatform::getInstance()->get_active_profile();
		$this->assign('profileid', $profileid);

		// Get profile name
		$pmodel = FOFModel::getAnInstance('Profiles', 'AkeebaModel');
		$pmodel->setId($profileid);
		$profile_data = $pmodel->getItem();
		$this->assign('profilename', $profile_data->description);

		return true;
	}
}