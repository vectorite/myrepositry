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

// ------------------  standard plugin initialize function - don't change ---------------------------
$sefConfig = &Sh404sefFactory::getConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);
if ($dosef == false)
	return;
// ------------------  standard plugin initialize function - don't change ---------------------------

$originalVars = empty($originalUri) ? $vars : $originalUri->getQuery($asArray = true);

if (count($originalVars) == 2 && !empty($originalVars['Itemid']) && !empty($originalVars['option']))
{
	// use directly menu item
	$item = JFactory::getApplication()->getMenu()->getItem($originalVars['Itemid']);
	if (!empty($item))
	{
		$query = $item->query;
		// // when limitstart is not set, VM2 fetches start from the session, instead
		// of just assuming 0
		if (!empty($query['view']) && $query['view'] == 'category')
		{
			if (!isset($query['limitstart']))
			{
				$limitstart = 0;
				shAddToGETVarsList('limitstart', $limitstart);
				shRemoveFromGETVarsList('limitstart');
			}
		}
		ShlSystem_Log::debug('sh404sef', 'Inside com_virtuemart.php, building url from menu item route');
		$title = array($item->route);
	}
}

if (empty($title))
{
	ShlSystem_Log::debug('sh404sef', 'Loading component own router.php file from inside com_virtuemart.php');
	$functionName = ucfirst(str_replace('com_', '', $option)) . 'BuildRoute';
	if (!function_exists($functionName))
	{
		include(JPATH_ROOT . '/components/' . $option . '/router.php');
	}
	$helper = vmrouterHelper::getInstance($originalVars);
	$menuItem = $helper->menuVmitems;
	$shopName = empty($menuItem) ? 'vm' : $menuItem[0]->alias;

	// check for shop root url, else normal routing
	if (!empty($originalVars['view']) && $originalVars['view'] == 'virtuemart')
	{

		// if VM is homepage, then that's fine
		if (!shIsAnyHomepage($string))
		{
			// else use menu item alias as slug
			$title[] = $shopName;
			unset($originalVars['view']);
		}

	}
	else
	{
		// various checks as VM2 seem to produce funky non-sef urls
		if (!empty($originalVars['view']) && $originalVars['view'] == 'productdetails')
		{
			if (empty($originalVars['virtuemart_product_id']))
			{
				// request for product details, but product id is 0
				return;
			}
		}

		// when limitstart is not set, VM2 fetches start from the session, instead
		// of just assuming 0
		if (!empty($originalVars['view']) && $originalVars['view'] == 'category')
		{
			if (!isset($originalVars['limitstart']))
			{
				$limitstart = 0;
				shAddToGETVarsList('limitstart', $limitstart);
				shRemoveFromGETVarsList('limitstart');
				// router.php expects this to be start, not limitstart
				$originalVars['start'] = $limitstart;
			}
			else
			{
				$originalVars['start'] = $originalVars['limitstart'];
				unset($originalVars['limitstart']);
			}
		}

		$hasCategoryId = !empty($originalVars['view']) && $originalVars['view'] == 'category' && !empty($originalVars['virtuemart_category_id']);
		$originalUrl = $originalUri->get('_uri');
		$nonSefItemid = shGetURLVar($originalUrl, 'Itemid');

		// have router.php build url
		$title = $functionName($originalVars);

		// VM router set the Itemid for category links!!!!
		// instead of doing the routing
		if ($hasCategoryId)
		{
			// if no Itemid in the original non-sef url, but we have one now
			if (empty($nonSefItemid) && !empty($vars['Itemid']) && $vars['Itemid'] != $nonSefItemid)
			{
				$validItemid = empty($helper->menu['virtuemart_category_id'][$vars['virtuemart_category_id']]) ? 0
					: $helper->menu['virtuemart_category_id'][$vars['virtuemart_category_id']];
			}
			else
			{
				$validItemid = empty($vars['Itemid']) ? 0 : $vars['Itemid'];
			}

			if (!empty($validItemid))
			{
				// we now use the calculated Itemid, either the original one
				// or the one that was swapped in by Virtuemart router.php
				$Itemid = $validItemid;
				$vars['Itemid'] = $validItemid;
				$originalUri->setVar('Itemid', $validItemid);
				shAddToGETVarsList('Itemid', $validItemid);

				$item = JFactory::getApplication()->getMenu()->getItem($validItemid);
				if (!empty($item))
				{
					$menuRoute = $item->route;
					// now we have to either insert the menuRoute at the beginning of
					// what was returned by Virtuemart router.php, or replace it entirely.
					if (empty($title))
					{ // router.php returned nothing, use menu item alias
						$title = array($menuRoute);
					}
					else
					{ // router.php returned a title[] for that non-sef
						if (empty($nonSefItemid) && !empty($vars['Itemid']))
						{
							// there was no itemid in non-sef, but router.php or ourselves found one
							// drop the title[] returned by router
							$title = array($menuRoute);
						}
						else if (!empty($nonSefItemid) && !empty($vars['Itemid']) && $vars['Itemid'] != $nonSefItemid)
						{
							// there was an Itemid in non-sef, but router.php or ourselves changed it: use the new one
							// and drop the title[] returned by router
							$title = array($menuRoute);
						}
						else
						{
							// general use case: Itemid was not modified
							array_unshift($title, $menuRoute);
						}
					}
				}
			}

		}

	}
}

if (!empty($title))
{
	// add shop menu item, if asked to
	if ($sefConfig->shVmInsertShopName)
	{
		array_unshift($title, $shopName);
	}

	// add user defined prefix
	$prefix = shGetComponentPrefix($option);
	if (!empty($prefix))
	{
		array_unshift($title, $prefix);
	}

	$title = empty($title) ? $title : $pageInfo->router->encodeSegments($title);
}

// manage GET var lists ourselves, as Joomla router.php does not do it
if (!empty($vars))
{
	// there are some unused GET vars, we must transfer them to our mechanism, so
	// that they are eventually appended to the sef url
	foreach ($vars as $k => $v)
	{
		switch ($k)
		{
			case 'option':
			case 'Itemid':
			case 'lang':
				shRemoveFromGETVarsList($k);
				break;
			default:
			// if variable has not been used in sef url, add it to list of variables to be
			// appended to the url as query string elements
				if (array_key_exists($k, $originalVars))
				{
					shAddToGETVarsList($k, $v);
				}
				else
				{
					shRemoveFromGETVarsList($k);
				}
				break;
		}
	}
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef)
{
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString, (isset($limit) ? $limit : null),
		(isset($limitstart) ? $limitstart : null), (isset($shLangName) ? $shLangName : null), (isset($showall) ? $showall : null),
		$suppressPagination = true);
}
// ------------------  standard plugin finalize function - don't change ---------------------------
