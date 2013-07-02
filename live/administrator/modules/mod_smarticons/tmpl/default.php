<?php
/**
 * @package SmartIcons Module for Joomla! 2.5
 * @version $Id: default.php 8 2011-08-28 15:07:19Z bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// No direct access.
defined('_JEXEC') or die;

$configuration = SmartIconsHelper::getComponentConfig();
$configuration = $configuration->toObject();

//If the component has not been onfigured, use a default value
if (!isset($configuration->display_mode)) {
	$configuration->display_mode = 2;
}

$buttons = SmartIconsHelper::getButtons();
$tab = null;
$tabStarted = false;
$firstTab = false;
$lastButton = end($buttons);
$tabIndex = 0;
?>

<div id="cpanel"><?php
foreach ($buttons as $button){
	if(JFactory::getUser()->authorise('core.view', 'com_smarticons.category.'.$button->TabId)) {
		if($button->Tab != $tab) {
			if ($firstTab) {
				if ($configuration->display_mode == 2) {
					SmartIconsHelper::plugins();
				}
				$firstTab = false;
			}
			if (is_null($tab)) {
				$firstTab = true;
			}
			if (!is_null($tab)) {
				$html = ob_get_clean();
				if(!$tabStarted) {
					echo JHtml::_('tabs.start', 'Tabs');
					echo JHtml::_('tabs.panel', $tab, $tabIndex++);
					echo $html;
					$html = '';
					$tabStarted = true;
				}
				echo $html;
				echo JHtml::_('tabs.panel', $button->Tab, $tabIndex++);
			} 
			$tab = $button->Tab;
			ob_start();
		}
		echo SmartIconsHelper::button($button);
	}
	if ($button == $lastButton) {
		$html = ob_get_clean();
		echo $html;
		
		//Last tab
		if ($configuration->display_mode == 3) {
			SmartIconsHelper::plugins();
		}
	}
}

if ($tabStarted) {
	echo JHtml::_('tabs.end');
}

if (($configuration->display_mode == 2 && $firstTab == true) || (count($buttons) == 0 && $configuration->display_mode != 4)) {
	SmartIconsHelper::plugins();
}

//End of tabs or we have no tabs defined
if (($configuration->display_mode==1) ) :?>
<div class="plugins">
	<?php SmartIconsHelper::plugins();?>
</div>

<?php 
endif;
?>
</div>