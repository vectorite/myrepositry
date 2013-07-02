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
<fieldset class="adminform">
  <legend><?php echo JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_DATA'); ?></legend>
        
    <table class="admintable" cellspacing="1" width="100%">
      <tbody>
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_DATA_VISITS') . '::' . JText::_('COM_SH404SEF_ANALYTICS_DATA_VISITS_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_DATA_VISITS' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape( $this->analytics->analyticsData->global->visits); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_DATA_VISITORS') . '::' . JText::_('COM_SH404SEF_ANALYTICS_DATA_VISITORS_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo  JText::_( 'COM_SH404SEF_ANALYTICS_DATA_VISITORS' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->visitors); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS') . '::' . JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_PAGEVIEWS_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_PAGEVIEWS' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->pageviews); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_AVG_PAGES_VISIT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_AVG_PAGES_VISIT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_GLOBAL_AVG_PAGES_VISIT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->global->pagesPerVisit)); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_BOUNCE_RATE') . '::' . JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_BOUNCE_RATE_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_GLOBAL_BOUNCE_RATE' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->global->bounceRate * 100)) . ' %'; ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_AVG_TIME_ON_SITE') . '::' . JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_AVG_TIME_ON_SITE_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_GLOBAL_AVG_TIME_ON_SITE' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->global->avgTimeOnSite)) . ' s.'; ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_NEW_VISITS') . '::' . JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_NEW_VISITS_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_GLOBAL_NEW_VISITS' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->global->newVisitsPerCent*100)) . ' %'; ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_SOCIAL_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_SOCIAL_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_SOCIAL_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->totalSocialEvents); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_VISITS_WITH_SOCIAL_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_GLOBAL_VISITS_WITH_SOCIAL_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_GLOBAL_VISITS_WITH_SOCIAL_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape(sprintf( '%0.1f', $this->analytics->analyticsData->global->visitsWithSocialEngagement * 100)) . ' %'; ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_FACEBOOK_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_FACEBOOK_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_FACEBOOK_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->sh404SEF_social_tracker_facebook); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_TWEETER_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_TWEETER_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_TWEETER_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->sh404SEF_social_tracker_tweeter); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PINTEREST_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PINTEREST_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_PINTEREST_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->sh404SEF_social_tracker_pinterest); ?> 
          </td>
        </tr>
            
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PLUSONE_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PLUSONE_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_PLUSONE_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->sh404SEF_social_tracker_gplus); ?> 
          </td>
        </tr>
        
        <?php  $title = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PLUSPAGE_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PLUSPAGE_ENGAGEMENT_DESC'); ?>
        <tr class="hasAnalyticsTip" title="<?php echo $title;?>">  
          <td width="50%" style="text-align: right;" >
          <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_PLUSPAGE_ENGAGEMENT' ); ?>&nbsp;
          </td>
          <td width="50%" class="key shlargerkey" style="text-align: left;">
            <?php echo $this->escape($this->analytics->analyticsData->global->sh404SEF_social_tracker_gplus_page); ?> 
          </td>
        </tr>
        
      </tbody>
    </table>      

</fieldset>        
</div>