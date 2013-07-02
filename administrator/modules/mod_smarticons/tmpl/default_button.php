<?php
/**
 * @package SmartIcons Module for Joomla! 2.5
 * @version $Id: default_button.php 8 2011-08-28 15:07:19Z bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access.
defined('_JEXEC') or die;

$textStyle = "";
$linkStyle = "";
if (isset($button->params->bold)) {
	if ($button->params->bold==1) {
		$textStyle.= "font-weight:bold;";
	}
}
if (isset($button->params->italic)) {
	if ($button->params->italic==1) {
		$textStyle.= "font-style:italic; ";
	}
}
if (isset($button->params->underline)) {
	if ($button->params->underline==1) {
		$textStyle.= "text-decoration:underline;";
	}
}
if (isset($button->params->NewWindow)) {
	if ($button->params->NewWindow==1) {
		$target = ' target="_blank"';
	}
}
if (isset($button->Title)) {
	$title = ' title="'.JText::_($button->Title).'"';
}
if (isset($button->params->width)) {
	if (is_numeric($button->params->width)) {
		$linkStyle.= "width:".abs($button->params->width).'px; ';
	}
}
if (isset($button->params->height)) {
	if (is_numeric($button->params->height)) {
		$linkStyle.= "height:".abs($button->params->height).'px; ';
	}
}

if (isset($button->Text)) {
	$altText = $button->Text;
} else {
	$altText = "";
}

if (file_exists(JPATH_ROOT . "/". $button->Icon)) {
	$iconFile = "<img src=\"". JURI::root(true) . "/". $button->Icon . " \"";
	$iconFile.= !empty($altText) ? " alt=\"". $altText ."\"" : "";
	$iconFile.= "/>";
} else {
	$iconFile = JHTML::_('image', $button->Icon, $altText, NULL, true);
}
$html = "<div class=\"icon-wrapper\""; 
$html.= !empty($button->id) ? ' id="'.$button->id.'"' : '';
$html.=">";
$html.= "	<div class=\"icon\">";
$html.= "		<a ";
$html.= !empty($linkStyle) ? 'style="'.$linkStyle.'"' : '';
$html.= "href=\"". $button->Target ."\"";
$html.= isset($target) ?  $target : "";
$html.= isset($button->Title) ? " title=\"". JText::_($button->Title) . "\"" : "";
$html.= ">";
if ($button->Display == 1 || $button->Display == 2) {
	$html.= "			". $iconFile;
}
if ($button->Display == 1 || $button->Display == 3) {
	$html.= "			<span";
	$html.= !empty($textStyle) ? " style=\"". $textStyle . "\"" : "";
	$html.= ">". JText::_($button->Name). "</span>"; 
}
$html.= "		</a>";
$html.= "	</div>";
$html.= "</div>";

echo $html;