<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div class="search<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php if ($this->escape($this->params->get('page_heading'))) :?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1>
<?php endif; ?>

<?php echo $this->loadTemplate('form'); ?>
<?php if(!empty($this->results)) :

		 if ($this->error==null && count($this->results) > 0) :
			echo $this->loadTemplate('results');
		else :
			echo $this->loadTemplate('error');
		endif; 
	else :
		echo "<div class='notfound'>Result Not Found, Please Seach Again.</div>";
	endif;
		
 ?>

</div>

<style>

.notfound {

text-align:center; 
font-size:15px;
margin:30px 10px 10px 10px;
color:#004F8A;
font-weight:bold;
background: none repeat scroll 0 0 #FFFFFF;
box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1) inset;
padding: 30px;

}

</style>