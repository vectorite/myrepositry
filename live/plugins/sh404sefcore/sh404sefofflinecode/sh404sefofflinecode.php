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

$app = JFactory::getApplication();
if (!$app->isAdmin())
{
	$app->registerEvent('onAfterRoute', 'plgSh404sefofflinecode');
}

/**
 * Output a correct response code when site is offline
 * to let know search engines that site data
 * should not be discarded or discounted
 */
function plgSh404sefofflinecode()
{

	$app = JFactory::getApplication();

	// are we in the backend, or not offline ?
	if (!defined('SH404SEF_IS_RUNNING') || $app->isAdmin() || !$app->getCfg('offline'))
	{
		return;
	}

	// get plugin params
	$plugin = JPluginHelper::getPlugin('sh404sefcore', 'sh404sefofflinecode');
	$pluginParams = new JRegistry;
	$pluginParams->loadString($plugin->params);

	$disallowAdminAccess = $pluginParams->get('disallowAdminAccess', 0);

	if (!$disallowAdminAccess)
	{
		// admins are allowed, lets check if current user
		// is an admin, or if user is trying to log in
		$user = JFactory::getUser();
		$option = JRequest::getCmd('option');
		$task = JRequest::getCmd('task');
		if ($option == 'com_users' && $task == 'user.login')
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');
			$loggingIn = true;
		}
		else
		{
			$loggingIn = false;
		}

		// if already logged inadmin, or admin logging in, let it go
		if ($user->authorise('core.manage', 'com_sh404sef') || $loggingIn)
		{
			return;
		}
	}

	// need to render offline screen
	if ($disallowAdminAccess)
	{
		// admins not allowed, use our own
		// simplified template. Most likely being hacked so
		// close doors as much as possible
		$template = '';
		$file = 'sh404sef_offline_template.php';
		$directory = JPATH_ROOT . '/plugins/sh404sefcore';
	}
	else
	{
		// admin can access, use Joomla! offline template,
		// that includes a login form
		$template = $app->getTemplate();
		$file = 'offline.php';
		$directory = JPATH_THEMES;
	}

	$params = array('template' => $template, 'file' => $file, 'directory' => $directory);

	$document = JFactory::getDocument();
	$data = $document->render($app->getCfg('caching'), $params);

	// header : service unavailable
	JResponse::setHeader('HTTP/1.0 503', true);

	// give it some time
	$retryAfter = $pluginParams->get('retry_after_delay', 7400);

	// set header
	Jresponse::setheader('Retry-After', gmdate('D, d M Y H:i:s', time() + $retryAfter) . ' GMT');

	// echo document
	JResponse::setBody($data);
	echo JResponse::toString($app->getCfg('gzip'));

	// and terminate
	$app->close();

}

