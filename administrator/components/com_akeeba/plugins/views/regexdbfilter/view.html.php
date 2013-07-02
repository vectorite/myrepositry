<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * Regular expression based db filters management View
 *
 */
class AkeebaViewRegexdbfilter extends FOFViewHtml
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

	public function onBrowse($tpl = null)
	{
		$media_folder = JURI::base().'../media/com_akeeba/';

		// Get the root URI for media files
		$this->assign( 'mediadir', AkeebaHelperEscape::escapeJS($media_folder.'theme/') );

		// Get a JSON representation of the available roots
		$model = $this->getModel();
		$root_info = $model->get_roots();
		$roots = array();
		if(!empty($root_info))
		{
			// Loop all dir definitions
			foreach($root_info as $def)
			{
				$roots[] = $def->value;
				$options[] = JHTML::_('select.option', $def->value, $def->text );
			}
		}
		$site_root = '[SITEDB]';
		$attribs = 'onchange="akeeba_active_root_changed();"';
		$this->assign('root_select', JHTML::_('select.genericlist', $options, 'root', $attribs, 'value', 'text', $site_root, 'active_root') );
		$this->assign('roots', $roots);

		$tpl = null;

		// Get a JSON representation of the directory data
		$model = $this->getModel();
		$json = json_encode($model->get_regex_filters($site_root));
		$this->assignRef( 'json', $json );

		// Add live help
		AkeebaHelperIncludes::addHelp('regexdbfilter');

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