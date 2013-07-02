<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsViewScanner extends FOFViewHtml
{	
	protected function onBrowse($tpl = null)
	{
		$model = $this->getModel();
		$this->assign('fileExtensions', $model->getFileExtensions());
		$this->assign('excludeFolders', $model->getExcludeFolders());
		$this->assign('excludeFiles', $model->getExcludeFiles());
		$this->assign('minExecTime', $model->getMinExecTime());
		$this->assign('maxExecTime', $model->getMaxExecTime());
		$this->assign('runtimeBias', $model->getRuntimeBias());
		
		return true;
	}
}