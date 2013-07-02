<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsModelFixpermsconfig extends FOFModel
{
	public function  __construct($config = array()) {
		parent::__construct($config);

		$this->table = 'customperm';
	}

	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db)
			->select(array('*'))
			->from($db->nameQuote('#__admintools_customperms'));
		
		$fltPath			= $this->getState('filter_path', null, 'string');
		if($fltPath) {
			$fltPath = $fltPath.'%';
			$query->where($db->nameQuote('path').' LIKE '.$db->quote($fltPath));
		}

		$fltReason		= $this->getState('reason', null, 'cmd');
		if($fltReason) {
			$fltReason = '%'.$fltReason.'%';
			$query->where($db->nameQuote('reason').' LIKE '.$db->quote($fltReason));
		}
		
		if(!$overrideLimits) {
			$order = $this->getState('filter_order',null,'cmd');
			if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'id';
			$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
			$query->order($order.' '.$dir);
		}

		return $query;
	}

	public function saveDefaults()
	{
		$dirperms = $this->getState('dirperms');
		$fileperms = $this->getState('fileperms');

		$dirperms = octdec($dirperms);
		if( ($dirperms < 0600) || ($dirperms > 0777) ) $dirperms = 0755;

		$fileperms = octdec($fileperms);
		if( ($fileperms < 0600) || ($fileperms > 0777) ) $fileperms = 0755;

		$params = JModel::getInstance('Storage','AdmintoolsModel');

		$params->setValue('dirperms', '0'.decoct($dirperms));
		$params->setValue('fileperms', '0'.decoct($fileperms));
		
		$params->save();
	}

	public function applyPath()
	{
		jimport('joomla.filesystem.folder');

		// Get and clean up the path
		$path = $this->getState('path','');
		$relpath = $this->getRelativePath($path);
		$this->setState('filter_path',$relpath);

		$this->getItemList(true);
	}

	public function getRelativePath($somepath)
	{
		$path = JPATH_ROOT.DS.$somepath;
		$path = JPath::clean($path,'/');

		// Clean up the root
		$root = JPath::clean(JPATH_ROOT, '/');

		// Find the relative path and get the custom permissions
		$relpath = ltrim(substr($path, strlen($root) ), '/');
		return $relpath;
	}

	public function getListing()
	{
		jimport('joomla.filesystem.folder');
		$this->applyPath();

		$relpath = $this->getState('filter_path','');
		$path = JPATH_ROOT.DS.$relpath;

		$folders_raw = JFolder::folders($path);
		$files_raw = JFolder::files($path);

		if(!empty($relpath)) $relpath .= '/';

		$folders = array();
		if(!empty($folders_raw)) foreach($folders_raw as $folder)
		{
			$perms = $this->getPerms($relpath.$folder);
			$currentperms = @fileperms(JPATH_ROOT.DS.$relpath.$folder);
			$owneruser = function_exists('fileowner') ? fileowner(JPATH_ROOT.DS.$relpath.$folder) : false;
			$ownergroup = function_exists('filegroup') ? filegroup(JPATH_ROOT.DS.$relpath.$folder) : false;
			$folders[] = array(
				'item'	=> $folder,
				'path'	=> $relpath.$folder,
				'perms' => $perms,
				'realperms' => $currentperms,
				'uid' => $owneruser,
				'gid' => $ownergroup
			);
		}

		$files = array();
		if(!empty($files_raw)) foreach($files_raw as $file)
		{
			$perms = $this->getPerms($relpath.$file);
			$currentperms = @fileperms(JPATH_ROOT.DS.$relpath.$file);
			$owneruser = function_exists('fileowner') ? @fileowner(JPATH_ROOT.DS.$relpath.$file) : false;
			$ownergroup = function_exists('filegroup') ? @filegroup(JPATH_ROOT.DS.$relpath.$file) : false;
			$files[] = array(
				'item'	=> $file,
				'path'	=> $relpath.$file,
				'perms' => $perms,
				'realperms' => $currentperms,
				'uid' => $owneruser,
				'gid' => $ownergroup
			);
		}

		$crumbs = explode('/',$relpath);

		return array('folders'=>$folders, 'files'=>$files, 'crumbs' => $crumbs);
	}

	public function getPerms($path)
	{
		if(!empty($this->list)) foreach($this->list as $item)
		{
			if($item->path == $path) return $item->perms;
		}
		return '';
	}

	public function savePermissions($apply = false)
	{
		if($apply) {
			$fixmodel = JModel::getInstance('Fixperms', 'AdmintoolsModel');
		}
		
		$db = $this->getDBO();
		$relpath = $this->getState('filter_path','');

		if(!empty($relpath))
		{
			$path_esc = $db->getEscaped($relpath);
			$query = FOFQueryAbstract::getNew($db)
				->delete($db->nameQuote('#__admintools_customperms'))
				->where(
						$db->nameQuote('path').' REGEXP '.
						$db->quote('^'.$path_esc.'/[^/]*$')
				);
			$db->setQuery($query);
			$db->query();
		}

		$folders = $this->getState('folders',array());
		if(!empty($folders))
		{
			if(empty($relpath))
			{
				$query = FOFQueryAbstract::getNew($db)
				->delete($db->nameQuote('#__admintools_customperms'));
				
				$sqlparts = array();
				foreach($folders as $folder => $perms)
				{
					$sqlparts[] = $db->Quote($folder);
				}
				
				$query->where($db->nameQuote('path').' IN ('.implode(', ',$sqlparts).')');
				$db->setQuery($query);
				$db->query();
			}

			$sqlparts = array();
			foreach($folders as $folder => $perms)
			{
				if(!empty($perms))
				{
					$sqlparts[] = $db->Quote($folder).', '.$db->Quote($perms);
					if($apply) {
						$fixmodel->chmod(JPATH_ROOT.DS.$folder, $perms);
					}
				}
			}
			if(!empty($sqlparts))
			{
				$query = FOFQueryAbstract::getNew($db)
					->insert($db->nameQuote('#__admintools_customperms'))
					->columns(array(
						$db->nameQuote('path'),
						$db->nameQuote('perms')
					))->values($sqlparts);
				$db->setQuery($query);
				$db->query();
			}
		}

		$files = $this->getState('files',array());
		if(!empty($files))
		{
			if(empty($relpath))
			{
				$query = FOFQueryAbstract::getNew($db)
				->delete($db->nameQuote('#__admintools_customperms'));
				
				$sqlparts = array();
				foreach($files as $file => $perms)
				{
					$sqlparts[] = $db->Quote($file);
				}
				
				$query->where($db->nameQuote('path').' IN ('.implode(', ',$sqlparts).')');
				$db->setQuery($query);
				$db->query();
			}

			$sqlparts = array();
			foreach($files as $file => $perms)
			{
				if(!empty($perms))
				{
					$sqlparts[] = $db->Quote($file).', '.$db->Quote($perms);
					if($apply) {
						$fixmodel->chmod(JPATH_ROOT.DS.$file, $perms);
					}
				}
			}
			if(!empty($sqlparts))
			{
				$query = FOFQueryAbstract::getNew($db)
					->insert($db->nameQuote('#__admintools_customperms'))
					->columns(array(
						$db->nameQuote('path'),
						$db->nameQuote('perms')
					))->values($sqlparts);
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}