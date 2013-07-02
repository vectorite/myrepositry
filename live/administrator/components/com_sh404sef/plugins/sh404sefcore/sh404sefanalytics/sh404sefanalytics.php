<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$mainframe = JFactory::getApplication();
$mainframe->registerEvent('onShInsertAnalyticsSnippet', 'plgSh404sefAnalyticsCustomVars');

function plgSh404sefAnalyticsCustomVars(&$customVars, $sefConfig)
{

	// add custom variable : page creation time
	if ($sefConfig->analyticsEnableTimeCollection)
	{
		$profiler = JProfiler::getInstance('sh404sef_profiler');
		$profiler->mark('');
		$pageCreationTime = $profiler->getBuffer();

		//extract Data
		$pageCreationTime = str_replace(array('<code>sh404sef_profiler', '</code>'), '', $pageCreationTime[0]);
		$tmp = explode(' ', trim($pageCreationTime)); // we may have memory report attached
		$time = trim($tmp[0]);

		// classify exact time into predefined categories for encoding
		$time = Sh404sefHelperAnalytics::classifyTime($time);

		// same for memory used
		$memory = empty($tmp[1]) ? 0 : sh404sefHelperAnalytics::classifyMemory($profiler->getMemory() / (1024 * 1024));

		// store results into incoming array
		$customVars = is_null($customVars) ? array() : $customVars;
		$customVars[SH404SEF_ANALYTICS_TIME_CUSTOM_VAR] = empty($customVars[SH404SEF_ANALYTICS_TIME_CUSTOM_VAR]) ? new stdClass()
			: $customVars[SH404SEF_ANALYTICS_TIME_CUSTOM_VAR];
		$customVars[SH404SEF_ANALYTICS_TIME_CUSTOM_VAR]->name = 'Page creation time and ram';
		$customVars[SH404SEF_ANALYTICS_TIME_CUSTOM_VAR]->value = ($time << 4) + $memory;

	}

	// add custom variable : user logged in
	if ($sefConfig->analyticsEnableUserCollection)
	{
		$user = clone(JFactory::getUser());
		$customVars = is_null($customVars) ? array() : $customVars;
		$customVars[SH404SEF_ANALYTICS_USER_CUSTOM_VAR] = empty($customVars[SH404SEF_ANALYTICS_USER_CUSTOM_VAR]) ? new stdClass()
			: $customVars[SH404SEF_ANALYTICS_USER_CUSTOM_VAR];
		$customVars[SH404SEF_ANALYTICS_USER_CUSTOM_VAR]->name = 'Logged-in user';
		if ($user->guest)
		{
			$userType = 'anonymous';
		}
		else
		{
			$groups = Sh404sefHelperGeneral::getUserGroups();
			$userGroup = array_shift($user->groups);
			$userType = $groups[$userGroup];
		}
		$customVars[SH404SEF_ANALYTICS_USER_CUSTOM_VAR]->value = htmlentities($userType, ENT_QUOTES, 'UTF-8');
	}

	return true;

}

