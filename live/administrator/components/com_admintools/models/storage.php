<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id: seoandlink.php 178 2011-02-16 08:43:23Z nikosdion $
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class AdmintoolsModelStorage extends JModel
{
	/** @var JRegistry */
	private $config = null;
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		if(!defined('FOF_INCLUDED')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_admintools/fof/include.php';
		}
	}
	
	public function getValue($key, $default = null)
	{
		if(is_null($this->config)) $this->load();
		
		return $this->config->getValue($key, $default);
	}
	
	public function setValue($key, $value, $save = false)
	{
		if(is_null($this->config)) $this->load();
		
		$x = $this->config->setValue($key, $value);
		if($save) $this->save();
		return $x;
	}
	
	public function load()
	{
		$db = JFactory::getDBO();
		$query = FOFQueryAbstract::getNew($db)
			->select($db->nameQuote('value'))
			->from($db->nameQuote('#__admintools_storage'))
			->where($db->nameQuote('key').' = '.$db->quote('cparams'));
		$db->setQuery($query);
		$res = $db->loadResult();
		
		$this->config = new JRegistry('admintools');
		if(!empty($res)) {
			$res = json_decode($res, true);
			$this->config->loadArray($res);
		}
	}
	
	public function save()
	{
		if(is_null($this->config)) $this->load();
		
		$db = JFactory::getDBO();
		$data = $this->config->toArray();
		$data = json_encode($data);
		
		$query = FOFQueryAbstract::getNew($db)
			->delete($db->nameQuote('#__admintools_storage'))
			->where($db->nameQuote('key').' = '.$db->quote('cparams'));
		$db->setQuery($query);
		$db->query();
		
		$object = (object)array(
			'key'		=> 'cparams',
			'value'		=> $data
		);
		$db->insertObject('#__admintools_storage', $object);
	}
}