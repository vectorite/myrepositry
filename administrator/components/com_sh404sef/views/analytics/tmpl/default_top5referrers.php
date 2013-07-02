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

<div class="width-100">
<fieldset>
   <legend><?php echo JText::sprintf('COM_SH404SEF_ANALYTICS_TOP5_REFERRERS', $this->options['max-top-referrers']); ?></legend>
        
  <table class="adminlist">
    <thead>
      <tr>
        <th class="title" width="3%">
          <?php echo '#'; ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_REF_SOURCE') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_SOURCE_SITE_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_REF_SOURCE' ); ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_REF_PATH') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_SOURCE_PATH_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_REF_PATH' ); ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_PAGE_VIEWS_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS' ); ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS_PERCENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_REFERRER_PER_CENT_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS_PERCENT' ); ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_SITE') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_AVG_TIME_ON_SITE_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_SITE' ); ?>
        </th>
      </tr>
    </thead>
        
        
   <tbody>
        <?php
          $k = 0;
          $i = 1;
          foreach($this->analytics->analyticsData->top5referrers as $entry) :
        ?>    
            
        <tr class="<?php echo "row$k"; ?>">
        
          <td align="center" width="3%">
            <?php echo $i; ?>
          </td>
          
          <td width="22%">
            <?php echo $this->escape( $entry->dimension['source']); ?>
          </td>
          
          <td width="40%">
            <?php echo $this->escape( $entry->dimension['referralPath']); ?>
          </td>
          
          <td align="center" width="15%">
            <?php echo $this->escape( $entry->views); ?>
          </td>
          
          <td align="center" width="10%">
            <?php 
              echo $this->escape( sprintf( '%0.1f', $entry->viewsPerCent*100));
            ?>
          </td>
          
          <td align="center" width="10%">
            <?php 
              echo $this->escape( sprintf( '%0.1f', $entry->avgTimeOnSite));
            ?>
          </td>

        </tr>
        <?php
        $k = 1 - $k;
        $i++;
      endforeach;
        
      ?>     
        
    </tbody>
  </table>    
        
</fieldset>
</div>
	