<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 3.2
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * Archive discovery view - HTML View
 */
class AkeebaViewDiscover extends FOFViewHtml
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

	public function onDiscover($tpl = null)
	{
		$media_folder = JURI::base().'../media/com_akeeba/';
		
		$model = $this->getModel();
		
		$directory = $model->getState('directory','');
		$this->setLayout('discover');

		$files = $model->getFiles();

		$this->assign('files', $files);
		$this->assign('directory', $directory);
		
		AkeebaHelperIncludes::addHelp('discover');
		
		return true;
	}
	
	public function onBrowse($tpl = null)
	{
		$media_folder = JURI::base().'../media/com_akeeba/';
		
		$model = $this->getModel();
		
		$directory = $model->getState('directory','');
		if(empty($directory)) {
			$config = AEFactory::getConfiguration();
			$this->assign('directory', $config->get('akeeba.basic.output_directory','[DEFAULT_OUTPUT]'));
		} else {
			$this->assign('directory');
		}
		
		AkeebaHelperIncludes::addHelp('discover');
		
		return true;
	}
}