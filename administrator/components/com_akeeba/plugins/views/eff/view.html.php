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
 * Extra Directories inclusion filter
 *
 */
class AkeebaViewEff extends FOFViewHtml
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

		// Get a JSON representation of the directories data
		$model = $this->getModel();
		$directories = $model->get_directories();
		$json = json_encode($directories);
		$this->assign('json', $json);

		// Add live help
		AkeebaHelperIncludes::addHelp('eff');

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