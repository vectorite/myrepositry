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

  <?php if (!empty($this->analytics->analyticsData->topSocialPinterest)) : ?>
  
   <legend><?php echo JText::_('COM_SH404SEF_ANALYTICS_SOCIAL_PINTEREST_ENGAGEMENT'); ?></legend>
        
 	<table class="adminlist" >
    <thead>
      <tr>
        <th class="title" >
          <?php echo '#'; ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_URL') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_URL_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_URL' ); ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PINTEREST_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_PINTEREST_ENGAGEMENT_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_PINTEREST_ENGAGEMENT' ); ?>
        </th>
        
        <?php?>
        <th class="title" >
        <?php echo '%'; ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_PAGE') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TT_AVG_TIME_ON_PAGE_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOP5_AVG_TIME_ON_PAGE' ); ?>
        </th>
      </tr>
    </thead>
 	      
 	      
 	 <tbody>
        <?php
          $k = 0;
          $i = 1;
          foreach($this->analytics->analyticsData->topSocialPinterest as $entry) :
        ?>    
            
        <tr class="<?php echo "row$k"; ?>">
        
          <td align="center" width="3%">
            <?php echo $i; ?>
          </td>
          
          <td width="62%">
            <?php 
            $path = str_replace( JURI::root(),'',  $entry->dimension['eventLabel']);
              echo $this->escape( $path);
            ?>
          </td>
          
          <td align="center" width="15%">
            <?php echo $this->escape( $entry->totalEvents); ?>
          </td>
          
          <td align="center" width="10%">
            <?php 
              echo $this->escape( sprintf( '%0.1f', $entry->eventsPerCent*100));
            ?>
          </td>
          
          <td align="center" width="10%">
            <?php 
              echo $this->escape( sprintf( '%0.1f', $entry->avgTimeOnPage));
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
 	
 	<?php else : ?>
 	  <legend><?php echo JText::_('COM_SH404SEF_ANALYTICS_SOCIAL_NO_PINTEREST_ENGAGEMENT'); ?></legend>
 	<?php endif; ?>      
</fieldset>
</div>	

	