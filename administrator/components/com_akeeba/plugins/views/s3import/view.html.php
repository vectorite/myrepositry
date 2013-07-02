<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 3.4
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * S3 Import view - View
 *
 */
class AkeebaViewS3import extends FOFViewHtml
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
	
	public function onBrowse($tpl = null) {
		// Add live help
		AkeebaHelperIncludes::addHelp('s3import');
		
		$model = $this->getModel();
		$model->getS3Credentials();
		$contents = $model->getContents();
		$buckets = $model->getBuckets();
		$bucketSelect = $model->getBucketsDropdown();
		$root = JFactory::getApplication()->getUserStateFromRequest('com_akeeba.folder',   'folder',   ''             , 'raw');

		// Assign variables
		$this->assign('s3access',		$model->getState('s3access'));
		$this->assign('s3secret',		$model->getState('s3secret'));
		$this->assign('buckets',		$buckets);
		$this->assign('bucketSelect',	$bucketSelect);
		$this->assign('contents',		$contents);
		$this->assign('root',			$root);
		$this->assign('crumbs',			$model->getCrumbs());
		
		return true;
	}
	
	public function onDltoserver($tpl = null)
	{
		$this->setLayout('downloading');
		$model = $this->getModel();
		
		// Add live help
		AkeebaHelperIncludes::addHelp('s3import');
		
		$total	= JFactory::getApplication()->getUserState('com_akeeba.s3import.totalsize',	0);
		$done	= JFactory::getApplication()->getUserState('com_akeeba.s3import.donesize',	0);
		$part	= JFactory::getApplication()->getUserState('com_akeeba.s3import.part',		0) + 1;
		$parts	= JFactory::getApplication()->getUserState('com_akeeba.s3import.totalparts',0);

		if($total <= 0) {
			$percent = 0;
		} else {
			$percent = (int)(100 * ($done/$total) );
			if($percent < 0) $percent = 0;
			if($percent > 100) $percent = 100; 
		}
		$this->assign('total',			$total);
		$this->assign('done',			$done);
		$this->assign('percent',		$percent);
		$this->assign('total_parts',	$parts);
		$this->assign('current_part',	$part);

		// Render the progress bar
		$document = JFactory::getDocument();
		$step = $model->getState('step', 1) + 1;

		$script = "window.addEvent( 'domready' ,  function() {\n";
		$script .= "window.location='index.php?option=com_akeeba&view=s3import&layout=downloading&task=dltoserver&step=$step';\n";
		$script .= "});\n";
		$document->addScriptDeclaration($script);
		
		AkeebaHelperIncludes::addHelp('s3import');
		
		return true;
	}

}