<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class AdmintoolsModelWafconfig extends FOFModel
{
	var $defaultConfig = array(
		'ipwl'			=> 0,
		'ipbl'			=> 0,
		'adminpw'		=> '',
		'blockinstall'	=> 0,
		'nonewadmins'	=> 1,
		'poweredby'		=> '',
		'nojoomla'		=> 0,
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
	);

	function getConfig()
	{
		$params = JModel::getInstance('Storage','AdmintoolsModel');
		$config = array();
		foreach($this->defaultConfig as $k => $v) {
			$config[$k] = $params->getValue($k, $v);
		}
		return $config;
	}

	function saveConfig($newParams)
	{
		$params = JModel::getInstance('Storage','AdmintoolsModel');

		foreach($newParams as $key => $value)
		{
			$params->setValue($key,$value);
		}
		
		$params->save();
	}
}