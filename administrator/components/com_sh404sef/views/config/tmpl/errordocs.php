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

$editor =JFactory::getEditor();

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
  
  <?php 
    echo JHtml::_('tabs.start', 'sh404SEFConf');
    echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_TITLE_BASIC'), 'main');
  ?>  
    
<table class="adminlist">
  
  <!-- 404 error page -->
  <?php
  $x = 0;

  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_PAGE_NOT_FOUND_ITEMID'),
  JText::_('COM_SH404SEF_TT_PAGE_NOT_FOUND_ITEMID'),
        'shPageNotFoundItemid',
  $this->sefConfig->shPageNotFoundItemid, 30, 30);
  
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_PAGE_NOT_FOUND_TEMPLATE'),
  JText::_('COM_SH404SEF_TT_PAGE_NOT_FOUND_TEMPLATE'),
        'error404SubTemplate',
  $this->sefConfig->error404SubTemplate, 30, 50);
  
  
  ?></table><?php
  // end of params for meta tags management  -->
  
    echo JHtml::_('tabs.panel', JText::_('COM_SH404SEF_DEF_404_PAGE'), 'error404page');

  // params for Page title configuration  -->
  ?><table class="adminlist">

  <tr>
    <td ><?php
    // parameters : areaname, content, width, height, cols, rows
    echo $editor->display( 'introtext',  $this->txt404 , '650', '450', '50', '50' ) ;
    ?></td>
    <td>
        <?php echo JText::_('COM_SH404SEF_404_PAGE_DESCRIPTION') . '<br /><br />' . JText::_('COM_SH404SEF_404_PAGE_EDIT_HELP'); ?>
    </td>
  </tr>
  
  </table><?php
  // end of params for content title configuration  -->
  
     echo JHtml::_('tabs.end'); 
  ?>
  
  <!-- end of configuration html -->

    <input type="hidden" name="c" value="config" />
    <input type="hidden" name="view" value="config" />
    <input type="hidden" name="layout" value="errordocs" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="shajax" value="1" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="page404" value="<?php  echo $this->sefConfig->page404; ?>" />
    
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
