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

  <div id="content-box">
    <div class="border">
      <div id="toolbar-box">
        <div class="t">
          <div class="t">
            <div class="t"></div>
          </div>
        </div>
        <div class="m">
          <?php echo $this->toolbar->render(); ?>
          <?php echo $this->toolbarTitle; ?>
          <div class="clr"></div>
        </div>
          <div class="b">
          <div class="b">
            <div class="b"></div>
          </div>
        </div>
      </div>
      <div class="clr"></div>
      <div class="clr"></div>
      <div class="clr"></div>
    </div>
  </div>
 
 <div class="clr"></div>
 
  <dl id="system-message">
  <dt class="error"></dt>
  <dd class="error message fade">
    <div id="sh-error-box">
  <?php if (!empty( $this->errors)) : ?>
      <div id="error-box-content">
        <ul>
        <?php 
          foreach ($this->errors as $error) : 
            echo '<li>' . $error . '</li>';
          endforeach;
        ?>    
        </ul>
      </div>  
    <?php endif; ?>
    </div>
  </dd>
  </dl>

  <dl id="system-message">
  <dt class="message"></dt>
  <dd class="message message fade">
  <div id="sh-message-box">
  <?php if (!empty( $this->message)) : ?>
    <ul>
      <li><div id="message-box-content"><?php if (!empty( $this->message)) echo $this->message; ?></div></li>
    </ul>
    <?php endif; ?>
    </div>
  </dd>
  </dl>
