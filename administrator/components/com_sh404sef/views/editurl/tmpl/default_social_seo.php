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

<table class="adminlist">
  <thead>
  </thead>
  <?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_DATA_ENABLED_BY_URL'),
  JText::_('COM_SH404SEF_TT_OG_DATA_ENABLED_BY_URL'),
  $this->ogData['og_enable'] );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_REQUIRED_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_TYPE_SELECT'),
  JText::_('COM_SH404SEF_TT_OG_TYPE_SELECT'),
  $this->ogData['og_type'] );
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_IMAGE_PATH'),
  JText::_('COM_SH404SEF_TT_OG_IMAGE_PATH'),
                'og_image',
  $this->ogData['og_image'], 50, 255 );
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_OPTIONAL_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_DESCRIPTION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_DESCRIPTION'),
  $this->ogData['og_enable_description'] );
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_SITE_NAME'),
  $this->ogData['og_enable_site_name'] );
  
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_SITE_NAME'),
                'og_site_name',
  $this->ogData['og_site_name'], 50, 255 );
  
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_ENABLE_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_OG_ENABLE_FB_ADMIN_IDS'),
  $this->ogData['og_enable_fb_admin_ids']);
  
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_FB_ADMIN_IDS'),
                'fb_admin_ids',
  $this->ogData['fb_admin_ids'], 50, 255 );
  
