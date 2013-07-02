<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JLoader::import('joomla.application.component.model');

class AdmintoolsModelWafconfig extends FOFModel
{
	/**
	 * Default configuration variables
	 * 
	 * @var array
	 */
	private $defaultConfig = array(
		'ipwl'			=> 0,
		'ipbl'			=> 0,
		'adminpw'		=> '',
		'blockinstall'	=> 0,
		'nonewadmins'	=> 1,
		'sqlishield'	=> 1,
		'antispam'		=> 0,
		'custgenerator'	=> 0,
		'generator'		=> '',
		'tpone'			=> 1,
		'tmpl'			=> 1,
		'template'		=> 1,
		'logbreaches'	=> 1,
		'emailonadminlogin' => '',
		'emailonfailedadminlogin' => '',
		'emailbreaches'	=> '',
		'muashield'		=> 1,
		'csrfshield'	=> 0,
		'rfishield'		=> 1,
		'dfishield'		=> 1,
		'badbehaviour'	=> 0,
		'bbstrict'		=> 0,
		'bbhttpblkey'	=> '',
		'bbwhitelistip'	=> '',
		'tsrenable'		=> 0,
		'tsrstrikes'	=> 3,
		'tsrnumfreq'	=> 1,
		'tsrfrequency'	=> 'hour',
		'tsrbannum'		=> 1,
		'tsrbanfrequency'	=> 'day',
		'spammermessage'	=> 'You are a spammer, hacker or an otherwise bad person.',
		'uploadshield'	=> 1,
		'xssshield'		=> 0,
		'nofesalogin'	=> 0,
		'tmplwhitelist'	=> 'component,system,raw',
		'neverblockips'	=> '',
		'emailafteripautoban'	=> '',
		'custom403msg'	=> '',
		'httpblenable'	=> 0,
		'httpblthreshold' => 25,
		'httpblmaxage'	=> 30,
		'httpblblocksuspicious'	=> 0,
		'allowsitetemplate' => 0,
		'trackfailedlogins' => 1,
		'use403view' => 0,
		'showpwonloginfailure' => 1,
		'iplookup' => 'ip-lookup.net/index.php?ip={ip}',
		'iplookupscheme' => 'http',
		'saveusersignupip' => 0,
		'twofactorauth' => 0,
		'twofactorauth_secret' => '',
		'twofactorauth_panic' => '',
	);

	/**
	 * Load the WAF configuration
	 * @return type 
	 */
	public function getConfig()
	{
		if(interface_exists('JModel')) {
			$params = JModelLegacy::getInstance('Storage','AdmintoolsModel');
		} else {
			$params = JModel::getInstance('Storage','AdmintoolsModel');
		}
		$config = array();
		foreach($this->defaultConfig as $k => $v) {
			$config[$k] = $params->getValue($k, $v);
		}
		$this->_migrateIplookup($config);
		return $config;
	}

	public function saveConfig($newParams)
	{
		$this->_migrateIplookup($newParams);
		
		if(interface_exists('JModel')) {
			$params = JModelLegacy::getInstance('Storage','AdmintoolsModel');
		} else {
			$params = JModel::getInstance('Storage','AdmintoolsModel');
		}

		foreach($newParams as $key => $value)
		{
			// Do not save unnecessary parameters
			if(!array_key_exists($key, $this->defaultConfig)) continue;
			$params->setValue($key,$value);
		}
		
		$params->save();
	}
	
	private function _migrateIplookup(&$data)
	{
		$iplookup = $data['iplookup'];
		$iplookupscheme = $data['iplookupscheme'];
		
		if(empty($iplookup)) {
			$iplookup = 'ip-lookup.net/index.php?ip={ip}';
			$iplookupscheme = 'http';
		}
		
		$test = strtolower($iplookup);
		if(substr($test, 0, 7) == 'http://') {
			$iplookup = substr($iplookup, 7);
			$iplookupscheme = 'http';
		} elseif(substr($test, 0, 8) == 'https://') {
			$iplookup = substr($iplookup, 8);
			$iplookupscheme = 'https';
		}
		
		$data['iplookup'] = $iplookup;
		$data['iplookupscheme'] = $iplookupscheme;
	}
}