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

jimport('joomla.plugin.plugin');

class plgSh404sefcoresh404sefSocial extends JPlugin
{

	private $_params = null;
	private $_enabledButtons = array('facebooklike', 'facebooksend', 'twitter', 'googleplusone', 'googlepluspage', 'pinterestpinit');

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject);
		// get plugin params
		$plugin = JPluginHelper::getPlugin('sh404sefcore', 'sh404sefsocial');
		$this->_params = new JRegistry;
		$this->_params->loadString($plugin->params);
	}

	/**
	 * Insert appropriate script links into document
	 */
	public function onSh404sefInsertSocialButtons(&$page, $sefConfig)
	{

		$app = JFactory::getApplication();

		// are we in the backend - that would be a mistake
		if (!defined('SH404SEF_IS_RUNNING') || $app->isAdmin())
		{
			return;
		}

		// don't display on errors
		$pageInfo = Sh404sefFactory::getPageInfo();
		if (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		{
			return;
		}

		// regexp to catch plugin requests
		$regExp = '#{sh404sef_social_buttons(.*)}#Uus';

		// search for our marker}
		if (preg_match_all($regExp, $page, $matches, PREG_SET_ORDER) > 0)
		{

			// process matches
			foreach ($matches as $id => $match)
			{
				$url = '';
				$imageSrc = '';
				$imageDesc = '';
				// extract target URL
				if (!empty($match[1]))
				{

					//normally, there is no quotes around attributes
					// but a description will probably have spaces, so we
					// now try to get attributes from both syntax
					jimport('joomla.utilities.utility');
					$attributes = JUtility::parseAttributes($match[1]);
					$url = empty($attributes['url']) ? '' : $attributes['url'];
					$imageSrc = empty($attributes['img']) ? '' : $attributes['img'];
					$imageDesc = empty($attributes['desc']) ? '' : $attributes['desc'];
					$type = empty($attributes['type']) ? '' : $attributes['type'];

					// now process usual tags
					$raw = explode(' ', $match[1]);
					$attributes = array();
					$enabledButtons = array();
					foreach ($raw as $attribute)
					{
						$attribute = JString::trim($attribute);
						if (strpos($attribute, '=') === false)
						{
							continue;
						}
						$bits = explode('=', $attribute);
						if (empty($bits[1]))
						{
							continue;
						}
						switch ($bits[0])
						{
							case 'url':
								if (empty($url))
								{
									$base = JURI::base(true);
									if (substr($bits[1], 0, 10) == 'index.php?')
									{
										$url = JURI::getInstance()->toString(array('scheme', 'host', 'port')) . JRoute::_($bits[1]);
									}
									else if (substr($bits[1], 0, JString::strlen($base)) == $base)
									{
										$url = JURI::getInstance()->toString(array('scheme', 'host', 'port')) . $bits[1];
									}
									else if (substr($bits[1], 0, 1) == '/')
									{
										$url = JString::rtrim(JURI::base(), '/') . $bits[1];
									}
									else
									{
										$url = $bits[1];
									}
								}
								break;
							case 'type':
								$newType = trim(strtolower($bits[1]));
								if (!in_array($newType, $enabledButtons))
								{
									$enabledButtons[] = $newType;
								}
								break;
							case 'img':
								$imageSrc = empty($imageSrc) ? strtolower($bits[1]) : $imageSrc;
								break;
						}
					}

					if (!empty($enabledButtons))
					{
						$this->_enabledButtons = $enabledButtons;
					}
				}
				// get buttons html
				$buttons = $this->_sh404sefGetSocialButtons($sefConfig, $url, $context = '', $content = null, $imageSrc, $imageDesc);
				$buttons = str_replace('\'', '\\\'', $buttons);

				// replace in document
				$page = str_replace($match[0], $buttons, $page);
			}
		}

		// insert head links as needed
		$this->_insertSocialLinks($page, $sefConfig);

	}

	public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
	{

		$app = JFactory::getApplication();

		// are we in the backend - that would be a mistake
		if (!defined('SH404SEF_IS_RUNNING') || $app->isAdmin())
		{
			return;
		}

		// don't display on errors
		$pageInfo = Sh404sefFactory::getPageInfo();
		if (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		{
			return '';
		}

		if ($this->_params->get('buttonsContentLocation', 'onlyTags') == 'before')
		{
			$buttons = $this->_sh404sefGetSocialButtons(Sh404sefFactory::getConfig(), $url = '', $context, $row);
		}
		else
		{
			$buttons = '';
		}
		return $buttons;

	}
	public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
	{

		if ($this->_params->get('buttonsContentLocation', 'onlyTags') == 'after')
		{
			$buttons = $this->_sh404sefGetSocialButtons(Sh404sefFactory::getConfig(), $url = '', $context, $row);
		}
		else
		{
			$buttons = '';
		}
		return $buttons;

	}

	public function onSh404sefInsertFBJavascriptSDK(&$page, $sefConfig)
	{

		static $_inserted = false;

		if ($sefConfig->shMetaManagementActivated && !$_inserted
			&& ($this->_params->get('enableFbLike', true) || $this->_params->get('enableFbSend', true)))
		{

			$_inserted = true;

			// G! use underscore in language tags
			$locale = str_replace('-', '_', JFactory::getLanguage()->getTag());

			// append Facebook SDK
			$socialSnippet = "
      <div id='fb-root'></div><script type='text/javascript'>

      // Load the SDK Asynchronously
      (function(d){
      var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
      js = d.createElement('script'); js.id = id; js.async = true;
      js.src = '//connect.facebook.net/" . $locale
				. "/all.js';
      d.getElementsByTagName('head')[0].appendChild(js);
    }(document));

    </script>";

			// use page rewrite utility function to insert as needed
			$page = shPregInsertCustomTagInBuffer($page, '<\s*body[^>]*>', 'after', $socialSnippet, $firstOnly = 'first');
		}
	}

	private function _sh404sefGetSocialButtons($sefConfig, $url = '', $context = '', $content = null, $imageSrc = '', $imageDesc = '')
	{

		// if no URL, use current
		if (empty($url))
		{
			// no url set on social button tag, we should
			// use current URL, except if we are on a page
			// where this would cause the wrong url to be shared
			// try identify this condition
			if ($this->_shouldDisplaySocialButtons($sefConfig, $context, $content))
			{
				Sh404sefHelperShurl::updateShurls();
				$pageInfo = Sh404sefFactory::getPageInfo();
				$url = !$this->_params->get('useShurl', true) || empty($pageInfo->shURL) ? JURI::current()
					: JURI::base() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $pageInfo->shURL;
			}
			else
			{
				return '';
			}
		}

		// buttons html
		$buttonsHtml = '';

		// get language from Joomla
		$longLang = JFactory::getLanguage()->getTag();
		// networks use en_GB, not en-GB
		$shortLang = substr($longLang, 0, 2);

		// we wrap buttons in unordered list
		$wrapperOpen = '<li>';
		$wrapperClose = '</li>';

		// Tweet
		if ($this->_params->get('enableTweet', true) && in_array('twitter', $this->_enabledButtons))
		{
			$buttonsHtml .= $wrapperOpen . '<a href="https://twitter.com/share" data-via="' . $this->_params->get('viaAccount', '')
				. '" data-count="' . $this->_params->get('tweetLayout', 'none') . '" data-url="' . $url . '" data-lang="' . $shortLang
				. '" class="twitter-share-button">Tweet</a>' . $wrapperClose;
		}

		// plus One
		if ($this->_params->get('enablePlusOne', true) && in_array('googleplusone', $this->_enabledButtons))
		{
			$buttonsHtml .= $wrapperOpen . '<g:plusone callback="_sh404sefSocialTrackGPlusTracking" annotation="'
				. $this->_params->get('plusOneAnnotation', 'none') . '" size="' . $this->_params->get('plusOneSize', '') . '" href="' . $url
				. '"></g:plusone>' . $wrapperClose;
		}

		// Google plus page badge
		$page = $this->_params->get('googlePlusPage', '');
		$page = JString::trim($page, '/');
		if ($this->_params->get('enableGooglePlusPage', true) && in_array('googlepluspage', $this->_enabledButtons) && !empty($page))
		{
			$buttonsHtml .= $wrapperOpen . '<a class="google-page-badge" onclick="_sh404sefSocialTrack.GPageTracking(\'/' . $page . '/\', \'' . $url
				. '\')" href="https://plus.google.com/' . $page . '/?prsrc=3">';

			// badge image
			switch ($this->_params->get('googlePlusPageSize', 'medium'))
			{
				case 'small':
					$size = '16';
					$buttonsHtml .= '<div style="display: inline-block;">';
					// custom text
					if ($this->_params->get('googlePlusCustomText', ''))
					{
						$buttonsHtml .= '<span style="float: left; font: bold 13px/16px arial,sans-serif; margin-right: 4px;">'
							. htmlspecialchars($this->_params->get('googlePlusCustomText', ''))
							. '</span><span style="float: left; font: 13px/16px arial,sans-serif; margin-right: 11px;">'
							. htmlspecialchars($this->_params->get('googlePlusCustomText2', '')) . '</span>';
					}

					$buttonsHtml .= '<div style="float: left;"><img src="https://ssl.gstatic.com/images/icons/gplus-16.png" width="16" height="16" style="border: 0;"/></div><div style="clear: both"></div>';

					break;
				case 'large':
					$size = '64';
					$buttonsHtml .= '<div style="display: inline-block; *display: inline;"><div style="text-align: center;"><img src="https://ssl.gstatic.com/images/icons/gplus-64.png" width="64" height="64" style="border: 0;"></img></div><div style="font: bold 13px/16px arial,sans-serif; text-align: center;">'
						. $this->_params->get('googlePlusCustomText', '')
						. '</div><div style="font: 13px/16px arial,sans-serif; text-align: center;">'
						. htmlspecialchars($this->_params->get('googlePlusCustomText2', '')) . '</div>';

					break;
				default:
					$size = '32';
					$buttonsHtml .= '<div style="display: inline-block;">';
					// custom text
					if ($this->_params->get('googlePlusCustomText', ''))
					{
						$buttonsHtml .= '<span style="float: left; font: bold 13px/16px arial,sans-serif; margin-right: 4px; margin-top: 7px;">'
							. htmlspecialchars($this->_params->get('googlePlusCustomText', ''))
							. '</span><span style="float: left; font: 13px/16px arial,sans-serif; margin-right: 11px; margin-top: 7px;">'
							. htmlspecialchars($this->_params->get('googlePlusCustomText2', '')) . '</span>';
					}
					$buttonsHtml .= '<div style="float: left;"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" width="32" height="32" style="border: 0;"/></div><div style="clear: both"></div>';

					break;
			}

			$buttonsHtml .= '</div></a>' . $wrapperClose;
		}

		// Pinterest
		if ($this->_params->get('enablePinterestPinIt', 1) && in_array('pinterestpinit', $this->_enabledButtons))
		{

			// we use either the first image in content, or the provided one (from a user created tag)
			if (empty($imageSrc))
			{
				// we're using the first image in the content
				$regExp = '#<img([^>]*)/>#ius';
				$text = empty($content->fulltext) ? (empty($content->introtext) ? '' : $content->introtext) : $content->introtext
					. $content->fulltext;
				$img = preg_match($regExp, $text, $match);
				if (empty($img) || empty($match[1]))
				{
					// could not find an image in the article
					// last chance is maybe webmaster is using Joomla! full text image article feature
					// note: if we are not on the canonical page (ie the full article display), Joomla!
					// uses the image_intro instead. However, I decided to still pin the full image
					// in such case, as the image_intro will most often be a thumbnail
					// Is this correct? can there be side effects?
					$imageSrc = '';
					if ($context == 'com_content.article' && !empty($content->images))
					{
						$registry = new JRegistry;
						$registry->loadString($content->images);
						$fulltextImage = $registry->get('image_fulltext');
						if (!empty($fulltextImage))
						{
							$imageSrc = $fulltextImage;
							$imageDesc = $registry->get('image_fulltext_alt', '');
						}
					}
					else if ($context == 'com_k2.item')
					{
						// handle K2 images feature
						if (!empty($content->imageMedium))
						{
							$imageSrc = JURI::root() . str_replace(JURI::base(true) . '/', '', $content->imageMedium);
							$imageDesc = $content->image_caption;
						}
					}
				}
				else
				{
					// extract image details
					jimport('joomla.utilities.utility');
					$attributes = JUtility::parseAttributes($match[1]);
					$imageSrc = empty($attributes['src']) ? '' : $attributes['src'];
					$imageDesc = empty($attributes['alt']) ? '' : $attributes['alt'];
				}
			}
			if (!empty($imageSrc))
			{
				if (substr($imageSrc, 0, 4) != 'http' && substr($imageSrc, 0, 1) != '/')
				{
					// relative url, prepend root url
					$imageSrc = JURI::base() . $imageSrc;
				}
				$buttonsHtml .= $wrapperOpen;
				$buttonsHtml .= '<a href="http://pinterest.com/pin/create/button/?url=' . urlencode($url) . '&media=' . urlencode($imageSrc)
					. (empty($imageDesc) ? '' : '&description=' . urlencode($imageDesc)) . '" ' . 'class="pin-it-button" count-layout="'
					. $this->_params->get('pinItCountLayout', 'none') . '">' . $this->_params->get('pinItButtonText', 'Pin it') . '</a>';
				$buttonsHtml .= $wrapperClose;
			}
		}

		// FB Like
		if ($this->_params->get('enableFbLike', 1) && in_array('facebooklike', $this->_enabledButtons))
		{
			$layout = $this->_params->get('fbLayout', '') == 'none' ? '' : $this->_params->get('fbLayout', '');
			if ($this->_params->get('fbUseHtml5', false))
			{
				$buttonsHtml .= $wrapperOpen . '<div class="fb-like" data-href="' . $url . '" data-send="'
					. ($this->_params->get('enableFbSend', 1) ? 'true' : 'false') . '" data-action="' . $this->_params->get('fbAction', '')
					. '" data-width="' . $this->_params->get('fbWidth', '') . '" data-layout="' . $layout . '" data-show-faces="'
					. $this->_params->get('fbShowFaces', 'true') . '" data-colorscheme="' . $this->_params->get('fbColorscheme', 'light')
					. '"></div>' . $wrapperClose;
			}
			else
			{
				$buttonsHtml .= $wrapperOpen . '<fb:like href="' . $url . '" send="' . ($this->_params->get('enableFbSend', 1) ? 'true' : 'false')
					. '" action="' . $this->_params->get('fbAction', '') . '" width="' . $this->_params->get('fbWidth', '') . '" layout="' . $layout
					. '" show_faces="' . $this->_params->get('fbShowFaces', 'true') . '" colorscheme="' . $this->_params->get('fbColorscheme', '')
					. '"></fb:like>' . $wrapperClose;
			}
		}
		else if ($this->_params->get('enableFbSend', 1) && in_array('facebooksend', $this->_enabledButtons))
		{
			if ($this->_params->get('fbUseHtml5', false))
			{
				$buttonsHtml .= $wrapperOpen . '<div class="fb-send" data-href="' . $url . '" data-colorscheme="'
					. $this->_params->get('fbColorscheme', '') . '"></div>' . $wrapperClose;
			}
			else
			{
				$buttonsHtml .= $wrapperOpen . '<fb:send href="' . $url . '" colorscheme="' . $this->_params->get('fbColorscheme', '')
					. '"></fb:send>' . $wrapperClose;
			}
		}

		// perform replace
		if (!empty($buttonsHtml))
		{
			$buttonsHtml = '<div class="sh404sef-social-buttons"><ul>' . $buttonsHtml . '</ul></div>';
		}

		return $buttonsHtml;

	}

	private function _shouldDisplaySocialButtons($sefConfig, $context = '', $content = null)
	{

		// if SEO off, don't do anything
		if (!$sefConfig->shMetaManagementActivated)
		{
			return false;
		}

		$shouldDisplay = true;

		// user can disable this attempt to identify possible failure
		// to select the correct url
		if (!$this->_params->get('onlyDisplayOnCanonicalUrl', true))
		{
			return $shouldDisplay;
		}

		// get request details
		$currentComponent = JRequest::getCmd('option');
		$currentView = JRequest::getCmd('view');
		if (empty($context))
		{
			$component = '';
			$view = '';
		}
		else
		{
			$bits = explode('.', $context);
			if (!empty($bits))
			{
				$component = $bits[0];
				$view = empty($bits[1]) ? JRequest::getCmd('view', '') : $bits[1];
			}
		}
		$printing = JRequest::getInt('print');

		// we are set to only display on canonical page for an item
		// this can only be true if context and current request matches
		if (!empty($component) && !empty($view) && !empty($currentComponent) && !empty($currentView)
			&& ($currentComponent != $component || $currentView != $view))
		{
			return false;
		}

		switch ($component)
		{
			case 'com_content':
			// only display if on an article page
				$shouldDisplay = $view == 'article' && empty($printing);
				// check category
				if ($shouldDisplay)
				{
					$cats = $this->_params->get('enabledCategories', array());
					if (!empty($cats) && ($cats[0] != 'show_on_all'))
					{
						// find about article category
						if (!empty($content))
						{
							// we have article details
							$catid = empty($content->catid) ? 0 : (int) $content->catid;
						}
						else
						{
							// no article details, use request
							$catid = JRequest::getInt('catid', 0);
							if (empty($catid))
							{
								$id = JRequest::getInt('id', 0);
								if ($id)
								{
									$article = JTable::getInstance('content');
									$article->load($id);
									$catid = $article->catid;
								}
							}
						}
						if (!empty($catid))
						{
							$shouldDisplay = in_array($catid, $cats);
						}
					}
				}
				break;
			case 'com_k2':
				$shouldDisplay = $view == 'item';
				break;
			default:
				break;
		}

		return $shouldDisplay;
	}

	/**
	 * Insert appropriate script links into document
	 */
	private function _insertSocialLinks(&$page, $sefConfig)
	{

		$headLinks = '';
		$bottomLinks = '';

		// what do we must link to
		$showFb = strpos($page, '<div class="fb-"') !== false || strpos($page, '<fb:') !== false;
		$showTwitter = strpos($page, '<a href="https://twitter.com/share"') !== false;
		$showPlusOne = strpos($page, '<g:plusone callback="_sh404sefSocialTrackGPlusTracking"') !== false;
		$gPlusPage = $this->_params->get('googlePlusPage', '');
		$gPlusPage = JString::trim($gPlusPage, '/');
		$showGPlusPage = strpos($page, 'onclick="_sh404sefSocialTrack.GPageTracking') !== false && !empty($gPlusPage);
		$showPinterest = strpos($page, 'class="pin-it-button"') !== false;

		// insert social tracking javascript
		if ($showFb || $showTwitter | $showPlusOne || $showGPlusPage || $showPinterest)
		{
			// G! use underscore in language tags
			$locale = str_replace('-', '_', JFactory::getLanguage()->getTag());
			$channelUrl = JURI::base() . 'index.php?option=com_sh404sef&view=channelurl&format=raw&langtag=' . $locale;
			$channelUrl = str_replace(array('http://', 'https://'), '//', $channelUrl);
			$headLinks .= "\n<script src='" . JURI::base(true) . '/plugins/sh404sefcore/sh404sefsocial/sh404sefsocial.js'
				. "' type='text/javascript' ></script>";
			$headLinks .= "\n<script type='text/javascript'>
      _sh404sefSocialTrack.options = {enableGoogleTracking:" . ($this->_params->get('enableGoogleSocialEngagement') ? 'true' : 'false')
				. ",
      enableAnalytics:" . ($this->_params->get('enableSocialAnalyticsIntegration') && $sefConfig->analyticsEnabled ? 'true' : 'false')
				. ", trackerName:'',
      FBChannelUrl:'" . $channelUrl . "'};
      window.fbAsyncInit = _sh404sefSocialTrack.setup;
      </script>";
		}

		if ($showFb)
		{
			$page = str_replace('<html ', '<html xmlns:fb="http://ogp.me/ns/fb#" ', $page);
		}

		// twitter share
		if ($showTwitter)
		{
			$bottomLinks .= "\n<script src='//platform.twitter.com/widgets.js' type='text/javascript'></script>";
		}

		// plus one
		if ($showPlusOne)
		{
			$bottomLinks .= "
      <script type='text/javascript'>
      (function() {
      var po = document.createElement('script');
      po.type = 'text/javascript';
      po.async = true;
      po.src = 'https://apis.google.com/js/plusone.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();

    </script>
    ";
		}

		// google plus page badge
		if ($showGPlusPage)
		{
			$headLinks .= "\n<link href='https://plus.google.com/" . $gPlusPage . "/' rel='publisher' />";
		}

		// pinterest
		if ($showPinterest)
		{
			$headLinks .= "
      <script type='text/javascript'>

      (function() {
      window.PinIt = window.PinIt || { loaded:false };
      if (window.PinIt.loaded) return;
      window.PinIt.loaded = true;
      function async_load(){
      var s = document.createElement('script');
      s.type = 'text/javascript';
      s.async = true;
      if (window.location.protocol == 'https:')
      //s.src = 'https://assets.pinterest.com/js/pinit.js';
      s.src = '" . JURI::base()
				. "media/com_sh404sef/pinterest/pinit.js';
      else
      //s.src = 'http://assets.pinterest.com/js/pinit.js';
      s.src = '" . JURI::base()
				. "media/com_sh404sef/pinterest/pinit.js';
      var x = document.getElementsByTagName('script')[0];
      x.parentNode.insertBefore(s, x);
    }
    if (window.attachEvent)
    window.attachEvent('onload', async_load);
    else
    window.addEventListener('load', async_load, false);
    })();
    </script>
    ";
		}

		// actually insert
		if (!empty($headLinks))
		{
			// add our wrapping css
			$headLinks .= "\n<link rel='stylesheet' href='" . JURI::base(true) . '/plugins/sh404sefcore/sh404sefsocial/sh404sefsocial.css'
				. "' type='text/css' />";
			$headLinks .= "<script type='text/javascript'>var _sh404SEF_live_site = '" . JURI::base() . "';</script>";

			// insert everything in page
			$page = shInsertCustomTagInBuffer($page, '</head>', 'before', $headLinks, $firstOnly = 'first');
		}

		if (!empty($bottomLinks))
		{
			// insert everything in page
			$page = shInsertCustomTagInBuffer($page, '</body>', 'before', $bottomLinks, $firstOnly = 'first');
		}

	}

}
