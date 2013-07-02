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

?>

<div class="sh404sef-popup" id="sh404sef-popup">

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

<form action="index.php" method="post" name="adminForm" id="adminForm">

  <div id="editcell">

  <!-- start of configuration html -->

<table class="adminlist">

  <?php  echo JText::_('COM_SH404SEF_CONF_ANALYTICS_HELP')?>

  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="4"><?php echo JText::_('COM_SH404SEF_TITLE_BASIC'); ?></th>
    </tr>
  </thead>
  <?php
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_ENABLED'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_ENABLED'),
  $this->lists['analyticsEnabled'] );

  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_ID'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_ID'),
  'analyticsId', $this->sefConfig->analyticsId, 15, 30 );

  ?>
  <tr <?php $x++; echo ( ( $x % 2) ? '' : ' class="row1"' ); ?>>
    <td valign="top"><?php echo JText::_('COM_SH404SEF_ANALYTICS_EXCLUDE_IP'); ?></td>
    <td><textarea name="analyticsExcludeIP" cols="30" rows="5"><?php echo $this->lists['analyticsExcludeIP'];?></textarea></td>
    <td><?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_ANALYTICS_EXCLUDE_IP') ); ?></td>
  </tr>
  <?php
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
				JText::_('COM_SH404SEF_ANALYTICS_USER_GROUPS'),
				JText::_('COM_SH404SEF_TT_ANALYTICS_USER_GROUPS'),
				$this->lists['analyticsUserGroups'] );

  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_ENABLE_TIME_COLLECTION'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_ENABLE_TIME_COLLECTION'),
  $this->lists['analyticsEnableTimeCollection'] );

  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_ENABLE_LOGGED_IN_USER'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_ENABLE_LOGGED_IN_USER'),
  $this->lists['analyticsEnableUserCollection'] );

  ?>
  <!-- Analytics reports  -->
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_ANALYTICS_REPORTS_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_REPORTS_ENABLED'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_REPORTS_ENABLED'),
  $this->lists['analyticsReportsEnabled'] );

  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_USER'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_USER'),
  'analyticsUser' , $this->sefConfig->analyticsUser, 40, 80 );

  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_PASSWORD'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_PASSWORD'),
  'analyticsPassword' , empty($this->sefConfig->analyticsPassword) ? '' : '********', 40, 80, $w1 = '200', $w2 = '150', $type= 'password');

  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_ENABLE_AUTO_CHECK'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_ENABLE_AUTO_CHECK'),
  $this->lists['autoCheckNewAnalytics'] );

  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_DASHBOARD_DATE_RANGE'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_DASHBOARD_DATE_RANGE'),
  $this->lists['analyticsDashboardDateRange'] );

  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ANALYTICS_DASHBOARD_DATA_TYPE'),
  JText::_('COM_SH404SEF_TT_ANALYTICS_DASHBOARD_DATA_TYPE'),
  $this->lists['analyticsDashboardDataType'] );


  ?>
</table>

  <!-- end of configuration html -->

    <input type="hidden" name="c" value="config" />
    <input type="hidden" name="view" value="config" />
    <input type="hidden" name="layout" value="analytics" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="shajax" value="1" />
    <input type="hidden" name="tmpl" value="component" />

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

