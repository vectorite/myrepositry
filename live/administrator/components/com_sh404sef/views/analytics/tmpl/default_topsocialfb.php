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

  <?php if (!empty($this->analytics->analyticsData->topSocialFB)) : ?>

   <legend><?php echo JText::_('COM_SH404SEF_ANALYTICS_SOCIAL_FACEBOOK_ENGAGEMENT'); ?></legend>
        
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
        
        <th class="title hasAnalyticsTip">
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_SOCIAL_TYPE' ); ?>
        </th>
        
        <?php  $t = JText::_('COM_SH404SEF_ANALYTICS_TOTAL_FACEBOOK_ENGAGEMENT') . '::' . JText::_('COM_SH404SEF_ANALYTICS_TOTAL_FACEBOOK_ENGAGEMENT_DESC'); ?>
        <th class="title hasAnalyticsTip" title="<?php echo $t;?>" >
        <?php echo JText::_( 'COM_SH404SEF_ANALYTICS_TOTAL_FACEBOOK_ENGAGEMENT' ); ?>
        </th>
        
        <?php?>
        <th class="title" >
        <?php echo '%'; ?>
        </th>
        
      </tr>
    </thead>
 	      
 	      
 	 <tbody>
        <?php
          $k = 0;
          $i = 1;
          foreach($this->analytics->analyticsData->topSocialFB as $entry) :
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
          
          <td align="center" width="10%">
            <?php 
            switch ($entry->dimension['eventAction']) {
              case 'like':
                $action = 'COM_SH404SEF_ANALYTICS_SOCIAL_LIKE';
                break;
              case 'unlike':
                $action = 'COM_SH404SEF_ANALYTICS_SOCIAL_UNLIKE';
                break;
              case 'send':
                $action = 'COM_SH404SEF_ANALYTICS_SOCIAL_SEND';
                break;
              default:
                $action = '-';
                break;
            }
              echo $this->escape( JText::_($action));
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
          
        </tr>
        <?php
        $k = 1 - $k;
        $i++;
      endforeach;
 	      
 	    ?>     
 	      
 	  </tbody>
  </table>    
 	
 	<?php else : ?>
 	  <legend><?php echo JText::_('COM_SH404SEF_ANALYTICS_SOCIAL_NO_FACEBOOK_ENGAGEMENT'); ?></legend>
 	<?php endif; ?>
 	      
</fieldset>
</div>	

	