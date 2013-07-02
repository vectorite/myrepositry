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

  <?php  echo JText::_('COM_SH404SEF_CONF_SOCIAL_SEO_HELP')?>

  <!-- start of configuration html -->
  
  <?php 
    echo JHtml::_('tabs.start', 'sh404SEFConf');
    echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_OG_CONFIG'), 'ogconfig');

  // params for open graph data configuration  -->
  ?>
  <table class="adminlist">
  <thead>
  </thead>
  <?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_DATA_ENABLED'),
  JText::_('COM_SH404SEF_TT_OG_DATA_ENABLED'),
  $this->lists['og_enable'] );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_REQUIRED_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_TYPE_SELECT'),
  JText::_('COM_SH404SEF_TT_OG_TYPE_SELECT'),
  $this->lists['og_type_select'] );
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_IMAGE_PATH'),
  JText::_('COM_SH404SEF_TT_OG_IMAGE_PATH'),
                'ogImage',
  $this->lists['og_image'], 50, 255 );
  
  ?></table>
    
  <?php
    echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_OG_OPTIONAL_TITLE'), 'ogoptional');
  ?>
  <table class="adminlist">
  <?php
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_DESCRIPTION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_DESCRIPTION'),
  $this->lists['og_insert_description'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_SITE_NAME'),
  $this->lists['og_insert_site_name'] );
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_SITE_NAME'),
                'ogSiteName',
  $this->lists['og_site_name'], 50, 255 );
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_FB_ADMIN_IDS'),
                'fbAdminIds',
  $this->lists['fb_admin_ids'], 50, 255 );
  
  ?></table>
    
  <?php
    
    echo JHtml::_('tabs.end');  
  ?>
  
  <!-- end of configuration html -->

    <input type="hidden" name="c" value="config" />
    <input type="hidden" name="view" value="config" />
    <input type="hidden" name="layout" value="social_seo" />
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
