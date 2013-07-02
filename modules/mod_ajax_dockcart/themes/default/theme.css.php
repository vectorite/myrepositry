<?php
/*------------------------------------------------------------------------
# mod_ajax_dockcart - AJAX Dock Cart for VirtueMart 
# ------------------------------------------------------------------------
# author    Balint Polgarfi 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
@-moz-keyframes fadeOut {
	0% 	{opacity: 1;}
	10%	{opacity: 0.2;}
	to	{opacity: 0.2;}
}

@-webkit-keyframes fadeOut {
	0% 	{opacity: 1;}
	10%	{opacity: 0.2;}
	to	{opacity: 0.2;}
}

#WWMainPage.fadeOut {

	filter: alpha(opacity=20);
  zoom:1;
}

.dj_ie6 .moduletable-dockcart {
	overflow: visible;
}

<?php if ($c['module_pos']==1) echo '.moduletable-dockcart .vmCartModule, ';?>
<?php if ($c['module_pos']==0) echo '.moduletable-dockcart, ';?>
.dj_ie6 #dockcart {
	height: 0px;
  overflow: hidden;
}

#dockcart {
  font-size: 13px;
  margin-bottom: -3px;
	bottom: 0px;
	z-index: 1000;
	visibility: hidden;
	min-width: 68px;
	background: url(<?php echo $c['center']; ?>) repeat-x left bottom;
}

#dockcart-left,
#dockcart-right,
#dockcart-icons,
#dockcart {
	height: <?php echo ($c['height']-6); ?>px;
}

#dockcart-left,
#dockcart-right {
  position: absolute;
	width: 29px;
  left: -29px;
	background: url(<?php echo $c['side']; ?>) no-repeat left bottom;
}

#dockcart-right {
  background-position: right bottom;
  left: 0;
  margin-left: 100%;
}

#dockcart-lgrad,
#dockcart-rgrad {
  position: absolute;
  right: 0px;
  bottom: 0px;
  width: 34px;
  height: 52px;
  background: url(<?php echo $c['grad']; ?>) no-repeat left bottom;
}

#dockcart-lgrad {
	left: 0px;
	z-index: 1;
  background-position: -34px bottom;
}

#dockcart-msg {
	display: none;
	top: 52px;
	color: white;
}

#dockcart-icons {
  position: relative;
	width: 100%;
	color: white;
	z-index: 1;
}

#dockcart .dojoxFisheyeListItemRemove,
.dojoxFishSelected .dojoxFisheyeListItemRemove {
	position: absolute;
	right: -4px;
	top: -4px;
  width: 27px;
  height: 29px;
  padding-left: 2px;
	line-height: 25px;
	text-align: center;
	text-shadow: 0 0 1px rgba(0, 0, 0, 0.8);
	color: #fff;
	font-weight: bold;
	background: url(<?php echo $c['url']; ?>/images/x.png) no-repeat right;
}

#dockcart .dojoxFisheyeListItem:hover .dojoxFisheyeListItemRemove,
#dockcart .dojoxFishSelected .dojoxFisheyeListItemRemove {
	background-position: left;
}

#dockcart .dojoxFisheyeListItem .dojoxFisheyeListItemRemove.hover {
  background-position: -29px;
}

.dojoxFisheyeListItem .count {
  line-height: 200%;
  text-decoration: none;
}

.dojoxFisheyeListItem:hover .count,
.dojoxFishSelected .count {
	display: none;
}

#dockcart .dojoxFisheyeListItemPrice {
  width: 100%;
  margin-top: 100%;
  text-align: center;
  line-height: 14px;
  white-space: nowrap;
  font-size: 11px;
  color: #<?php echo $this->params->get('text_c', 'FFFFFF'); ?>;
}

#dockcart .dojoxFisheyeListItemLabel {
  display: none;
	font-family: Arial, Helvetica, sans-serif;
	color: #<?php echo $this->params->get('tip_c', 'FFFFFF'); ?>;
	padding: 2px 5px 3px 5px;
	margin-bottom: 5px;
	text-align: center;
	position: absolute;
	white-space: nowrap;
	-moz-border-radius: <?php echo $c['corner'];?>px;
	border-radius: <?php echo $c['corner'];?>px;
	-webkit-box-shadow: 0px 5px 5px -5px rgba(0,0,0,0.65);
	-moz-box-shadow: 0px 5px 5px -5px rgba(0,0,0,0.65);
	box-shadow: 0px 5px 5px -5px rgba(0,0,0,0.65);
	/* Opera & IE9 */
	background: url(data:image/svg+xml;base64,<?php echo $c['svg'];?>);
	/* Firefox */
	background: -moz-linear-gradient( top, rgba(<?php echo $c['gradA'];?>), rgba(<?php echo $c['gradZ'];?>) );
	/* Chrome & Safari */
	background: -webkit-gradient( linear, left top, left bottom, color-stop( 0, rgba(<?php echo $c['gradA'];?>) ), color-stop( 1, rgba(<?php echo $c['gradZ'];?>) ) );
}

.dj_ie7 .dojoxFisheyeListItemLabel,
.dj_ie8 .dojoxFisheyeListItemLabel {
	/* IE7 */
	filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php echo $c['hexA'];?>,EndColorStr=#<?php echo $c['hexZ'];?>);
	/* IE8 */
	-ms-filter: "progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php echo $c['hexA'];?>,EndColorStr=#<?php echo $c['hexZ'];?>)";
}

.dj_opera #dockcart .dojoxFisheyeListItemLabel {
  border-radius: 0px;
}

.dojoxFisheyeListItemLabel td {
  vertical-align: top;
  text-align: left;
}

.dojoxFisheyeListItemLabel:hover {
	display: none;
}

#dockcart .dojoxFisheyeListItemRemove:hover .dojoxFisheyeListItemLabel,
#dockcart .dojoxFishSelected .dojoxFisheyeListItemLabel {
	display: block;
}

.dojoxFisheyeListItem {
	cursor: pointer;
	position: relative;
	float: left;
	z-index: 2;
}

.dojoxFisheyeListItemImage {
	border: 0px;
	position: absolute;
}

table.desc {
  border: 0px !important;
}

.desc tr, .desc td {
  border: 0px;
}

.desc img {
  height: 15px;
	-moz-border-radius: 2px;
	border-radius: 2px;
}

<?php if ($this->params->get('shadow', 0)): ?>
.dojoxFisheyeListItemImage {
	-webkit-box-shadow: 0px 14px 14px -14px rgba(0,0,0,0.6);
	-moz-box-shadow: 0px 14px 14px -14px rgba(0,0,0,0.6);
	box-shadow: 0px 14px 14px -14px rgba(0,0,0,0.6);
}

.dojoxFisheyeListItem:last-child .dojoxFisheyeListItemImage,
.dojoxFisheyeListItem:first-child .dojoxFisheyeListItemImage {
	-webkit-box-shadow: none;
	-moz-box-shadow: none;
	box-shadow: none;
}
<?php endif; ?>