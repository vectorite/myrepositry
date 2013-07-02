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
  <tbody>
    <tr>
      <td width="20%" class="key">
        <label for="oldurl">
          <?php echo JText::_('COM_SH404SEF_OLDURL'); ?>&nbsp;
        </label>
      </td>
      
      <td width="70%">
      <?php 
        $oldUrl = $this->url->get('oldurl');
        if ( $this->noUrlEditing || ($this->canEditNewUrl && !empty($oldUrl)) ) {
          echo $this->escape( $this->url->get('oldurl') ); 
        } else { ?> 
          <input class="text_area" maxlength="<?php echo sh404SEF_MAX_SEF_URL_LENGTH; ?>" type="text" name="oldurl" id="oldurl" size="90" value="<?php echo $this->escape($this->url->get('oldurl')); ?>" />
      <?php } ?>
      </td>
      <td width="10%">
      <?php 
        if ( $this->noUrlEditing || ($this->canEditNewUrl && !empty($oldUrl))) {
          echo '&nbsp;'; 
        } else {
          echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_OLDURL'));
        } ?>
      </td>
      
    </tr>
    
    <tr>
      <td width="20%" class="key">
        <label for="newurl">
          <?php echo JText::_('COM_SH404SEF_NEWURL'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <?php
        if ( !$this->canEditNewUrl || $this->noUrlEditing) {
          echo $this->escape( $this->url->get('newurl') ); 
        } else { ?> 
          <input class="text_area" type="text" size="90" id="newurl" name="newurl" value="<?php echo $this->escape( $this->url->get('newurl') ); ?>" /> 
        <?php } ?>
      </td>
      <td width="10%">
        <?php if (!$this->canEditNewUrl || $this->noUrlEditing) {
          echo '&nbsp;';
        } else {  
          echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_NEWURL'));
        } ?>
      </td>
    </tr>
    
    <tr>
      <td width="20%" class="key">
        <label for="canonical">
          <?php echo JText::_('COM_SH404SEF_CANONICAL'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <?php
        if ( $this->noUrlEditing) {
          echo $this->escape( $this->meta->canonical ); 
        } else { ?> 
          <input class="text_area" type="text" size="90" id="canonical" name="canonical" value="<?php echo $this->escape( $this->meta->canonical ); ?>" /> 
        <?php } ?>
      </td>
      <td width="10%">
        <?php if ($this->noUrlEditing) {
          echo '&nbsp;';
        } else {  
          echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_CANONICAL'));
        } ?>
      </td>
    </tr>
    
    <tr>
      <td width="20%" class="key">
        <label for="name">
          <?php echo JText::_('COM_SH404SEF_PAGE_ID'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <?php
          echo $this->escape($this->pageid->pageid);
        ?>   
      </td>
      <td width="10%">
        &nbsp;
      </td>
    </tr>
    
    <tr>
      <td width="20%" class="key">
        QR code&nbsp;
      </td>
      <td width="70%">
          <img src="http://chart.apis.google.com/chart?chs=130x130&cht=qr&chld=L|0&chl=<?php echo urlencode( $this->qrCodeUrl); ?>" alt="QR code" width="130" height="130">
      </td>
      <td width="10%">
        &nbsp;
      </td>
    </tr>
    
  </tbody>  
  </table>
