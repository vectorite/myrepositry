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

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

// we'll use panes so import Joomla library and instantiate one
?>

<div
	class="sh404sef-popup" id="sh404sef-popup">

	<!-- markup common to all config layouts -->

	<?php include JPATH_ADMINISTRATOR . '/components/com_sh404sef/views/config/tmpl/common_header.php'; ?>

	<!-- start general configuration markup -->

	<div id="element-box">
		<div class="t">
			<div class="t">
				<div class="t"></div>
			</div>
		</div>
		<div class="m">

			<form action="index.php" method="post" name="adminForm"
				id="adminForm">

				<div id="editcell">

					<!-- start of configuration html -->

					<?php
					echo JHtml::_('tabs.start', 'sh404SEFConf');
					echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_CONF_TAB_MAIN'), 'basics');
					?>
					<table class="adminlist">
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="4"><?php echo JText::_('COM_SH404SEF_TITLE_BASIC'); ?>
								</th>
							</tr>
						</thead>
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_SEF_ENABLED'),
						    JText::_('COM_SH404SEF_TT_SEF_ENABLED'),
						    $this->lists['enabled'] );

						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_REPLACE_CHAR'),
						    JText::_('COM_SH404SEF_TT_REPLACE_CHAR'),
						    'replacement',
						    $this->sefConfig->replacement, 1, 1 );

						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_PAGEREP_CHAR'),
						    JText::_('COM_SH404SEF_TT_PAGEREP_CHAR'),
						    'pagerep',
						    $this->sefConfig->pagerep, 1, 1 );

						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_STRIP_CHAR'),
						    JText::_('COM_SH404SEF_TT_STRIP_CHAR'),
						    'stripthese',
            $this->sefConfig->stripthese, 60, 255 ); ?>
						<!-- shumisha 2007-04-01 new param for characters replacement table  -->
						<tr <?php $x++; echo ( ( $x % 2) ? '' : ' class="row1"' ); ?>>
							<td valign="top"><?php echo JText::_('COM_SH404SEF_REPLACEMENTS'); ?></td>
							<td><textarea name="shReplacements" cols="60" rows="5"><?php echo $this->sefConfig->shReplacements;?></textarea></td>
							<td><?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_REPLACEMENTS') ); ?>
							</td>
						</tr>
						<!-- shumisha 2007-04-01 end of new param for characters replacement table  -->
						<?php
						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_FRIENDTRIM_CHAR'),
						    JText::_('COM_SH404SEF_TT_FRIENDTRIM_CHAR'),
						    'friendlytrim',
						    $this->sefConfig->friendlytrim, 60, 255 );

						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_SUFFIX'),
						    JText::_('COM_SH404SEF_TT_SUFFIX'),
						    'suffix',
						    $this->sefConfig->suffix, 10, 10 );

						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_ALWAYS_APPEND_ITEMS_PER_PAGE'),
						    JText::_('COM_SH404SEF_TT_ALWAYS_APPEND_ITEMS_PER_PAGE'),
						    $this->lists['alwaysAppendItemsPerPage'] );


						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_ADDFILE'),
						    JText::_('COM_SH404SEF_TT_ADDFILE'),
						    'addFile',
						    $this->sefConfig->addFile, 60, 60 );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_LOWER'),
						    JText::_('COM_SH404SEF_TT_LOWER'),
						    $this->lists['lowercase'] );

						?>

					</table>
					<?php

					echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_CONF_TAB_PAGEID'), 'pageid');
					echo JText::_('COM_SH404SEF_ENABLE_PAGEID_HELP'). '<br /><br />';
					?>
					<table class="adminlist">
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="4"><?php echo JText::_('COM_SH404SEF_TITLE_BASIC'); ?>
								</th>
							</tr>
						</thead>
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_ENABLE_PAGEID'),
						    JText::_('COM_SH404SEF_TT_ENABLE_PAGEID'),
						    $this->lists['enablePageId'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_STOP_CREATING_SHURLS'),
						    JText::_('COM_SH404SEF_TT_STOP_CREATING_SHURLS'),
						    $this->lists['stopCreatingShurls'] );
						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_SHURL_BLACKLIST'),
						    JText::_('COM_SH404SEF_TT_SHURL_BLACKLIST'),
						    'shurlBlackList',
						    $this->sefConfig->shurlBlackList, 60, 255 );
						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_SHURL_NON_SEF_BLACKLIST'),
						    JText::_('COM_SH404SEF_TT_SHURL_NON_SEF_BLACKLIST'),
						    'shurlNonSefBlackList',
						    $this->sefConfig->shurlNonSefBlackList, 60, 255 );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_SHORTLINK_TAG'),
						    JText::_('COM_SH404SEF_TT_INSERT_SHORTLINK_TAG'),
						    $this->lists['insertShortlinkTag'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_REV_CAN_TAG'),
						    JText::_('COM_SH404SEF_TT_INSERT_REV_CAN_TAG'),
						    $this->lists['insertRevCanTag'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_ALT_SHORT_TAG'),
						    JText::_('COM_SH404SEF_TT_INSERT_ALT_SHORT_TAG'),
						    $this->lists['insertAltShorterTag'] );

						?>
					</table>
					<?php

					echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_CONF_TAB_LANGUAGES'), 'Languages');

					?>

					<table class="adminlist">
						<!-- shumisha 27/09/2007 new params for languages management  -->
						<thead>
						</thead>
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_ENABLE_MULTILINGUAL_SUPPORT'),
						    JText::_('COM_SH404SEF_TT_ENABLE_MULTILINGUAL_SUPPORT'),
						    $this->lists['enableMultiLingualSupport'] );
						?>
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_TRANSLATION_TITLE'); ?>
								</th>
							</tr>
						</thead>
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_TRANSLATE_URL'),
						    JText::_('COM_SH404SEF_TT_TRANSLATE_URL_GEN'),
						    $this->lists['shTranslateURL'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_LANGUAGE_CODE'),
						    JText::_('COM_SH404SEF_TT_INSERT_LANGUAGE_CODE_GEN'),
						    $this->lists['shInsertLanguageCode'] );

            foreach( $this->lists['activeLanguages'] as $currentLang ) { ?>
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="3"><?php echo ucfirst($currentLang); ?>
								</th>
							</tr>
						</thead>
						<?php
						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_PAGETEXT'),
						    JText::_('COM_SH404SEF_TT_PAGETEXT'),
						    'languages_'.$currentLang.'_pageText',
						    $this->sefConfig->pageTexts[$currentLang], 10, 20 );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_TRANSLATE_URL'),
						    JText::_('COM_SH404SEF_TT_TRANSLATE_URL_PER_LANG'),
						    $this->lists['languages_' . $currentLang . '_translateURL'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_LANGUAGE_CODE'),
						    JText::_('COM_SH404SEF_TT_INSERT_LANGUAGE_CODE_PER_LANG'),
						    $this->lists['languages_'.$currentLang.'_insertCode'] );
						/*
						 $x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_LIVE_SITES'),
						    JText::_('COM_SH404SEF_TT_LIVE_SITES'),
						    'languages_'.$currentLang.'_liveSites',
						    $this->sefConfig->liveSites[$currentLang], 30, 100 );
						*/
  }
  ?>
						<!-- shumisha 27/09/2007 new params for languages management  -->
					</table>

					<?php

					echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_CONF_TAB_ADVANCED'), 'Advanced');

					?>

					<table class="adminlist">
						<!-- shumisha 2007-04-01 new params for cache  -->
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_CACHE_TITLE');?>
								</th>
							</tr>
						</thead>
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_USE_URL_CACHE'),
						    JText::_('COM_SH404SEF_TT_USE_URL_CACHE'),
						    $this->lists['shUseURLCache'] );
						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_MAX_URL_IN_CACHE'),
						    JText::_('COM_SH404SEF_TT_MAX_URL_IN_CACHE'),
						    'shMaxURLInCache',
                $this->sefConfig->shMaxURLInCache, 10, 10 );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_URL_CACHE_HANDLER'),
						    JText::_('COM_SH404SEF_TT_URL_CACHE_HANDLER'),
						    $this->lists['UrlCacheHandler']
						    ,null, null, array('', '', $this->lists['linkToCachePluginParams']) );
						
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_DISPLAY_URL_CACHE_STATS'),
						    JText::_('COM_SH404SEF_TT_DISPLAY_URL_CACHE_STATS'),
						    $this->lists['displayUrlCacheStats']);
						
						?>
						<!-- shumisha 2007-04-01 end of new params for cache  -->
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_TITLE_ADV');?>
								</th>
							</tr>
						</thead>
						<!-- shumisha 2007-06-23 new param to select rewrite mode  -->
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_SELECT_REWRITE_MODE'),
						    JText::_('COM_SH404SEF_TT_SELECT_REWRITE_MODE'),
						    $this->lists['shRewriteMode'] );
						/* <!-- shumisha 2007-06-23 new param to select rewrite mode  -->
						 <!-- shumisha 2007-04-13 new params for automatic 301 redirect of non-sef URL  --> */
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_REDIRECT_NON_SEF_TO_SEF'),
						    JText::_('COM_SH404SEF_TT_REDIRECT_NON_SEF_TO_SEF'),
						    $this->lists['shRedirectNonSefToSef'] );

						/* <!-- shumisha 2007-05-4 new params for automatic 301 redirect of joomla-sef URL  --> */
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_REDIRECT_WWW'),
						    JText::_('COM_SH404SEF_TT_REDIRECT_WWW'),
						    $this->lists['shAutoRedirectWww'] );

						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_REDIRECT_CORRECT_CASE_URL'),
						    JText::_('COM_SH404SEF_TT_REDIRECT_CORRECT_CASE_URL'),
						    $this->lists['redirectToCorrectCaseUrl'] );

						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_LOG_404_ERRORS'),
						    JText::_('COM_SH404SEF_TT_LOG_404_ERRORS'),
						    $this->lists['shLog404Errors'] );

						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_FORCE_NON_SEF_HTTPS'),
						    JText::_('COM_SH404SEF_TT_FORCE_NON_SEF_HTTPS'),
						    $this->lists['shForceNonSefIfHttps'] );

						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_FORCED_HOMEPAGE'),
						    JText::_('COM_SH404SEF_TT_FORCED_HOMEPAGE'),
						    'shForcedHomePage',
						    $this->sefConfig->shForcedHomePage, 30, 60 );

						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_SLOW_SERVER'),
						    JText::_('COM_SH404SEF_TT_SLOW_SERVER'),
						    $this->lists['slowServer'] );

						$x++;
						if(!empty($this->lists['shlibPluginId'])) {
						  $hRef = empty( $this->lists['shlibPluginId']) ? '' : 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . (int) $this->lists['shlibPluginId'];
						  $pluginLink =  '<a href="' . $hRef . '" target="_blank">' . JText::_('COM_SH404SEF_CONFIGURE_SHLIB_PLUGIN') . '</a>';
						} else {
						  $pluginLink =  '';
						}
						$output  = '<tr' . ( ( $x % 2 ) ? '' : ' class="row1"' ) . '>' . "\n"
						. '<td width="200">' . JText::_('COM_SH404SEF_DEBUG_TO_LOG_FILE') . '</td>' . "\n"
						. '<td width="150">' . $pluginLink . '</td>' . "\n"
						. '<td>' . JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_DEBUG_TO_LOG_FILE_PLUGIN'), JText::_('COM_SH404SEF_DEBUG_TO_LOG_FILE')) . '</td>' . "\n"
						. '</tr>' . "\n"
						;
						
						echo $output;
						?>
						<!-- shumisha 2007-04-01 new params for Itemid handling and URL construction  -->
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_ITEMID_TITLE'); ?>
								</th>
							</tr>
						</thead>
						<?php
						$x = 1;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_GUESS_HOMEPAGE_ITEMID'),
						    JText::_('COM_SH404SEF_TT_GUESS_HOMEPAGE_ITEMID'),
						    $this->lists['guessItemidOnHomepage'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_GLOBAL_ITEMID_IF_NONE'),
						    JText::_('COM_SH404SEF_TT_INSERT_GLOBAL_ITEMID_IF_NONE'),
						    $this->lists['shInsertGlobalItemidIfNone'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_INSERT_TITLE_IF_NO_ITEMID'),
						    JText::_('COM_SH404SEF_TT_INSERT_TITLE_IF_NO_ITEMID'),
						    $this->lists['shInsertTitleIfNoItemid'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_ALWAYS_INSERT_MENU_TITLE'),
						    JText::_('COM_SH404SEF_TT_ALWAYS_INSERT_MENU_TITLE'),
						    $this->lists['shAlwaysInsertMenuTitle'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_ALWAYS_INSERT_ITEMID'),
						    JText::_('COM_SH404SEF_TT_ALWAYS_INSERT_ITEMID'),
						    $this->lists['shAlwaysInsertItemid'] );
						$x++;
						echo Sh404sefHelperView::shTextParamHTML( $x,
						    JText::_('COM_SH404SEF_DEFAULT_MENU_ITEM_NAME'),
						    JText::_('COM_SH404SEF_TT_DEFAULT_MENU_ITEM_NAME'),
						    'shDefaultMenuItemName',
						    $this->sefConfig->shDefaultMenuItemName, 30, 30 );

						?>
						<!-- shumisha 2007-04-01 end of new params for Itemid handling and URL construction  -->
						<!-- shumisha 19/08/2007 16:29:22 new params for upgrade management  -->
						<thead>
							<tr>
								<th class="title" style="text-align: left;" colspan="4"><?php echo JText::_('COM_SH404SEF_UPGRADE_TITLE'); ?>
								</th>
							</tr>
						</thead>
						<?php
						$x = 1;
						//autoCheckNewVersion
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_UPGRADE_ENABLE_CHECK'),
						    JText::_('COM_SH404SEF_TT_UPGRADE_ENABLE_CHECK'),
						    $this->lists['autoCheckNewVersion'] );
						$x++;

						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_UPGRADE_KEEP_CONFIG'),
						    JText::_('COM_SH404SEF_TT_UPGRADE_KEEP_CONFIG'),
						    $this->lists['shKeepConfigOnUpgrade'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_UPGRADE_KEEP_URL'),
						    JText::_('COM_SH404SEF_TT_UPGRADE_KEEP_URL'),
						    $this->lists['shKeepStandardURLOnUpgrade'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_UPGRADE_KEEP_CUSTOM'),
						    JText::_('COM_SH404SEF_TT_UPGRADE_KEEP_CUSTOM'),
						    $this->lists['shKeepCustomURLOnUpgrade'] );
						$x++;
						echo Sh404sefHelperView::shYesNoParamHTML( $x,
						    JText::_('COM_SH404SEF_UPGRADE_KEEP_META'),
						    JText::_('COM_SH404SEF_TT_UPGRADE_KEEP_META'),
						    $this->lists['shKeepMetaDataOnUpgrade'] );
						?>
						<!-- shumisha 19/08/2007 16:29:22 new params for upgrade management  -->
					</table>
					<?php

					echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_CONF_TAB_BY_COMPONENT'), 'ByComponent');

					?>
					<table class="adminlist">
						<tr align="left">
							<td width="140" align="left">&nbsp;</td>
							<td style="width: auto; text-align: left">&nbsp;<?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_ADV_MANAGE_URL'), JText::_('COM_SH404SEF_ADV_MANAGE_URL') ); ?>
							</td>

							<td style="width: auto; text-align: left">&nbsp; <?php echo JHTML::_('tooltip',JText::_('COM_SH404SEF_TT_ADV_TRANSLATE_URL'), JText::_('COM_SH404SEF_ADV_TRANSLATE_URL') ); ?>
							</td>
							<td style="width: auto; text-align: left">&nbsp; <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_ADV_INSERT_ISO'), JText::_('COM_SH404SEF_ADV_INSERT_ISO') ); ?>
							</td>
							<td style="width: auto; text-align: left">&nbsp; <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_ADV_OVERRIDE_SEF'), JText::_('COM_SH404SEF_OVERRIDE_SEF_EXT') ); ?>
							</td>
							<td style="width: auto; text-align: left">&nbsp; <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_COMP_ENABLE_PAGEID'), JText::_('COM_SH404SEF_COMP_ENABLE_PAGEID') ); ?>
							</td>
							<td style="width: auto; text-align: left">&nbsp; <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_ADV_COMP_DEFAULT_STRING'), JText::_('COM_SH404SEF_ADV_COMP_DEFAULT_STRING') ); ?>
							</td>

						</tr>
						<?php
						foreach( $this->lists['adv_config'] as $key => $compList ) {
    $x++; ?>
						<tr <?php echo ( ( $x % 2 ) ? '' : ' class="row1"' ); ?>>
							<td width="140"><?php echo $key; ?></td>
							<td style="width: auto"><?php echo $compList['manageURL']; ?></td>
							<td style="width: auto"><?php echo $compList['translateURL']; ?>
							</td>
							<td style="width: auto"><?php echo $compList['insertIsoCode']; ?>
							</td>
							<td style="width: auto"><?php echo $compList['shDoNotOverrideOwnSef']; ?>
							</td>
							<td style="width: auto"><?php echo $compList['compEnablePageId']; ?>
							</td>
							<?php
							echo $compList['defaultComponentString'];
							?>
						</tr>
						<?php
  } ?>
					</table>

					<?php

					echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_DEFAULT_PARAMS_TITLE'), 'default_params');

					?>

					<table class="adminform">
						<tr>
							<td colspan="3" align="left">
								<div
									style="border: 1px solid #FF0000; margin: 5px; padding: 5px; background-color: #FFEFEF">
									<?php echo JText::_('COM_SH404SEF_DEFAULT_PARAMS_WARNING'); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td width="450"><textarea name="defaultParamList" cols="100"
									rows="30"><?php echo $this->lists['defaultParamList']; ?></textarea>
							</td>
						</tr>
					</table>
					<?php

					echo JHtml::_('tabs.end');
					?>


					<!-- end of configuration html -->

					<input type="hidden" name="c" value="config" /> <input
						type="hidden" name="view" value="config" /> <input type="hidden"
						name="layout" value="default" /> <input type="hidden"
						name="format" value="raw" /> <input type="hidden" name="option"
						value="com_sh404sef" /> <input type="hidden" name="task" value="" />
					<input type="hidden" name="shajax" value="1" /> <input
						type="hidden" name="tmpl" value="component" /> <input
						type="hidden" name="tmpl" value="component" /> <input
						type="hidden" name="shRedirectJoomlaSefToSef" value="0" />

					<?php echo JHTML::_( 'form.token' ); ?>
				</div>
			</form>


			<div class="clr"></div>
		</div>
		<div class="b">
			<div class="b">
				<div class="b"></div>
			</div>
		</div>
	</div>


</div>

