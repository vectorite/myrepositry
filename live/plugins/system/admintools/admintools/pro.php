<?php
/*
 *  Administrator Tools
 *  Copyright (C) 2010-2011  Nicholas K. Dionysopoulos / AkeebaBackup.com
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

jimport('joomla.application.plugin');

class plgSystemAdmintoolsPro extends JPlugin
{
	private $cparams = null;
	
	private $hasGeoIPPECL = false;
	
	private $exceptions = array();
	
	private $skipFiltering = false;

	public function __construct(& $subject, $config = array())
	{
		jimport('joomla.html.parameter');
		jimport('joomla.plugin.helper');
		jimport('joomla.application.component.helper');
		$plugin = JPluginHelper::getPlugin('system', 'admintools');
		$defaultConfig = (array)($plugin);
		
		$config = array_merge($defaultConfig, $config);
		
		// Use the parent constructor to create the plugin object
		parent::__construct($subject, $config);
		
		// Load the components parameters
		jimport('joomla.application.component.model');
		require_once JPATH_ROOT.'/administrator/components/com_admintools/models/storage.php';
		$this->cparams = JModel::getInstance('Storage','AdmintoolsModel');
		
		// Check if the GeoIP PECL extension is loaded, otherwise load the
		// PHP-based implementation of the library.
		if(!function_exists('geoip_country_code_by_name')) {
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'geoip.php';
			$this->hasGeoIPPECL = false;
		} else {
			$this->hasGeoIPPECL = true;
		}
		
		// Load WAF exceptions
		$this->loadExceptions();
		if(empty($this->exceptions)) {
			$this->exceptions = array();
		} else {
			if(empty($this->exceptions[0])) {
				$this->skipFiltering = true;
			}
		}
		
		// Enforce the Update Site for Admin Tools Professional
		if(JFactory::getApplication()->isAdmin()) {
			$this->enforceUpdateSite();
		}
	}

	/**
	 * Hooks to the onAfterInitialize system event, the first time in the
	 * Joomla! page load workflow which fires a plug-in event
	 */
	public function onAfterInitialise()
	{
		// Automatic banning
		$this->AutoIPFiltering();
		
		// IP Blacklisting
		if($this->cparams->getValue('ipbl',0) == 1) $this->IPFiltering();
		
		// GeoBlocking
		$cnt = $this->cparams->getValue('geoblockcountries','');
		$con = $this->cparams->getValue('geoblockcontinents','');
		if( !empty($cnt) || !empty($con) ) {
			$this->geoBlocking();
		}

		// Check for admin access
		if($this->isAdminAccessAttempt())
		{
			// IP Whitelist filtering for back-end access
			if($this->cparams->getValue('ipwl',0) == 1) $this->adminIPFiltering();
			// Administrator "secret word" protection
			$this->adminPasswordProtection();
		}

		// Remove inactive users
		if($this->params->get('deleteinactive', 0) == 1) $this->removeInactiveUsers();
		
		$app = JFactory::getApplication();
		// Back-end stuff
		if(in_array($app->getName(),array('administrator','admin')))
		{
			// Block access to the extensions installer
			if($this->cparams->getValue('blockinstall',0) > 0) $this->blockInstall();
			// Disable editing of back-end users
			if($this->cparams->getValue('nonewadmins',0) == 1) $this->noNewAdmins();
			// Email on administrator access
			$emailonadmin = $this->cparams->getValue('emailonadminlogin','');
			if(!empty($emailonadmin)) $this->emailOnAdminLogin();

			// If there is an administrator secret word set, upon logout redirect to the site's home page
			$password = $this->cparams->getValue('adminpw','');
			if(!empty($password)) {
				$option = JRequest::getCmd('option','');
				$task = JRequest::getCmd('task','');
				$uid = JRequest::getInt('uid', 0);
				$loggingMeOut = true;
				if(!empty($uid)) {
					$myUID = JFactory::getUser()->id;
					$loggingMeOut = $myUID == $uid;
				}
				if( ($option == 'com_login') && ($task == 'logout') && $loggingMeOut ) {
					// Logout and redirect to the homepage
					$result = $app->logout();
					$baseURL = JURI::base();
					$baseURL = str_replace('/administrator','',$baseURL);
					$app->redirect($baseURL);
				}
			}
			
		}
		else
		// Front-end stuff
		{
			// PHP "Powered By" cloaking
			$poweredby = $this->cparams->getValue('poweredby','TMX-194.19');
			if(!empty($poweredby))
			{
				JResponse::setHeader('X-Powered-By',$poweredby);
			}
			
			if(!$this->skipFiltering) {
				// HTTP:BL integration
				if($this->cparams->getValue('httpblenable',0) == 1) $this->ProjectHoneypotHTTPBL();	
				// Bad Behaviour integration
				if($this->cparams->getValue('badbehaviour',0) == 1) $this->BadBehaviour();	
				// SQL Injection shielding
				if($this->cparams->getValue('sqlishield',0) == 1) $this->SQLiShield();
				// XSS shielding
				if($this->cparams->getValue('xssshield',0) == 1) $this->XSSShield();
				// Malicious User Agent shielding
				if($this->cparams->getValue('muashield',0) == 1) $this->MUAShield();
				// CSRF shield / anti-spam form filtering
				if($this->cparams->getValue('csrfshield',0) == 1) {
					$this->CSRFShield_BASIC();
				} elseif($this->cparams->getValue('csrfshield',0) == 2) {
					$this->CSRFShield_ADVANCED();
				}
				// RFIShield
				if($this->cparams->getValue('rfishield',1) == 1) $this->RFIShield();
				// DFIShield
				if($this->cparams->getValue('dfishield',1) == 1) $this->DFIShield();
				// UploadShield
				if($this->cparams->getValue('uploadshield',1) == 1) $this->UploadShield();
				// Anti-spam
				if($this->cparams->getValue('antispam',0) == 1) $this->antiSpam();

				// Disable module debugging (Joomla! 1.5)
				if(version_compare(JVERSION, '1.6.0', 'lt') && $this->cparams->getValue('tpone',0) == 1) $this->disableTpOne();
				// Disable template switching (tmpl)
				if($this->cparams->getValue('tmpl',0) == 1) $this->disableTmplSwitch();
				// Disable template switching (template)
				if($this->cparams->getValue('template',0) == 1) $this->disableTemplateSwitch();
			}
			
			// Custom URL redirection
			if($this->cparams->getValue('urlredirection',1) == 1) $this->customRouter();
			// Session optimizer
			if($this->params->get('sesoptimizer',0) == 1) $this->sessionOptimizer();
			// Session cleaner
			if($this->params->get('sescleaner',0) == 1) $this->sessionCleaner();
			// Cache cleaner
			if($this->params->get('cachecleaner',0) == 1) $this->cacheCleaner();
			// Cache expiration
			if($this->params->get('cacheexpire',0) == 1) $this->cacheExpire();
			// Temp-directory cleaning
			if($this->params->get('cleantemp',0) == 1) $this->cleanTemp();
			// Log purging
			if($this->params->get('purgelog',0) == 1) $this->purgeLog();
		}
	}

	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		
		if(in_array($app->getName(),array('administrator','admin'))) {
		} else {
			if($this->cparams->getValue('nojoomla',0) == 1)
			{
				$buffer = JResponse::getBody();
				$buffer = preg_replace('#joomla(!|[^.]{1,1})#iU', '', $buffer);
				JResponse::setBody($buffer);
			}
			
			if($this->cparams->getValue('csrfshield',0) == 2) {
				$this->CSRFShield_PROCESS();
			}			
		}
	}
	
	public function onAfterRoute()
	{
		// Naughty, naughty trick
		if(JFactory::getSession()->get('block',false,'com_admintools')) {
			// This is an underhanded way to short-circuit Joomla!'s internal router. Muwahaha!
			JRequest::set(array(
				'option'	=> 'com_admintools'
			), 'get', true);
		}
	}
	
	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();
		// Back-end stuff
		if(in_array($app->getName(),array('administrator','admin'))) {
			// Email on failed administrator access
			if(version_compare(JVERSION, '2.5.0', 'lt')) {
				$emailonfailedadmin = $this->cparams->getValue('emailonfailedadminlogin','');
				if(!empty($emailonfailedadmin)) $this->emailOnFailedAdminLogin();
			}
		} else {
			// Front-end stuff
			
			// Meta generator cloaking
			if($this->cparams->getValue('custgenerator',0) == 1) $this->cloakGenerator();
		}
	}
	
	/**
	 * Joomla! 1.5 failed login handler
	 * @param array $response 
	 */
	public function onLoginFailure($response)
	{
		if($this->cparams->getValue('trackfailedlogins', 0)) {
			$this->trackFailedLogin();
		} elseif(version_compare(JVERSION, '2.5.0', 'ge')) {
			$app = JFactory::getApplication();
			// Back-end stuff
			if(in_array($app->getName(),array('administrator','admin'))) {
				// Email on failed administrator access
				$emailonfailedadmin = $this->cparams->getValue('emailonfailedadminlogin','');
				if(!empty($emailonfailedadmin)) $this->emailOnFailedAdminLogin(true);
			}
		}
	}
	
	/**
	 * Joomla! 1.6+ failed login handler
	 * @param array $response 
	 */
	public function onUserLoginFailure($response)
	{
		$this->onLoginFailure($response);
	}
	
	/**
	 * User login event fired by Joomla! 1.5, redirected to the Joomla! 1.6 event 
	 * @param JUser $user
	 * @param array $options
	 */
	public function onUserLogin($user, $options)
	{
		return $this->onLoginUser($user, $options);
	}
	
	/**
	 * User login event fired by Joomla! 1.6
	 * @param unknown_type $user
	 * @param unknown_type $options
	 */
	public function onLoginUser($user, $options)
	{
		$app = JFactory::getApplication();
		$instance = $this->_getUser($user, $options);
		
		// Disallow front-end Super Administrator login
		if($this->cparams->getValue('nofesalogin',0) == 1) {
			if(!in_array($app->getName(),array('administrator','admin')))
			{
				// Is the user a Super Administrator
				$isSuperAdmin = false;
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 - Check all groups for core.admin privileges
					foreach($instance->groups as $group) {
						$isSuperAdmin |= JAccess::checkGroup($group, 'core.admin');
					}
				} else {
					// Joomla! 1.5 - Needs gid > 24
					$isSuperAdmin = ($instance->gid > 24);
				}
				// Block SAs
				if($isSuperAdmin) {
					$newopts = array();
					$app->logout($instance->id, $newopts);
					// Throw error
					if(version_compare( JVERSION, '1.6.0', 'ge' )) {
						$this->loadLanguage('plg_system_admintools');
						JError::raiseError(403,JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
					} else {
						JError::raiseError(403,JText::_('ACCESS DENIED'));
					}
					return false;
				}
			}
		}
		
		return true;
	}

	/**
	 * Filters back-end access by IP. If the IP of the visitor is not included
	 * in the whitelist, he gets redirected to the home page
	 */
	private function adminIPFiltering()
	{
		// Let's get a list of allowed IP ranges
		$db = JFactory::getDBO();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select($db->nq('ip'))
				->from($db->nq('#__admintools_adminiplist'));
		} else {
			$sql = 'SELECT `ip` FROM `#__admintools_adminiplist`';
		}
		$db->setQuery($sql);
		$ipTable = $db->loadResultArray();

		if(empty($ipTable)) return;

		$inList = $this->IPinList($ipTable);
		if($inList === false) {
			if(!$this->logBreaches('ipwl')) return;
			$autoban = $this->cparams->getValue('tsrenable', 0);
			if($autoban) $this->autoBan('ipwl');
			$this->redirectAdminToHome();
		}
	}


	/**
	 * Filters visitor access by IP. If the IP of the visitor is included in the
	 * blacklist, he gets a 403 error
	 */
	private function IPFiltering()
	{

		// Let's get a list of blocked IP ranges
		$db = JFactory::getDBO();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select($db->nq('ip'))
				->from($db->nq('#__admintools_ipblock'));
		} else {
			$sql = 'SELECT `ip` FROM `#__admintools_ipblock`';
		}
		$db->setQuery($sql);
		$ipTable = $db->loadResultArray();

		if(empty($ipTable)) return;

		$inList = $this->IPinList($ipTable);
		if($inList === true) {
			
			$message = $this->cparams->getValue('custom403msg','');
			if(empty($message)) {
				$message = 'ADMINTOOLS_BLOCKED_MESSAGE';
			}

			// Merge the default translation with the current translation
			$jlang = JFactory::getLanguage();
			// Front-end translation
			$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, null, true);
			// Do we have an override?
			$langOverride = $this->params->get('language_override','');
			if(!empty($langOverride)) {
				$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, $langOverride, true);
			}

			if((JText::_('ADMINTOOLS_BLOCKED_MESSAGE') == 'ADMINTOOLS_BLOCKED_MESSAGE') && ($message == 'ADMINTOOLS_BLOCKED_MESSAGE')) {
				$message = "Access Denied";
			} else {
				$message = JText::_($message);
			}

			// Show the 403 message
			if($this->cparams->getValue('use403view',0)) {
				// Using a view
				if(!JFactory::getSession()->get('block',false,'com_admintools'))
				{
					// This is inside an if-block so that we don't end up in an infinite rediretion loop
					JFactory::getSession()->set('block',true,'com_admintools');
					JFactory::getSession()->set('message',$message,'com_admintools');
					JFactory::getSession()->close();
					JFactory::getApplication()->redirect(JURI::base());
				}
			} else {
				// Using Joomla!'s error page
				JError::raiseError('403', $message);
			}
		}
	}

	/**
	 * Checks if the secret word is set in the URL query, or redirects the user
	 * back to the home page.
	 */
	private function adminPasswordProtection()
	{
		$password = $this->cparams->getValue('adminpw','');
		if(empty($password)) return;

		$myURI = JURI::getInstance();
		// If the "password" query param is not defined, the default value
		// "thisisnotgood" is returned. If it is defined, it will return null or
		// the value after the equal sign.
		$check = $myURI->getVar($password, 'thisisnotgood');
		if($check == 'thisisnotgood') {
			// Uh oh... Unauthorized access! Let's redirect the perp back to the
			// site's home page.
			if(!$this->logBreaches('adminpw')) return;
			$autoban = $this->cparams->getValue('tsrenable', 0);
			if($autoban) $this->autoBan('adminpw');
			$this->redirectAdminToHome();
		}
	}

	/**
	 * Cloak the generator meta tag
	 */
	private function cloakGenerator()
	{
		// Only do this for the front-end application
		$app = JFactory::getApplication();
		if($app->getName() != 'site') return;

		$generator = $this->cparams->getValue('generator','');
		if(empty($generator)) $generator = 'MYOB'; // Mind Your Own Business, peeping Tom!
		
		$document = JFactory::getDocument();
		$document->setGenerator($generator);
	}

	/**
	 * Disable module debugging (?tp=1 or tp=1)
	 */
	private function disableTpOne()
	{
		// Joomla! 1.6? It has no effect; it is now a Global Configuration option.
		if(version_compare(JVERSION,'1.6.0','ge')) return;
		
		// Allow all Super Administrators to do that
		$user = JFactory::getUser();
		if($user->gid == 25) return;
		
		// Block tp=1
		$tp = JRequest::getVar('tp',null);
		if( !empty($tp) ) {
			if(!$this->logBreaches('tpone')) return;
			$autoban = $this->cparams->getValue('tsrenable', 0);
			if($autoban) $this->autoBan('tpone');
			JRequest::setVar('tp',0);
		}
	}

	/**
	 * Disable template switching in the URL
	 */
	private function disableTmplSwitch()
	{
		$tmpl = JRequest::getCmd('tmpl',null);
		if(empty($tmpl)) return;
		
		$whitelist = $this->cparams->getValue('tmplwhitelist','component,system');
		if(empty($whitelist)) $whitelist = 'component,system';
		$temp = explode(',', $whitelist);
		$whitelist = array();
		foreach($temp as $item) {
			$whitelist[] = trim($item);
		}
		$whitelist = array_merge(array('component','system'), $whitelist);

		if(!is_null($tmpl) && !in_array($tmpl, $whitelist)) {
			if(!$this->logBreaches('tmpl')) return;
			$autoban = $this->cparams->getValue('tsrenable', 0);
			if($autoban) $this->autoBan('tmpl');
			JRequest::setVar('tmpl',null);
		}
	}

	/**
	 * Disable template switching in the URL
	 */
	private function disableTemplateSwitch()
	{
		static $siteTemplates = array();
		
		$template = JRequest::getCmd('template',null);
		$block = true;
		if( !empty($template) ) {
			// Exception: existing site templates are allowed
			if(version_compare(JVERSION, '1.7.0', 'ge') && JRequest::getCmd('option','com_mailto')) {
				// com_email URLs in Joomla! 1.7 and later have template= defined; force $allowsitetemplate in this case
				$allowsitetemplate = true;
			} else {
				// Otherwise, allow only of the switch is set
				$allowsitetemplate = $this->cparams->getValue('allowsitetemplate', 0);
			}
			if($allowsitetemplate) {
				if(empty($siteTemplates)) {
					jimport('joomla.filesystem.folder');
					$siteTemplates = JFolder::folders(JPATH_SITE.'/templates');
				}
				$block = !in_array($template, $siteTemplates);
			}
			
			if($block) {
				if(!$this->logBreaches('template')) return;
				$autoban = $this->cparams->getValue('tsrenable', 0);
				if($autoban) $this->autoBan('template');
				JRequest::setVar('template',null);
			}
		}
	}

	/**
	 * Fend off most common types of SQLi attacks. See the comments in the code
	 * for more security-minded information.
	 */
	private function SQLiShield()
	{
		// We filter all hashes separately to guard against underhanded injections.
		// For example, if the parameter registration to the $_REQUEST array is
		// GPCS, a GET variable will "hide" a POST variable during a POST request.
		// If the vulnerable component is, however, *explicitly* asking for the
		// POST variable, if we only check the $_REQUEST superglobal array we will
		// miss the attack: we will see the innocuous GET variable which is
		// registered to the $_REQUEST array due to higher precedence, while the
		// malicious POST payload makes it through to the component. When you are
		// talking about security you can leave NOTHING in the hands of Fate, or
		// it will come back to bite your sorry ass.
		$hashes = array('get','post');
		// Removing the jos_/#__ filter as it throws false positives on posts regarding SQL commands
		//$regex = '#[^\s]*([\s]|/\*(.*)\*/|;|\'|"|%22){1,}(union([\s]{1,}|/\*(.*)\*/){1,}select|select(([\s]{1,}|/\*(.*)\*/|`){1,}([\w]|_|-|\.|\*){1,}([\s]{1,}|/\*(.*)\*/|`){1,}(,){0,})*from([\s]{1,}|/\*(.*)\//){1,}[a-z0-9]{1,}_|(insert|replace)(([\s]{1,}|/\*(.*)\*/){1,})((low_priority|delayed|high_priority|ignore)([\s]{1,}|/\*(.*)\*/){1,}){0,}into|drop([\s]{1,}|/\*(.*)\*/){1,}(database|schema|event|procedure|function|trigger|view|index|server|(temporary([\s]{1,}|/\*(.*)\*/){1,}){0,1}table){1,1}([\s]{1,}|/\*(.*)\*/){1,}|update([\s]{1,}|/\*[^\w]*\/){1,}(low_priority([\s]{1,}|/\*[^\w]*\/){1,}|ignore([\s]{1,}|/\*[^\w]*\/){1,})?`?[\w]*_.*set|delete([\s]{1,}|/\*(.*)\*/){1,}((low_priority|quick|ignore)([\s]{1,}|/\*(.*)\*/){1,}){0,}from|benchmark([\s]{1,}|/\*(.*)\*/){0,}\(([\s]{1,}|/\*(.*)\*/){0,}[0-9]{1,}|\#__|jos_){1}#i';
		$regex = '#[^\s]*([\s]|/\*(.*)\*/|;|\'|"|%22){1,}(union([\s]{1,}|/\*(.*)\*/){1,}select|select(([\s]{1,}|/\*(.*)\*/|`){1,}([\w]|_|-|\.|\*){1,}([\s]{1,}|/\*(.*)\*/|`){1,}(,){0,})*from([\s]{1,}|/\*(.*)\//){1,}[a-z0-9]{1,}_|(insert|replace)(([\s]{1,}|/\*(.*)\*/){1,})((low_priority|delayed|high_priority|ignore)([\s]{1,}|/\*(.*)\*/){1,}){0,}into|drop([\s]{1,}|/\*(.*)\*/){1,}(database|schema|event|procedure|function|trigger|view|index|server|(temporary([\s]{1,}|/\*(.*)\*/){1,}){0,1}table){1,1}([\s]{1,}|/\*(.*)\*/){1,}|update([\s]{1,}|/\*[^\w]*\/){1,}(low_priority([\s]{1,}|/\*[^\w]*\/){1,}|ignore([\s]{1,}|/\*[^\w]*\/){1,})?`?[\w]*_.*set|delete([\s]{1,}|/\*(.*)\*/){1,}((low_priority|quick|ignore)([\s]{1,}|/\*(.*)\*/){1,}){0,}from|benchmark([\s]{1,}|/\*(.*)\*/){0,}\(([\s]{1,}|/\*(.*)\*/){0,}[0-9]{1,}){1}#i';

		foreach($hashes as $hash)
		{
			$allVars = JRequest::get($hash, 2);
			if(empty($allVars)) continue;

			if($this->match_array($regex,$allVars)) {
				$extraInfo = "Hash      : $hash\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($allVars, true);
				$extraInfo .= "\n";
				$this->blockRequest('sqlishield',null,$extraInfo);
			}
		}
	}

	/**
	 * Runs a RegEx match against a string or recursively against an array.
	 * In the case of an array, the first positive match against any level element
	 * of the array returns true and breaks the RegEx matching loop. If you pass
	 * any other data type except an array or string, it returns false.
	 * 
	 * @param string $regex The regular expressions to feed to preg_match
	 * @param mixed $array
	 * @return <type> 
	 */
	private function match_array($regex, $array, $striptags = false)
	{
		$result = false;
				
		if(is_array($array)) {
			foreach($array as $key => $value)
			{
				if(is_array($value)) {
					$result = $this->match_array($regex, $value, $striptags);
				} else {
					$v = $striptags ? strip_tags($value) : $value;
					$result = preg_match($regex, $v);
				}
				if($result) break;
			}
		} elseif(is_string($array)) {
			$v = $striptags ? strip_tags($array) : $array;
			$result = preg_match($regex, $v);
		}

		return $result;
	}

	/**
	 * The simplest anti-spam solution imagineable. Just blocks a request if a prohibited word is found.
	 */
	private function antiSpam()
	{
		$db = JFactory::getDBO();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select($db->nq('word'))
				->from($db->nq('#__admintools_badwords'))
				->group($db->nq('word'));
		} else {
			$sql = 'SELECT `word` FROM `#__admintools_badwords` GROUP BY `word`';
		}		
		$db->setQuery($sql);
		$badwords = $db->loadResultArray();

		if(empty($badwords)) return;

		$hashes = array('get','post');
		foreach($hashes as $hash)
		{
			$allVars = JRequest::get($hash, 2);
			if(empty($allVars)) continue;

			foreach($badwords as $word)
			{
				$regex = '#\b'.$word.'\b#i';
				if($this->match_array($regex,$allVars,true)) {
					$extraInfo = "Hash      : $hash\n";
					$extraInfo .= "Variables :\n";
					$extraInfo .= print_r($allVars, true);
					$extraInfo .= "\n";
					$this->blockRequest('antispam',null,$extraInfo);
				}
			}
		}
	}

	/**
	 * Performs custom redirections defined in the back-end of the component.
	 * It doesn't even require SEF to be turned on, he he!
	 */
	private function customRouter()
	{
		// Get the base path
		$basepath = ltrim( JURI::base(true), '/' );

		$myURL = JURI::getInstance();
		$fullurl = ltrim($myURL->toString(array('path','query','fragment')),'/');
		$path = ltrim( $myURL->getPath(), '/' );
		
		$pathLength = strlen($path);
		$baseLength = strlen($basepath);
		if($baseLength != 0)
		{
			if($pathLength > $baseLength) {
				$path = ltrim(substr($path,$baseLength),'/');
			} elseif($pathLength = $baseLength) {
				$path = '';
			}
		}

		$pathLength = strlen($fullurl);
		if($baseLength != 0)
		{
			if($pathLength > $baseLength) {
				$fullurl = ltrim(substr($fullurl,$baseLength),'/');
			} elseif($pathLength = $baseLength) {
				$fullurl = '';
			}
		}
		
		$db = JFactory::getDBO();
		
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select($db->nq('source'))
				->from($db->nq('#__admintools_redirects'))
				->where(
					'('.$db->nq('dest').' = '.$db->q($path).')'.
					' OR '.
					'('.$db->nq('dest').' = '.$db->q($fullurl).')'
				)->where($db->nq('published').' = '.$db->q('1'))
				->order($db->nq('ordering').' DESC')
				;
		} else {
			$sql = 'SELECT `source` FROM `#__admintools_redirects` WHERE (`dest` = '.
			$db->Quote($path).' OR `dest` = '.$db->Quote($fullurl).
			') AND `published` = 1 ORDER BY `ordering`';
		}		
		$db->setQuery($sql,0,1);
		$newURL = $db->loadResult();

		if(!empty($newURL))
		{
			$new = JURI::getInstance($newURL);
			$host = $new->getHost();
			$fragment = $new->getFragment();
			$query = $new->getQuery();

			if(empty($host))
			{
				$base = JURI::getInstance(JURI::base());
				$new->setHost( $base->getHost() );
				$new->setPort( $base->getPort() );
				//$new->setPath( $base->getPath().'/'.ltrim($newURL, '/') );
			}
			if(empty($query)) {
				$new->setQuery( $myURL->getQuery() );
			}
			if(empty($fragment)) {
				$new->setFragment($myURL->getFragment());
			}
			$path = $new->getPath();
			if(!empty($path)) {
				if(substr($path,0,1) != '/') {
					$new->setPath('/'.$path);
				}
			}
			$new->setScheme( $myURL->getScheme() );
			$app = JFactory::getApplication();
			$app->redirect($new->toString());
		}
	}

	private function sessionOptimizer()
	{
		$minutes = (int)$this->params->get('sesopt_freq', 0);
		if($minutes <= 0) return;

		$lastJob = $this->getTimestamp('session_optimize');
		$nextJob = $lastJob + $minutes*60;

		jimport('joomla.utilities.date');
		$now = new JDate();

		if($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('session_optimize');
			$this->sessionOptimize();
		}
	}

	/**
	 * Run the session cleaner (garbage collector) on a schedule
	 */
	private function sessionCleaner()
	{
		$minutes = (int)$this->params->get('ses_freq', 0);
		if($minutes <= 0) return;

		$lastJob = $this->getTimestamp('session_clean');
		$nextJob = $lastJob + $minutes*60;

		jimport('joomla.utilities.date');
		$now = new JDate();

		if($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('session_clean');
			$this->purgeSession();
		}
	}

	private function cacheCleaner()
	{
		$minutes = (int)$this->params->get('cache_freq', 0);
		if($minutes <= 0) return;

		$lastJob = $this->getTimestamp('cache_clean');
		$nextJob = $lastJob + $minutes*60;

		jimport('joomla.utilities.date');
		$now = new JDate();

		if($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('cache_clean');
			$this->purgeCache();
		}
	}

	private function cacheExpire()
	{
		$minutes = (int)$this->params->get('cacheexp_freq', 0);
		if($minutes <= 0) return;

		$lastJob = $this->getTimestamp('cache_expire');
		$nextJob = $lastJob + $minutes*60;

		jimport('joomla.utilities.date');
		$now = new JDate();

		if($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('cache_expire');
			$this->expireCache();
		}
	}

	private function cleanTemp()
	{
		$minutes = (int)$this->params->get('cleantemp_freq', 0);
		if($minutes <= 0) return;

		$lastJob = $this->getTimestamp('clean_temp');
		$nextJob = $lastJob + $minutes*60;

		jimport('joomla.utilities.date');
		$now = new JDate();

		if($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('clean_temp');
			$this->tempDirectoryCleanup();
		}
	}
	
	/**
	 * Checks if a non logged in user is trying to access the administrator
	 * application
	 */
	private function isAdminAccessAttempt()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if(in_array($app->getName(),array('administrator','admin')))
		{
			if($user->guest)
			{
				$option = JRequest::getCmd('option', null);
				$task = JRequest::getCmd('task', null);
				if(($option=='com_login') && ($task=='login'))
				{
					// Check for malicious direct post without a valid token
					// In this case, we "cheat" by pretending that it is a
					// login attempt we need to filter. If it's a legitimate
					// login request (username & password posted) we stop
					// filtering so as to allow Joomla! to parse the login
					// request.
					return !JRequest::getVar(JUtility::getToken(), false);
				}
				else
				{
					// Back-end login attempt
					return true;
				}
			}
			else
			{
				// Logged in admin user
				return false;
			}
		}
		else
		{
			// The request doesn't belong to the Administrator application
			return false;
		}
	}

	/**
	 * Checks if the user's IP is contained in a list of IPs or IP expressions
	 * @param array $ipTable The list of IP expressions
	 * @return null|bool True if it's in the list, null if the filtering can't proceed
	 */
	private function IPinList($ipTable = array())
	{
		// Sanity check
		if(!function_exists('ip2long')) return null;

		// Get our IP address
		$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
		if (strpos($ip, '::') === 0) {
			$ip = substr($ip, strrpos($ip, ':')+1);
		}
		// No point continuing if we can't get an address, right?
		if(empty($ip)) return null;
		$myIP = ip2long($ip);


		if(empty($ipTable)) return null;

		foreach($ipTable as $ipExpression)
		{
			if(strstr($ipExpression, '-'))
			{
				// Inclusive IP range, i.e. 123.123.123.123-124.125.126.127
				list($from,$to) = explode('-', $ipExpression, 2);
				$from = ip2long(trim($from));
				$to = ip2long(trim($to));
				// Swap from/to if they're in the wrong order
				if($from > $to) list($from, $to) = array($to, $from);
				if( ($myIP >= $from) && ($myIP <= $to) ) return true;
			}
			elseif(strstr($ipExpression, '/'))
			{
				// Netmask or CIDR provided, i.e. 123.123.123.123/255.255.255.0
				// or 123.123.123.123/24
				list($ip, $netmask) = explode('/',$ipExpression,2);
				$ip = ip2long(trim($ip));
				$netmask = trim($netmask);

				if(strstr($netmask,'.'))
				{
					// Convert netmask to CIDR
					$long = ip2long($netmask);
					$base = ip2long('255.255.255.255');
					$netmask = 32 - log(($long ^ $base)+1,2);
				}

				// Compare the IP to the masked IP
				$ip_binary_string = sprintf("%032b",$myIP);
				$net_binary_string = sprintf("%032b",$ip);
				if( substr_compare($ip_binary_string,$net_binary_string,0,$netmask) === 0 )
				{
					return true;
				}
			}
			else
			{
				// Standard IP address, i.e. 123.123.123.123 or partial IP address, i.e. 123.[123.][123.][123]
				$ipExpression = trim($ipExpression);
				$dots = 0;
				if(substr($ipExpression, -1) == '.') {
					// Partial IP address. Convert to CIDR and re-match
					foreach(count_chars($ipExpression,1) as $i => $val) {
						if($i == 46) $dots = $val;
					}
					switch($dots) {
						case 1:
							$netmask = '255.0.0.0';
							$ipExpression .= '0.0.0';
							break;
							
						case 2:
							$netmask = '255.255.0.0';
							$ipExpression .= '0.0';
							break;
							
						case 3:
							$netmask = '255.255.255.0';
							$ipExpression .= '0';
							break;
							
						default:
							$dots = 0;
					}
					
					if($dots) {
						// Convert netmask to CIDR
						$long = ip2long($netmask);
						$base = ip2long('255.255.255.255');
						$netmask = 32 - log(($long ^ $base)+1,2);
		
						// Compare the IP to the masked IP
						$ip_binary_string = sprintf("%032b",$myIP);
						$net_binary_string = sprintf("%032b",ip2long(trim($ipExpression)));
						if( substr_compare($ip_binary_string,$net_binary_string,0,$netmask) === 0 )
						{
							return true;
						}
					}
				}
				if(!$dots) {
					$ip = ip2long(trim($ipExpression));
					if($ip == $myIP) return true;
				}
			}
		}

		return false;
	}

	/**
	 * Blocks acess to com_install
	 */
	private function blockInstall()
	{
		$option = JRequest::getCmd('option','');
		if( !in_array($option,array('com_installer','com_plugins')) ) return;

		$blockSetting = $this->cparams->getValue('blockinstall',0);
		if($blockSetting == 0) return;

		$user = JFactory::getUser();
		if(!$user->guest)
		{
			if(version_compare( JVERSION, '1.6.0', 'ge' ))
			{
				// Joomla! 1.6 -- Only Super Users have the core.admin global privilege
				$coreAdmin = $user->authorise('core.admin');
				if( !empty($coreAdmin) && ($coreAdmin === true) ) {
					$coreAdmin = true;
				} else {
					$coreAdmin = false;
				}
				if( ($blockSetting == 1) && ($coreAdmin) ) return;
			}
			else
			{
				// Joomla! 1.5 - Super Users belong to hardcoded group 25
				if( ($blockSetting == 1) && ($user->gid == 25) ) return;	
			}

			$jlang = JFactory::getLanguage();
			$jlang->load('joomla', JPATH_ROOT, 'en-GB', true);
			$jlang->load('joomla', JPATH_ROOT, $jlang->getDefault(), true);
			$jlang->load('joomla', JPATH_ROOT, null, true);

			if(version_compare( JVERSION, '1.6.0', 'ge' )) {
				$this->loadLanguage('plg_system_admintools');
				JError::raiseError(403,JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			} else {
				JError::raiseError(403,JText::_('ACCESS DENIED'));
			}
		}
	}

	/**
	 * Disabled creating new admins or updating new ones
	 */
	private function noNewAdmins()
	{
		$option = JRequest::getCmd('option','');
		if($option != 'com_users') return;

		$task = JRequest::getCmd('task','');
		$gid = JRequest::getInt('gid',0);
		$jform = JRequest::getVar('jform',array(),'default','array');
		if( ($task == 'save') || ($task == 'apply') || ($task='user.apply') )
		{
			if(version_compare(JVERSION,'1.6.0','ge'))
			{
				// Joomla! 1.6
				if(empty($jform)) return; // Not editing, just core devs using the same task throughout the component, dammit
				$groups = $jform['groups'];
				
				$user = JFactory::getUser((int)$jform['id']);
				if(!empty($user->groups)) foreach($user->groups as $title => $gid) {
					if(!in_array($gid, $groups)) $groups[] = $gid;
				}
				
				$isAdmin = false;
				
				if(!empty($groups)) foreach($groups as $group) {
					// First try to see if the group has explicit backend login privileges
					$backend = JAccess::checkGroup($group, 'core.login.admin');
					// If not, is it a Super Admin (ergo inherited privileges)?
					if(is_null($backend)) $backend = JAccess::checkGroup($group, 'core.admin');
					$isAdmin |= $backend;
				}
			} else {
				// Joomla! 1.5 -- Check for group 24 or greater
				$isAdmin = $gid >= 24;
			}
			
			if($isAdmin)
			{
				$jlang = JFactory::getLanguage();
				$jlang->load('joomla', JPATH_ROOT, 'en-GB', true);
				$jlang->load('joomla', JPATH_ROOT, $jlang->getDefault(), true);
				$jlang->load('joomla', JPATH_ROOT, null, true);

				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					$this->loadLanguage('plg_system_admintools');
					JError::raiseError(403,JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
				} else {
					JError::raiseError(403,JText::_('ACCESS DENIED'));
				}
			}
		}
	}

	/**
	 * Redirects an administrator request back to the home page
	 */
	private function redirectAdminToHome()
	{
		// Get the current URI
		$myURI = JURI::getInstance();
		$path = $myURI->getPath();
		// Pop the administrator from the URI path
		$path_parts = explode('/',$path);
		$path_parts = array_slice($path_parts, 0, count($path_parts) - 2 );
		$path = implode('/', $path_parts);
		$myURI->setPath($path);
		// Unset any query parameters
		$myURI->setQuery('');
		// Redirect
		$app = JFactory::getApplication();
		$app->redirect( $myURI->toString() );
	}

	/**
	 * Blocks the request in progress and, optionally, logs the details of the
	 * blocked request for the admin to review later
	 * 
	 * @param string $reason Block reason code
	 * @param string $message The message to be shown to the user
	 * @param string $extraLogInformation Extra information to be written to the text log file
	 * @param string $extraLogTableInformation Extra information to be written to the extradata field of the log table (useful for JSON format)
	 */
	private function blockRequest($reason = 'other', $message = '', $extraLogInformation = '', $extraLogTableInformation = '')
	{
		if(empty($message)) {
			$customMessage = $this->cparams->getValue('custom403msg','');
			if(!empty($customMessage)) {
				$message = $customMessage;
			} else {
				$message = 'ADMINTOOLS_BLOCKED_MESSAGE';
			}
		}
		
		$r = $this->logBreaches($reason, $extraLogInformation, $extraLogTableInformation);
		if(!$r) return;
		
		$autoban = $this->cparams->getValue('tsrenable', 0);
		if($autoban) $this->autoBan($reason);

		// Merge the default translation with the current translation
		$jlang = JFactory::getLanguage();
		// Front-end translation
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, null, true);
		
		if((JText::_('ADMINTOOLS_BLOCKED_MESSAGE') == 'ADMINTOOLS_BLOCKED_MESSAGE') && ($message == 'ADMINTOOLS_BLOCKED_MESSAGE')) {
			$message = "Access Denied";
		} else {
			$message = JText::_($message);
		}

		// Show the 403 message
		if($this->cparams->getValue('use403view',0)) {
			// Using a view
			if(!JFactory::getSession()->get('block',false,'com_admintools'))
			{
				// This is inside an if-block so that we don't end up in an infinite rediretion loop
				JFactory::getSession()->set('block',true,'com_admintools');
				JFactory::getSession()->set('message',$message,'com_admintools');
				JFactory::getSession()->close();
				JFactory::getApplication()->redirect(JURI::base());
			}
		} else {
			// Using Joomla!'s error page
			JError::raiseError('403', $message);
		}
	}

	private function logBreaches($reason, $extraLogInformation = '', $extraLogTableInformation = '')
	{
		// === SANITY CHECK - BEGIN ===
		// Get our IP address
		$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
		if (strpos($ip, '::') === 0) {
			$ip = substr($ip, strrpos($ip, ':')+1);
		}
		// No point continuing if we can't get an address, right?
		if(empty($ip)) return false;
		$myIP = ip2long($ip);
		
		// Make sure it's not an IP in the safe list
		$safeIPs = $this->cparams->getValue('neverblockips','');
		if(!empty($safeIPs)) {
			$safeIPs = explode(',', $safeIPs);
			if(!empty($safeIPs)) {
				if($this->IPinList($safeIPs)) return false;
			}
		}
		
		// Make sure we don't have a list in the administrator white list
		if($this->cparams->getValue('ipwl',0) == 1) {
			$db = JFactory::getDBO();
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$sql = $db->getQuery(true)
					->select($db->nq('ip'))
					->from($db->nq('#__admintools_adminiplist'));
			} else {
				$sql = 'SELECT `ip` FROM `#__admintools_adminiplist`';
			}
			$db->setQuery($sql);
			$ipTable = $db->loadResultArray();
			if(!empty($ipTable)) {
				if($this->IPinList($ipTable)) return false;
			}			
		}
		// === SANITY CHECK - END ===	
		
		if($this->cparams->getValue('logbreaches',0))
		{
			// Logging requested. Fetch log information...
			$uri = JURI::getInstance();
			$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));
			
			$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
			if (strpos($ip, '::') === 0) {
				$ip = substr($ip, strrpos($ip, ':')+1);
			}
			
			jimport('joomla.utilities.date');
			$date = new JDate();
			
			$user = JFactory::getUser();
			if($user->guest) {
				$username = 'Guest';
			} else {
				$username = $user->username . ' (' . $user->name .' <'. $user->email .'>)';
			}
			
			if(@file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'assets'.DS.'geoip'.DS.'GeoIP.dat')) {
				if(!$this->hasGeoIPPECL) {
					require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'geoip.php';
					$gi = geoip_open(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'assets'.DS.'geoip'.DS.'GeoIP.dat',GEOIP_STANDARD);
					$country = geoip_country_code_by_addr($gi, $ip);
					$continent = geoip_region_by_addr($gi, $ip);
					geoip_close($gi);
				} else {
					$country = geoip_country_code_by_name($ip);
					$continent = geoip_continent_code_by_name($ip);
				}
			} else {
				$country = '(unknown country)';
				$continent = '(unknown continent)';
			}	
			
			// Logging to file
			$config = JFactory::getConfig();
			$logpath = $config->getValue('log_path');
			$fname = $logpath.DS.'admintools_breaches.log';
			// -- Check the file size. If it's over 1Mb, archive and start a new log.
			if(@file_exists($fname)) {
				$fsize = filesize($fname);
				if($fsize > 1048756) {
					if(@file_exists($fname.'.1')) {
						unlink($fname.'.1');
					}
					@copy($fname, $fname.'.1');
					@unlink($fname);
				}
			}
			// -- Log the exception
			$fp = @fopen($fname,'at');
			if($fp !== false) {
				fwrite($fp,str_repeat('-',79)."\n");
				fwrite($fp,"Blocking reason: ".$reason."\n".str_repeat('-',79)."\n");
				fwrite($fp,'Date/time : '.gmdate('Y-m-d H:i:s')." GMT\n");
				fwrite($fp,'URL       : '.$url."\n");
				fwrite($fp,'User      : '.$username."\n");
				fwrite($fp,'IP        : '.$ip."\n");
				fwrite($fp,'Country   : '.$country."\n");
				fwrite($fp,'Continent : '.$continent."\n");
				fwrite($fp,'UA        : '.$_SERVER['HTTP_USER_AGENT']."\n");
				if(!empty($extraLogInformation)) fwrite($fp,$extraLogInformation."\n");
				fwrite($fp, "\n\n");
				fclose($fp);
			}
		
			// ...and write a record to the log table
			$db = JFactory::getDBO();
			$logEntry = (object)array(
				'logdate'		=> $date->toMySQL(),
				'ip'			=> $ip,
				'url'			=> $url,
				'reason'		=> $reason,
				'extradata'		=> $extraLogTableInformation,
			);
			$db->insertObject('#__admintools_log', $logEntry);
		}
		
		$emailbreaches = $this->cparams->getValue('emailbreaches','');
		if(!empty($emailbreaches))
		{
			// Load the component's administrator translation files
			$jlang = JFactory::getLanguage();
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);
			
			// Get the site name
			$config = JFactory::getConfig();
			$sitename = $config->getValue('config.sitename');
			
			// Get the IP address
			$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
			if (strpos($ip, '::') === 0) {
				$ip = substr($ip, strrpos($ip, ':')+1);
			}
			
			// Create a link to llokup the IP
			$ip_link = 'http://ip-lookup.net/index.php?ip='.$ip;
			
			// Get the reason in human readable format
			$txtReason = JText::_('ATOOLS_LBL_REASON_'.strtoupper($reason));
			
			// Get extra information
			if($extraLogTableInformation) {
				list($logReason, $techURL) = explode('|', $extraLogTableInformation);
				$txtReason .= " ($logReason)";
			}
					
			// Send the email
			$mailer = JFactory::getMailer();
			$mailer->setSender(array( $config->getvalue('config.mailfrom'), $config->getvalue('config.fromname') ));
			$mailer->addRecipient($this->cparams->getValue('emailbreaches',''));
			$mailer->setSubject(JText::sprintf('ATOOLS_LBL_WAF_EMAILBREACHES_SUBJECT', $sitename));
			$mailer->setBody(JText::sprintf('ATOOLS_LBL_WAF_EMAILBREACHES_BODY', $sitename, $ip, $ip_link, $txtReason, $sitename));
			$mailer->Send();
		}
		
		return true;
	}

	/**
	 * Optimizes the session table. The idea is that as users log in and out,
	 * vast amounts of records are created and deleted, slowly fragmenting the
	 * underlying database file and slowing down user session operations. At
	 * some point, your site might even crash. By doing a periodic optimization
	 * of the sessions table this is prevented. An optimization per hour should
	 * be adequate, even for huge sites.
	 *
	 * Note: this is not necessary if you're not using the database to save
	 * session data. Using disk files, memcache, APC or other alternative caches
	 * has no impact on your database performance. In this case you should not
	 * enable this option, as you have nothing to gain.
	 */
	private function sessionOptimize()
	{
		$db = JFactory::getDBO();
		
		// First, make sure this is MySQL!
		if(!in_array(strtolower(str_replace('JDatabase', '', get_class(JFactory::getDbo()))), array('mysql','mysqli'))) return;
		
		$db->setQuery('CHECK TABLE '.$db->nameQuote('#__session'));
		$result = $db->loadObjectList();

		$isOK = false;
		if(!empty($result)) foreach($result as $row)
		{
			if( ($row->Msg_type == 'status') && (
				($row->Msg_text == 'OK') ||
				($row->Msg_text == 'Table is already up to date')
			) ) $isOK = true;
		}

		// Run a repair only if it is required
		if(!$isOK)
		{
			// The table needs repair
			$db->setQuery('REPAIR TABLE '.$db->nameQuote('#__session'));
			$db->query();
		}

		// Finally, optimize
		$db->setQuery('OPTIMIZE TABLE '.$db->nameQuote('#__session'));
		$db->query();
	}

	/**
	 * Purges expired sessions
	 */
	private function purgeSession()
	{
		jimport('joomla.session.session');

		$options = array();

		$conf = JFactory::getConfig();
		$handler =  $conf->getValue('config.session_handler', 'none');

		// config time is in minutes
		$options['expire'] = ($conf->getValue('config.lifetime')) ? $conf->getValue('config.lifetime') * 60 : 900;

		$storage = JSessionStorage::getInstance($handler, $options);
		$storage->gc($options['expire']);
	}

	/**
	 * Completely purges the cache
	 */
	private function purgeCache()
	{
		// Site client
		$client	= JApplicationHelper::getClientInfo(0);

		$er = @error_reporting(0);
		$cache = JFactory::getCache('');
		$cache->clean('sillylongnamewhichcantexistunlessyouareacompletelyparanoiddeveloperinwhichcaseyoushouldnotbewritingsoftwareokay','notgroup');
		@error_reporting($er);
	}

	/**
	 * Expires cache items
	 */
	private function expireCache()
	{
		$er = @error_reporting(0);
		$cache = JFactory::getCache('');
		$cache->gc();
		@error_reporting($er);
	}
	
	/**
	 * Cleans up the temporary director
	 */
	private function tempDirectoryCleanup()
	{
		$file = JPATH_ADMINISTRATOR.'/components/com_admintools/models/cleantmp.php';
		if(@file_exists($file)) {
			include_once($file);
			$model = new AdmintoolsModelCleantmp();
			$model->startScanning(); // This also runs the first batch of deletions
			$model->run(); // and this runs more deletions until the time is up
		}
	}

	/**
	 * Sets the timestamp for a specific scheduling task
	 * @param $key string The scheduling task key to set the timestamp parameter for
	 */
	private function setTimestamp($key)
	{
		jimport('joomla.utilities.date');
		$date = new JDate();

		$pk = 'timestamp_'.$key;
		$this->cparams->setValue($pk, $date->toUnix());
		
		$this->cparams->save();
	}

	/**
	 * Gets the last recorded timestamp for a specific scheduling task
	 * @param $key string The scheduling task key to retrieve the timestamp parameter
	 * @return int UNIX timestamp
	 */
	private function getTimestamp($key)
	{
		jimport('joomla.utilities.date');
		$pk = 'timestamp_'.$key;
		$timestamp = $this->cparams->getValue($pk,0);

		return $timestamp;
	}
	
	/**
	 * Sends an email upon accessing an administrator page other than the login screen
	 */
	private function emailOnAdminLogin()
	{
		// Make sure we don't fire when someone is still in the login page
		if($this->isAdminAccessAttempt()) return;

		// Double check
		$user = JFactory::getUser();
		if($user->guest) return;
		
		// Check if the session flag is set (avoid sending thousands of emails!)
		$session = JFactory::getSession();
		$flag = $session->get('waf.loggedin', 0, 'plg_admintools');
		if($flag == 1) return;
		
		// Load the component's administrator translation files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);
		
		// Get the username
		$username = $user->username;
		// Get the site name
		$config = JFactory::getConfig();
		$sitename = $config->getValue('config.sitename');
		// Get the IP address
		$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
		if (strpos($ip, '::') === 0) {
			$ip = substr($ip, strrpos($ip, ':')+1);
		}
		
		if(@file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'assets'.DS.'geoip'.DS.'GeoIP.dat')) {
			if(!$this->hasGeoIPPECL) {
				require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'geoip.php';
				$gi = geoip_open(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'assets'.DS.'geoip'.DS.'GeoIP.dat',GEOIP_STANDARD);
				$country = geoip_country_code_by_addr($gi, $ip);
				$continent = geoip_region_by_addr($gi, $ip);
				geoip_close($gi);
			} else {
				$country = geoip_country_code_by_name($ip);
				$continent = geoip_continent_code_by_name($ip);
			}
		} else {
			$country = '(unknown country)';
			$continent = '(unknown country)';
		}	
		
		// Construct the replacement table
		$substitutions = array(
			'[SITENAME]'	=> $sitename,
			'[USERNAME]'	=> $username,
			'[IP]'			=> $ip,
			'[UASTRING]'	=> $_SERVER['HTTP_USER_AGENT'],
			'[COUNTRY]'		=> $country,
			'[CONTINENT]'	=> $continent
		);
		
		$subject = JText::_('ATOOLS_LBL_WAF_EMAILADMINLOGIN_SUBJECT_21');
		$body = JText::_('ATOOLS_LBL_WAF_EMAILADMINLOGIN_BODY_21');
		
		foreach($substitutions as $k => $v) {
			$subject = str_replace($k, $v, $subject);
			$body = str_replace($k, $v, $body);
		}
		
		// Send the email
		$mailer = JFactory::getMailer();
		$mailer->setSender(array( $config->getvalue('config.mailfrom'), $config->getvalue('config.fromname') ));
		$mailer->addRecipient( $this->cparams->getValue('emailonadminlogin','') );
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->Send();
		// Set the flag to prevent sending more emails
		$session->set('waf.loggedin', 1, 'plg_admintools');
	}
	
	/**
	 * Sends an email upon a failed administrator login
	 * 
	 * @param $forcedFailure bool Wt to true to force a login failure trigger
	 */
	private function emailOnFailedAdminLogin($forcedFailure = false)
	{
		// Make sure we don't fire unless someone is still in the login page
		$user = JFactory::getUser();
		if(!$user->guest) return;
		
		$option = JRequest::getCmd('option');
		$task = JRequest::getCmd('task');
		if(($option != 'com_login') && !$forcedFailure) return;
		
		if(($task == 'login') || $forcedFailure) {
			// If we are STILL in the login task WITHOUT a valid user, we had a login failure.
			
			// Load the component's administrator translation files
			$jlang = JFactory::getLanguage();
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);
			
			// Fetch the username
			$username = JRequest::getString('username');
			
			// Get the site name
			$config = JFactory::getConfig();
			$sitename = $config->getValue('config.sitename');
			
			// Get the IP address
			$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
			if (strpos($ip, '::') === 0) {
				$ip = substr($ip, strrpos($ip, ':')+1);
			}
					
			// Send the email
			$mailer = JFactory::getMailer();
			$mailer->setSender(array( $config->getvalue('config.mailfrom'), $config->getvalue('config.fromname') ));
			$mailer->addRecipient( $this->cparams->getValue('emailonfailedadminlogin','') );
			$mailer->setSubject(JText::sprintf('ATOOLS_LBL_WAF_EMAILADMINFAILEDLOGIN_SUBJECT', $username, $sitename));
			$mailer->setBody(JText::sprintf('ATOOLS_LBL_WAF_EMAILADMINFAILEDLOGIN_BODY', $username, $sitename, $ip, $sitename));
			$mailer->Send();
		}
	}
	
	private function trackFailedLogin()
	{
		$user = JRequest::getCmd('username',null);
		$pass = JRequest::getCmd('password',null);
		if(empty($pass)) $pass = JRequest::getCmd('passwd',null);
		$extraInfo = null;
		if(!empty($user)) {
			if($this->cparams->getValue('showpwonloginfailure',1)) {
				$extraInfo = 'Username: '.$user.' -- Password: '.$pass;
			} else {
				$extraInfo = 'Username: '.$user;
			}
		}
		$this->logBreaches('loginfailure', $user, $extraInfo);
		$autoban = $this->cparams->getValue('tsrenable', 0);
		if($autoban) $this->autoBan('loginfailure');
	}
	
	/**
	 * Protects against a malicious User Agent string
	 */
	private function MUAShield()
	{
		// Some PHP binaries don't set the $_SERVER array under all platforms
		if(!isset($_SERVER)) return;
		if(!is_array($_SERVER)) return;
		// Some user agents don't set a UA string at all
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) return;
		
		$mua = $_SERVER['HTTP_USER_AGENT'];
		if(strstr($mua,'<?')) {
			$this->blockRequest('muashield');
		}
	}
	
	private function CSRFShield_BASIC()
	{
		// Do not activate on GET, HEAD and TRACE requests
		$method = strtoupper($_SERVER['REQUEST_METHOD']);
		if(in_array($method,array('GET','HEAD','TRACE'))) return;
		
		// Check the referer, if available
		$valid = true;
		$referer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '';
		if(!empty($referer)) {
			$jRefURI = JURI::getInstance($referer);
			$refererURI = $jRefURI->toString(array('host', 'port'));

			$jSiteURI = JURI::getInstance();
			$siteURI = $jSiteURI->toString(array('host', 'port'));
			
			$valid = ($siteURI == $refererURI);
		}
		
		if(!$valid) {
			$this->blockRequest('csrfshield');
		}
	}
	
	/**
	 * Applies basic HTTP referer filtering to POST, PUT, DELETE etc HTTP requests,
	 * usually associated with form submission.
	 */
	private function CSRFShield_GetFieldName()
	{
		static $fieldName = null;
		
		if(empty($fieldName)) {
			$config = JFactory::getConfig();
			$sitename = $config->getValue('config.sitename');
			$secret = $config->getValue('config.secret');
			$fieldName = md5($sitename.$secret);
		}
		
		return $fieldName;
	}
	
	/**
	 * Applies advanced reverse CAPTCHA checks to POST, PUT, DELETE etc HTTP
	 * requests, usually associated with form submission.
	 */
	private function CSRFShield_ADVANCED()
	{
		// Do not activate on GET, HEAD and TRACE requests
		$method = strtoupper($_SERVER['REQUEST_METHOD']);
		if(in_array($method,array('GET','HEAD','TRACE'))) return;
		
		// Check for the existence of a hidden field
		$valid = true;
		$hashes = array('get','post');
		$hiddenFieldName = $this->CSRFShield_GetFieldName();
		foreach($hashes as $hash) {
			$allVars = JRequest::get('default', 2);
			if(!array_key_exists($hiddenFieldName,$allVars)) continue;
			if(!empty($allVars[$hiddenFieldName])) {
				$this->blockRequest('csrfshield');
			}
		}
	}
	
	/**
	 * Processes all forms on the page, adding a reverse CAPTCHA field
	 * for advanced filtering
	 */
	private function CSRFShield_PROCESS()
	{
		$hiddenFieldName = $this->CSRFShield_GetFieldName();
		
		$buffer = JResponse::getBody();
		$buffer = preg_replace('#<[\s]*/[\s]*form[\s]*>#iU', '<input type="text" name="'.$hiddenFieldName.'" value="" style="float: left; position: absolute; z-index: 1000000; left: -10000px; top: -10000px;" /></form>', $buffer);
		JResponse::setBody($buffer);
	}
	
	/**
	 * Simple Remote Files Inclusion block. If any query string parameter contains a reference to an http[s]:// or ftp[s]://
	 * address it will be scanned. If the remote file looks like a PHP script, we block access.
	 */
	private function RFIShield()
	{
		$hashes = array('get','post');
		$regex = '#(http|ftp){1,1}(s){0,1}://.*#i';

		foreach($hashes as $hash)
		{
			$allVars = JRequest::get($hash, 2);
			if(empty($allVars)) continue;

			if($this->match_array_and_scan($regex,$allVars)) {
				$extraInfo = "Hash      : $hash\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($allVars, true);
				$extraInfo .= "\n";
				$this->blockRequest('rfishield',null,$extraInfo);
			}
		}
	}
	
	private function match_array_and_scan($regex, $array)
	{
		$result = false;

		if(is_array($array)) {
			foreach($array as $key => $value)
			{
				if(in_array($key, $this->exceptions)) continue;
				if(is_array($value)) {
					$result = $this->match_array_and_scan($regex, $value);
				} else {
					$result = preg_match($regex, $value);
				}
				if($result) {
					// Can we fetch the file directly?
					$fContents = @file_get_contents($value);
					if(!empty($fContents)) {
						$result = (strstr($fContents, '<?php') !== false);
						if($result) break;
					} else {
						$result = false;
					}
				}
			}
		} elseif(is_string($array)) {
			$result = preg_match($regex, $array);
			if($result) {
				// Can we fetch the file directly?
				$fContents = @file_get_contents($value);
				if(!empty($fContents)) {
					$result = (strstr($fContents, '<?php') !== false);
					if($result) break;
				} else {
					$result = false;
				}
			}
		}

		return $result;
	}

	/**
	 * Runs the Project Honeypot HTTP:BL integration
	 */
	private function ProjectHoneypotHTTPBL()
	{
		// Load parameters
		$httpbl_key = $this->cparams->getValue('bbhttpblkey','');
		$minthreat = $this->cparams->getValue('httpblthreshold',25);
		$maxage = $this->cparams->getValue('httpblmaxage',30);
		$suspicious = $this->cparams->getValue('httpblblocksuspicious',0);
		
		// Make sure we have an HTTP:BL  key set
		if(empty($httpbl_key)) return;
		
		// Get the IP address
		$reqip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
		if($reqip == '0.0.0.0') return false;
		if (strpos($reqip, '::') === 0) {
			$reqip = substr($reqip, strrpos($reqip, ':')+1);
		}
		
		// No point continuing if we can't get an address, right?
		if(empty($reqip)) return false;
		
		// IPv6 addresses are not supported by HTTP:BL yet
		if (strpos($reqip, ":")) return false;
		
		$find = implode('.', array_reverse(explode('.', $reqip)));
		$result = gethostbynamel($httpbl_key.".${find}.dnsbl.httpbl.org.");
		if (!empty($result)) {
			$ip = explode('.', $result[0]);
			
			if($ip[0] != 127) return; // Make sure it's a valid response
			if($ip[3] == 0) return; // Do not block search engines
			
			$block = ($ip[3] & 2) || ($ip[3] & 4); // Block harvesters and comment spammers
			if(!$suspicious && ($ip[3] & 1)) $block = false; // Do not block "suspicious" (not confirmed) IPs unless asked so
			
			$block = $block && ($ip[1] <= $maxage);
			$block = $block && ($ip[2] >= $minthreat);
			
			if($block) {
				$classes = array();
				if($ip[3] & 1) $classes[] = 'Suspicious';
				if($ip[3] & 2) $classes[] = 'Email Harvester';
				if($ip[3] & 4) $classes[] = 'Comment Spammer';
				$class = implode(', ', $classes);
				$extraInfo = <<<ENDINFO
HTTP:BL analysis for blocked spammer's IP address $reqip
	Attacker class		: $class
	Last activity		: $ip[1] days ago
	Threat level		: $ip[2] --> see http://is.gd/mAwMTo for more info

ENDINFO;
				$this->blockRequest('httpbl', '', $extraInfo);
			}
		}
	}
	
	/**
	 * Runs the Bad Behaviour anti-spam code
	 */
	private function BadBehaviour()
	{
		$strict = $this->cparams->getValue('bbstrict', 0) ? true : false;
		$httpbl_key = $this->cparams->getValue('bbhttpblkey','');
		$wlip = $this->cparams->getValue('bbwhitelistip','');
		if(empty($wlip)) {
			$wlip = array();
		} else {
			$wlip = explode(',', $wlip);
			if(!is_array($wlip)) {
				$wlip = array($wlip);
			}
		}
		$temp = array();
		if(!empty($wlip)) foreach($wlip as $ip) {
			$temp[] = trim($ip);
		}
		$wlip = $temp;
		
		
		define('BB2_CWD', dirname(__FILE__));
		define('BB2_TEST', 1);
		require_once(BB2_CWD . "/badbehaviour/generic.php");
		require_once(BB2_CWD . "/badbehaviour/core.inc.php");
		
		global $bb2_settings_defaults;
		$bb2_settings_defaults = array(
			'display_stats'		=> false,
			'strict'			=> $strict,
			'verbose'			=> false,
			'logging'			=> false,
			'offsite_forms'		=> true,
			'whitelist'			=> $wlip
		);
		
		$result = bb2_start($bb2_settings_defaults);
		if($result) {
			$text = JText::_('ADMINTOOLS_BLOCKED_MESSAGE_BADBEHAVIOR');
			if($text == 'ADMINTOOLS_BLOCKED_MESSAGE_BADBEHAVIOR') {
				$text = "Access Denied<br/>HTTP request error %s<br/>You can fix the problem yourself by visiting <a href='%s'>this URL</a> and following its instructions.";
			}
			$techhelpURL = 'http://www.ioerror.us/bb2-support-key?key='.$result;
			$extraInfo = <<<ENDINFO
Bad Behavior error code : $result
Technical help URL: $techhelpURL
ENDINFO;
			include_once BB2_CWD.'/badbehaviour/responses.inc.php';
			$reasonInfo = bb2_get_response($result);
			$logstring = $reasonInfo['log'];
			$extraLogData = "$logstring|$techhelpURL";
			$this->blockRequest('badbehaviour', sprintf($text, $result, $techhelpURL), $extraInfo, $extraLogData);
		}
	}
	
	private function geoBlocking()
	{
		if(!isset($_SERVER['REMOTE_ADDR'])) return;
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$continents = $this->cparams->getValue('geoblockcontinents','');
		$continents = empty($continents) ? array() : explode(',', $continents); 
		$countries = $this->cparams->getValue('geoblockcountries','');
		$countries = empty($countries) ? array() : explode(',', $countries);
		
		if(@file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'assets'.DS.'geoip'.DS.'GeoIP.dat')) {
			if(!$this->hasGeoIPPECL) {
				require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'geoip.php';
				$gi = geoip_open(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'assets'.DS.'geoip'.DS.'GeoIP.dat',GEOIP_STANDARD);
				$country = geoip_country_code_by_addr($gi, $ip);
				$continent = geoip_region_by_addr($gi, $ip);
				geoip_close($gi);
			} else {
				$country = geoip_country_code_by_name($ip);
				$continent = geoip_continent_code_by_name($ip);
			}
		} else {
			$country = '(unknown country)';
			$country = '(unknown continent)';
		}
		
		if(($continent) && !empty($continents)) {
			if(in_array($continent, $continents)) {
				$extraInfo = 'Continent : '.$continent;
				$this->blockRequest('geoblocking',null,$extraInfo);
			}
		}
		
		if(($country) && !empty($countries)) {
			if(in_array($country, $countries)) {
				$extraInfo = 'Country : '.$country;
				$this->blockRequest('geoblocking',null,$extraInfo);
			}
		}
	}
	
	/**
	 * Blocks visitors coming from an automatically banned IP. These suckers are repeat
	 * offenders. No courtesy from our part.
	 */
	private function AutoIPFiltering()
	{
		// We need to be able to get our own IP, right?
		if(!function_exists('ip2long')) return;

		// Get our IP address
		$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
		if($ip == '0.0.0.0') return;
		if (strpos($ip, '::') === 0) {
			$ip = substr($ip, strrpos($ip, ':')+1);
		}

		// No point continuing if we can't get an address, right?
		if(empty($ip)) return;
		$myIP = ip2long($ip);
		$myIPv4 = long2ip($myIP);
		
		// Let's get a list of blocked IP ranges
		$db = JFactory::getDBO();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select('*')
				->from($db->nq('#__admintools_ipautoban'))
				->where($db->nq('ip').' = '.$db->q($myIPv4));
		} else {
			$sql = 'SELECT * FROM `#__admintools_ipautoban` WHERE `ip` = '.$db->Quote($myIPv4);
		}
		$db->setQuery($sql);
		$record = $db->loadObject();

		if(empty($record)) return;
		
		// Is this record expired?
		jimport('joomla.utilities.date');
		$jNow = new JDate();
		$jUntil = new JDate($record->until);
		$now = $jNow->toUnix();
		$until = $jUntil->toUnix();
		if($now > $until) {
			// Ban expired. Clear the entry and allow the request to proceed.
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$sql = $db->getQuery(true)
					->delete($db->nq('#__admintools_ipautoban'))
					->where($db->nq('ip').' = '.$db->q($myIPv4));
			} else {
				$sql = 'DELETE FROM `#__admintools_ipautoban` WHERE `ip` = '.$db->Quote($myIPv4);
			}
			$db->setQuery($sql);
			$db->query();
			return;
		}

		// If we are still here, the user was auto-banned. Waste some of his time and
		// die with a 403 error. We try to waste as much time as possible. This will make
		// most hackbots give up or, at least, they will not waste tons of our server
		// resources trying to hack us.
		if(function_exists('ini_get')) {
			$maxexec = ini_get("max_execution_time");
			$waste = $maxexec - 3;
			if($waste < 0) $waste = 5; 
		} else {
			$waste = 5;
		}
		// -- Since we're wasting some time, let's clean up old entries :)
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->delete($db->nq('#__admintools_ipautoban'))
				->where($db->nq('until').' < '.$db->q($jNow->toMySQL()));
		} else {
			$sql = 'DELETE FROM `#__admintools_ipautoban` WHERE `until` < '.
				$db->Quote($jNow->toMySQL());
		}
		$db->setQuery($sql);
		$db->query();
		// -- Waste some time doing nothing
		sleep($waste);
		@ob_end_clean();
		header("HTTP/1.0 403 Forbidden");
		$spammerMessage = $this->cparams->getValue('spammermessage','');
		echo $spammerMessage;
		$app = JFactory::getApplication();
		$app->close();
	}

	private function autoBan($reason = 'other')
	{
		// We need to be able to get our own IP, right?
		if(!function_exists('ip2long')) return;
		
		// Get the IP
		$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
		if($ip == '0.0.0.0') return;
		if (strpos($ip, '::') === 0) {
			$ip = substr($ip, strrpos($ip, ':')+1);
		}
		
		// No point continuing if we can't get an address, right?
		if(empty($ip)) return;
		
		// Check for repeat offenses
		$db = JFactory::getDBO();
		$strikes = $this->cparams->getValue('tsrstrikes', 3);
		$numfreq = $this->cparams->getValue('tsrnumfreq', 1);
		$frequency = $this->cparams->getValue('tsrfrequency','hour');
		$mindatestamp = 0;
		switch($frequency)
		{
			case 'second':
				break;
				
			case 'minute':
				$numfreq *= 60;
				break;
				
			case 'hour':
				$numfreq *= 3600;
				break;
				
			case 'day':
				$numfreq *= 86400;
				break;
				
			case 'ever':
				$mindatestamp = 946706400; // January 1st, 2000
				break;
		}
		jimport('joomla.utilities.date');
		$jNow = new JDate();
		if($mindatestamp == 0) $mindatestamp = $jNow->toUnix() - $numfreq;
		$jMinDate = new JDate($mindatestamp);
		$minDate = $jMinDate->toMySQL();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select('COUNT(*)')
				->from($db->nq('#__admintools_log'))
				->where($db->nq('logdate').' >= '.$db->q($minDate));
		} else {
			$sql = 'SELECT COUNT(*) FROM `#__admintools_log` WHERE `logdate` >= '.$db->Quote($minDate);
		}		
		$db->setQuery($sql);
		$numOffenses = $db->loadResult();
		
		if($numOffenses < $strikes) return;
		
		// Block the IP
		$myIP = ip2long($ip);
		$myIPv4 = long2ip($myIP);
		
		$until = $jNow->toUnix();
		$numfreq = $this->cparams->getValue('tsrbannum', 1);
		$frequency = $this->cparams->getValue('tsrbanfrequency','hour');
		switch($frequency)
		{
			case 'second':
				$until += $numfreq;
				break;
				
			case 'minute':
				$numfreq *= 60;
				$until += $numfreq;
				break;
				
			case 'hour':
				$numfreq *= 3600;
				$until += $numfreq;
				break;
				
			case 'day':
				$numfreq *= 86400;
				$until += $numfreq;
				break;
				
			case 'ever':
				$until = 2145938400; // January 1st, 2038 (mind you, UNIX epoch runs out on January 19, 2038!)
				break;
		}
		jimport('joomla.utilities.date');
		$jMinDate = new JDate($until);
		$minDate = $jMinDate->toMySQL();
				
		$record = (object)array(
			'ip'		=> $myIPv4,
			'reason'	=> $reason,
			'until'		=> $minDate
		);
		
		$db->insertObject('#__admintools_ipautoban',$record);
		
		// Send an optional email
		if($this->cparams->getValue('emailafteripautoban','')) {
			// Get the site name
			$config = JFactory::getConfig();
			$sitename = $config->getValue('config.sitename');
			$substitutions = array(
				'[SITENAME]'	=> $sitename,
				'[IP]'			=> $myIPv4,
				'[UNTIL]'		=> $minDate
			);
			
			// Load the component's administrator translation files
			$jlang = JFactory::getLanguage();
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);
			
			$subject = JText::_('ATOOLS_LBL_WAF_AUTOIPBLOCKEMAIL_SUBJECT');
			$body = JText::_('ATOOLS_LBL_WAF_AUTOIPBLOCKEMAIL_BODY');
			
			foreach($substitutions as $k => $v) {
				$subject = str_replace($k, $v, $subject);
				$body = str_replace($k, $v, $body);
			}
			
			// Send the email
			$mailer = JFactory::getMailer();
			$mailer->setSender(array( $config->getvalue('config.mailfrom'), $config->getvalue('config.fromname') ));
			$mailer->addRecipient( $this->cparams->getValue('emailafteripautoban','') );
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->Send();
		}

	}
	
	/**
	 * Simple Direct Files Inclusion block.
	 */
	private function DFIShield()
	{
		$option = JRequest::getCmd('option','');
		$view = JRequest::getCmd('view','');
		$layout = JRequest::getCmd('layout','');
		
		// Special case: JCE
		if( ($option == 'com_jce') && ($view == 'editor') && ($layout == 'plugin') ) return;
		
		$hashes = array('get','post');

		foreach($hashes as $hash)
		{
			$allVars = JRequest::get($hash, 2);
			if(empty($allVars)) continue;

			if($this->match_array_dfi($allVars)) {
				$extraInfo = "Hash      : $hash\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($allVars, true);
				$extraInfo .= "\n";				
				$this->blockRequest('dfishield',null,$extraInfo);
			}
		}
	}
	
	private function match_array_dfi($array)
	{
		$result = false;

		if(is_array($array)) {
			foreach($array as $key => $value)
			{
				if(in_array($key, $this->exceptions)) continue;

				// If there's a null byte in the key, break
				if(strstr($key,"\u0000")) {
					$result = true;
					break;
				}
				
				// If there's no value, treat the key as a value
				if(empty($value)) $value = $key;
				
				// Scan the value
				if(is_array($value)) {
					$result = $this->match_array_dfi($value);
				} else {
					// If there's a null byte, break
					if(strstr($value,"\u0000")) {
						$result = true;
						break;
					}
					
					// If the value starts with a /, ../ or [a-z]{1,2}:, block
					if(preg_match('#^(/|\.\.|[a-z]{1,2}:\\\)#i', $value)) {
						// Fix 2.0.1: Check that the file exists
						$result = @file_exists($value);
						if(!$result) {
							$sillyParts = explode('../', $value);
							$realParts = array();
							foreach($sillyParts as $p) if(!empty($p)) $realParts[] = $p;
							$path = implode('/', $realParts);
							$result = @file_exists($path);
						}
						break;
					}
					
					if($result) break;
				}
			}
		}

		return $result;
	}

	/**
	 * Scans all uploaded files for PHP tags. This prevents uploading PHP files or crafted
	 * images with raw PHP code in them which may lead to arbitrary code execution under
	 * several common circumstances. It will also block files with null bytes in their
	 * filenames or with double extensions which include PHP in them (e.g. .php.jpg).
	 */
	private function UploadShield()
	{
		// Do we have uploaded files?
		$filesHash = JRequest::get('FILES',2);
		if(empty($filesHash)) return;

        $extraInfo = '';
		foreach($filesHash as $key => $descriptor) {
			if(is_array($descriptor) && !array_key_exists('tmp_name', $descriptor)) {
				$descriptors = $descriptor;
			} else {
				$descriptors[] = $descriptor;
			}
			unset($descriptor);
			
			foreach($descriptors as $descriptor) {
				$files = array();
				if(is_array($descriptor['tmp_name'])) {
					foreach($descriptor['tmp_name'] as $key => $value) {
						$files[] = array(
							'name'		=> $descriptor['name'][$key],
							'type'		=> $descriptor['type'][$key],
							'tmp_name'	=> $descriptor['tmp_name'][$key],
							'error'		=> $descriptor['error'][$key],
							'size'		=> $descriptor['size'][$key],
						);
					}
				} else {
					$files[] = $descriptor;
				}
				
				foreach($files as $fileDescriptor) {
					$tempName = $fileDescriptor['tmp_name'];
					$intendedName = $fileDescriptor['name'];

					$extraInfo .= "File descriptor :\n";
					$extraInfo .= print_r($fileDescriptor, true);
					$extraInfo .= "\n";

					// 1. Null byte check
					if(strstr($intendedName, "\u0000")) {
						$this->blockRequest('uploadshield',null,$extraInfo);
						return;
					}

					// 2. PHP-in-extension check
					$explodedName = explode('.', $intendedName);
					array_reverse($explodedName);

					// 2a. File extension is .php
					if( (count($explodedName) > 1) && (strtolower($explodedName[0]) == 'php')) {
						$this->blockRequest('uploadshield',null,$extraInfo);
						return;
					}

					// 2a. File extension is php.xxx
					if( (count($explodedName) > 2) && (strtolower($explodedName[1]) == 'php')) {
						$this->blockRequest('uploadshield',null,$extraInfo);
						return;
					}

					// 2b. File extensions is php.xxx.yyy
					if( (count($explodedName) > 3) && (strtolower($explodedName[2]) == 'php')) {
						$this->blockRequest('uploadshield',null,$extraInfo);
						return;
					}

					// 3. Contents scanner
					$fp = @fopen($tempName,'r');
					if($fp !== false) {
						$data = '';
						$extension = strtolower($explodedName[0]);
						while(!feof($fp)) {
							$buffer = @fread($fp, 131072);
							$data .= $buffer;
							if(strstr($buffer,'<?php')) {
								$this->blockRequest('uploadshield',null,$extraInfo);
								return;
							}
							if(in_array($extension,array('inc','phps','class','php3','php4','txt','dat','tpl','tmpl'))) {
								// These are suspicious text files which may have the short tag (<?) in them
								if(strstr($buffer,'<?')) {
									$this->blockRequest('uploadshield',null,$extraInfo);
									return;
								}
							}
							$data = substr($data, -4);
						}
						fclose($fp);
					}
				}
			}
		}
	}
	
	/**
	 * Tries to figure out if the given query string looks like an XSS attack. It's not watertight,
	 * but it's better than nothing.
	 * 
	 * Based largely on CodeIgniter's XSS cleanup code by EllisLab
	 * 
	 * @param string $str The string to filter
	 */
	private function looksLikeXSS($str)
	{
		// 1. Non-displayable character filtering
		static $non_displayables = null;

		if (is_null($non_displayables))
		{
			// All control characters except newline, carriage return, and horizontal tab (dec 09)
			$non_displayables = array(
				'/%0[0-8bcef]/',			// url encoded 00-08, 11, 12, 14, 15
				'/%1[0-9a-f]/',				// url encoded 16-31
				'/[\x00-\x08]/',			// 00-08
				'/\x0b/', '/\x0c/',			// 11, 12
				'/[\x0e-\x1f]/'				// 14-31
			);
		}
		
		foreach($non_displayables as $pattern) {
			$result = preg_match($pattern, $str);
			if($result) return true;
		}
		
		// 2. Partial standard character entities
		$test = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);
		if($test != $str) return true;
		
		// 3. Partial UTF16 two byte encoding
		$test = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;",$str);
		if($test != $str) return true;
		
		// 4. Conditioning
		// In this step we try to unwrap commonly encoded payloads for the next steps to work
		
		// 4a. URL decoding, in case an attacker tries to use URL-encoded payloads
		// Note: rawurldecode() is used to avoid decoding plus signs
		$str = rawurldecode($str);
		
		// 4b. Convert character entities to ASCII, as they are used a lot in XSS attacks
		$str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, 'attribute_callback'), $str);
		$str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, 'html_entity_decode_callback'), $str);
		
		// 5. Non-displayable character filtering (second pass, now that we decoded some more entities!)
		foreach($non_displayables as $pattern) {
			$result = preg_match($pattern, $str);
			if($result) return true;
		}
		
		// 6. Convert tab to spaces. Attackers may use ja	vascript to pass malicious code to us.
 		if (strpos($str, "\t") !== FALSE)
		{
			$str = str_replace("\t", ' ', $str);
		}
		
		// Store the converted string for later comparison
		$converted_string = $str;
		
		// 7. Filter out unsafe strings from list
		static $never_allowed_str = null;
		if(is_null($never_allowed_str)) {
			$never_allowed_str = array(
				'document.cookie',
				'document.write',
				'.parentNode',
				'.innerHTML',
				'window.location',
				'-moz-binding',
				'<!--',
				'-->',
				'<![CDATA['
			);
		}
		
		foreach ($never_allowed_str as $never)
		{
			if(strstr($str, $never) !== false) return true;   
		}
		
		// 8. Filter out unsafe strings from list of regular expressions
		static $never_allowed_regex = null;
		if(empty($never_allowed_regex)) {
			$never_allowed_regex = array(
				"javascript\s*:",
				"expression\s*(\(|&\#40;)",
				"vbscript\s*:",
				"Redirect\s+302",
			);
		}
		foreach ($never_allowed_regex as $pattern)
		{
			if(preg_match('#'.$pattern.'#i', $str)) return true;   
		}
		
		// 9. PHP filtering
		// Let's make sure that PHP tags (<? or <?php) are not present, while ensuring that
		// XML tags (<?xml) are not touched
		if($this->cparams->getValue('xssshield_allowphp',0) != 1) {
			$safe = str_replace('<?xml','--xml', $str);
			if(strstr($safe,'<?')) return true;
		}
		
		// 10. Compact exploded words like j a v a s c r i p t => javascript
		static $words = null;
		if(is_null($words)) {
			$words = array('javascript', 'expression', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');
		}
		foreach ($words as $word) {
			$temp = '';

			for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++)
			{
				$temp .= substr($word, $i, 1)."\s*";
			}

			// We only want to do this when it is followed by a non-word character
			$str = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', array($this, 'compact_exploded_words_callback'), $str);
		}
		
		// 11. Check for disallowed Javascript in links or img tags
		$original = $str;

		if (preg_match("/<a/i", $str)) {
			$str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", array($this, 'js_link_removal'), $str);
		}
		if($str != $original) return true;

		if (preg_match("/<img/i", $str)) {
			$str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", array($this, 'js_img_removal'), $str);
		}
		if($str != $original) return true;

		if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str)) {
			$str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
		}
		if($str != $original) return true;
		
		// 11. Detect Javascript event handlers
		$event_handlers = array('[^a-z_\-]on\w*','xmlns');
		$str = preg_replace("#<([^><]+?)(".implode('|', $event_handlers).")(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);
		if($str != $original) return true;
		
		// 12. Detect naughty PHP and Javascript code commonly used in exploits
		$result = preg_match('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', $str);
		if($result) return true;
		
		// -- At this point, the string has passed all XSS filters. We hope it contains nothing malicious
		// -- so we will report it as non-XSS.
		return false;
	}
	
	private function attribute_callback($match)
	{
		return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
	}
	
	private function html_entity_decode_callback($match)
	{
		$str = $match[0];
		
		if (stristr($str, '&') === FALSE) return $str;

		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		$str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
		return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
	}
	
	private function compact_exploded_words_callback($matches)
	{
		return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
	}

	private function js_link_removal($match)
	{
		$attributes = $this->filter_attributes(str_replace(array('<', '>'), '', $match[1]));
		return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
	}

	function js_img_removal($match)
	{
		$attributes = $this->filter_attributes(str_replace(array('<', '>'), '', $match[1]));
		return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
	}

	function filter_attributes($str)
	{
		$out = '';

		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$out .= preg_replace("#/\*.*?\*/#s", '', $match);
			}
		}

		return $out;
	}
	
	private function match_array_xss($array)
	{
		// Safe keys, i.e. keys which may contain stuff which looks like an XSS attack
		// TODO Move them to WAF Configuration
		static $safe_keys = array('password','passwd','token','_token','password1','password2','text');
		
		$result = false;
		
		if(is_array($array)) {
			foreach($array as $key => $value)
			{
				if(in_array($key,$safe_keys)) continue;
				if(!in_array($key, $this->exceptions)) continue;
				
				// If there's no value, treat the key as a value
				if(empty($value)) $value = $key;
				
				// Make sure the key is not an XSS attack
				// if($this->looksLikeXSS($key)) return true;
				
				// Scan the value
				if(is_array($value)) {
					$result = $this->match_array_xss($value);
				} else {
					$result = $this->looksLikeXSS($value); 
					if($result) break;
				}
			}
		}

		return $result;
	}

	/**
	 * Simple XSS attack block.
	 */
	private function XSSShield()
	{
		$hashes = array('get','post');

		foreach($hashes as $hash)
		{
			$allVars = JRequest::get($hash, 2);
			if(empty($allVars)) continue;

			if($this->match_array_xss($allVars)) {
				$extraInfo = "Hash      : $hash\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($allVars, true);
				$extraInfo .= "\n";
				$this->blockRequest('xssshield',null,$extraInfo);
			}
		}
	}

	/**
	 * Purges old log entries
	 */
	private function purgeLog()
	{
		$minutes = (int)$this->params->get('purgelog_freq', 0);
		if($minutes <= 0) return;

		$lastJob = $this->getTimestamp('purge_log');
		$nextJob = $lastJob + $minutes*60;

		jimport('joomla.utilities.date');
		$now = new JDate();

		if($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('purge_log');
			
			$maxage = (int)$this->params->get('purgelog_age', 0);
			$maxage = 24 * 3600 * $maxage;
			if($maxage > 0)
			{
				$now = time();
				$oldest = $now - $maxage;
				$jOldest = new JDate($oldest);
				$mOldest = $jOldest->toMySQL();
				
				$db = JFactory::getDBO();
				if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$sql = $db->getQuery(true)
					->delete($db->nq('#__admintools_log'))
					->where($db->nq('logdate').' < '.$db->q($mOldest));
				} else {
					$sql = 'DELETE FROM '.$db->nameQuote('#__admintools_log').' WHERE '.$db->nameQuote('logdate').
					' < '.$db->Quote($mOldest);
				}
				$db->setQuery($sql);
				$db->query();
			}
		}
	}
	
	function &_getUser($user, $options = array())
	{
		jimport('joomla.user.helper');
		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username'])))  {
			$instance->load($id);
			return $instance;
		}

		jimport('joomla.application.component.helper');
		$config   = JComponentHelper::getParams( 'com_users' );
		if(version_compare(JVERSION,'1.6.0','ge')) {
			$defaultUserGroup = $config->get('new_usertype', 2);
		} else {
			$usertype = $config->get( 'new_usertype', 'Registered' );	
		}

		$acl = JFactory::getACL();

		$instance->set( 'id'			, 0 );
		$instance->set( 'name'			, $user['fullname'] );
		$instance->set( 'username'		, $user['username'] );
		$instance->set( 'password_clear'	, $user['password_clear'] );
		$instance->set( 'email'			, $user['email'] );	// Result should contain an email (check)
		if(version_compare(JVERSION,'1.6.0','ge')) {
			$instance->set('usertype'		, 'deprecated');
			$instance->set('groups'		, array($defaultUserGroup));	
		} else {
			$instance->set( 'gid'			, $acl->get_group_id( '', $usertype));
			$instance->set( 'usertype'		, $usertype );	
		}
		
		return $instance;
	}
	
	private function loadExceptions()
	{
		// REMOVED - This doesn't work if this plugin is published BEFORE the
		// SEF router plugin (default)
		/*
		$app = JFactory::getApplication();
		if(!in_array($app->getName(),array('administrator','admin'))) {
			// We have to run the SQLiShield once, before parsing the URL through the router
			if($this->cparams->getValue('sqlishield',0) == 1) $this->SQLiShield();
		}
		// Break down the route
		$uri	= clone JURI::getInstance();
		$app	= JFactory::getApplication();
		$router = $app->getRouter();
		$result = $router->parse($uri);
		JRequest::set($result, 'get', false);
		*/
		
		// Now, proceed
		$option = JRequest::getCmd('option','');
		$view = JRequest::getCmd('view','');

		/*
        if(empty($option) && array_key_exists('option', $result)) $option = $result['option'];
        if(empty($view) && array_key_exists('view', $result)) $view = $result['view'];
		*/
		
		$db = JFactory::getDBO();
		
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select($db->nq('query'))
				->from($db->nq('#__admintools_wafexceptions'));
				if(empty($option)) {
					$sql->where(
						'('.$db->nq('option').' IS NULL OR '.
						$db->nq('option').' = '.$db->q('')
						.')'
					);
				} else {
					$sql->where(
						'('.$db->nq('option').' IS NULL OR '.
						$db->nq('option').' = '.$db->q('').' OR '.
						$db->nq('option').' = '.$db->q($option)
						.')'
					);
				}
				if(empty($view)) {
					$sql->where(
						'('.$db->nq('view').' IS NULL OR '.
						$db->nq('view').' = '.$db->q('')
						.')'
					);
				} else {
					$sql->where(
						'('.$db->nq('view').' IS NULL OR '.
						$db->nq('view').' = '.$db->q('').' OR '.
						$db->nq('view').' = '.$db->q($view)
						.')'
					);
				}
				$sql->group($db->nq('query'))
					->order($db->nq('query').' ASC');
				
		} else {
			$sql = 'SELECT `query` FROM `#__admintools_wafexceptions` WHERE ';
			if(empty($option)) {
				$sql .= "(`option` IS NULL OR `option` = '')";
			} else {
				$sql .= "(`option` IS NULL OR `option` = '' OR `option` = ".$db->Quote($option).")";
			}
			$sql .= ' AND ';
			if(empty($view)) {
				$sql .= "(`view` IS NULL OR `view` = '')";
			} else {
				$sql .= "(`view` IS NULL OR `view` = '' OR `view` = ".$db->Quote($view).")";
			}
			$sql .= ' GROUP BY `query` ORDER BY `query` ASC';
		}
		$db->setQuery($sql);
		$this->exceptions = $db->loadResultArray();
	}
	
	private function removeInactiveUsers()
	{
		// If the days are not at least 1, bail out
		$filtertype = (int)$this->params->get('deleteinactive', 1);
		$days = (int)$this->params->get('deleteinactive_days', 0);
		if($days <= 0) return;
		
		// Get up to 5 ids of users to remove
		$db = JFactory::getDbo();
		
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$sql = $db->getQuery(true)
				->select($db->nq('id'))
				->from($db->nq('#__users'))
				->where($db->nq('lastvisitDate').' = '.$db->q($db->getNullDate()))
				->where($db->nq('registerDate').' <= '."DATE_SUB(NOW(), INTERVAL $days DAY)")
				;
			switch($filtertype) {
				case 1:
					// Only users not yet activated
					$sql->where($db->nq('activation').' != '.$db->quote(''));
					break;

				case 2:
					// Only users already activated
					$sql->where($db->nq('activation').' = '.$db->quote(''));
					break;

				case 3:
					// All users who haven't logged in
					break;
			}
		} else {
			$sql = 'SELECT '.$db->nameQuote('id').' FROM '.$db->nameQuote('#__users').' WHERE '.$db->nameQuote('lastvisitDate').' = '
				.$db->quote('0000-00-00 00:00:00').' AND '.$db->nameQuote('registerDate').' <= '.
				"DATE_SUB(NOW(), INTERVAL $days DAY)";
			switch($filtertype) {
				case 1:
					// Only users not yet activated
					$sql .= ' AND '.$db->nameQuote('activation').' != \'\'';
					break;

				case 2:
					// Only users already activated
					$sql .= ' AND '.$db->nameQuote('activation').' = \'\'';
					break;

				case 3:
					// All users who haven't logged in
					break;
			}
		}
		
			
		$db->setQuery($sql,0,5);
		$ids = $db->loadResultArray();
		
		// Remove those inactive users
		if(!empty($ids)) {
			foreach($ids as $id) {
				$userToKill = JFactory::getUser($id);
				$userToKill->delete();
			}
		}
	}
	
	private function enforceUpdateSite()
	{	
		// Make sure the model file exists
		jimport('joomla.filesystem.file');
		$fName = JPATH_ADMINISTRATOR.'/components/com_admintools/models/cpanel.php';
		if(!JFile::exists($fName)) return;
		
		// Try loading the model file
		require_once($fName);
		$model = FOFModel::getAnInstance('Cpanels','AdmintoolsModel');
		$model->applyJoomlaExtensionUpdateChanges(true);
	}
}