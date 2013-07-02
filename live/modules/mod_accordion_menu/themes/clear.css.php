<?php
/*-------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com
# -------------------------------------------------------------------------
# @ author    Roland Soos
# @ copyright Copyright (C) 2012 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
global $ImageHelper;
$definedLevel = 1;
for($x=1; $params->get('level'.$x) > 0 && $x < 30;$x++){
  $definedLevel = $x;
}

$fonts = new OfflajnFontHelper($params);
echo $fonts->parseFonts();
/*echo "<pre>";
$fonts->printFontExcl('fonttest','Text', array('font-size') );
echo "\n";
echo "Hover\n";
$fonts->printFont('fonttest','Hover');
echo "\n";
echo "Hover full\n";
$fonts->printFont('fonttest','Hover', true);
echo "\n";
echo "Active\n";
$fonts->printFont('fonttest','Active');
echo "\n";
echo "Active full\n";
$fonts->printFont('fonttest','Active', true);
echo "\n";
exit;*/
?>

#<?php echo $module->containerinstanceid; ?>-inner{
  overflow: hidden;
}

div#<?php echo $module->containerinstanceid; ?> div,
div#<?php echo $module->containerinstanceid; ?> dl,
div#<?php echo $module->containerinstanceid; ?> dt,
div#<?php echo $module->containerinstanceid; ?> dd,
div#<?php echo $module->containerinstanceid; ?> span,
div#<?php echo $module->containerinstanceid; ?> a,
div#<?php echo $module->containerinstanceid; ?> img,
div#<?php echo $module->containerinstanceid; ?> h3{
  width: auto;
  padding: 0;
  margin: 0;
  border: 0;
  float: none;
  clear: none;
  line-height: normal;
  position: static;
  list-style: none;
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> div,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dt,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dd,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> span,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> a,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> img,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> h3{
  width: 100%;
}

#<?php echo $module->containerinstanceid; ?> span,
#<?php echo $module->containerinstanceid; ?> a,
#<?php echo $module->containerinstanceid; ?> img{
  vertical-align: middle;
}

div#<?php echo $module->containerinstanceid; ?> img{
  margin: 0 4px;
}

#<?php echo $module->containerinstanceid; ?> dl.level1 dl{
  position: absolute;
  <?php if($params->get('snaptobottom', 0) == 1): ?> 
  bottom: 0;
  <?php endif; ?>
  width: 100%;
}

#<?php echo $module->containerinstanceid; ?> dl.level1 dd{
  display: block;
  overflow: hidden;
  height: 0px;
  width: 100%;
  margin: 0;
  position: relative;
}

.dj_ie #<?php echo $module->containerinstanceid; ?> dl.level1 dd{
  display: none;
}

#<?php echo $module->containerinstanceid; ?> dl.level1 dd.opening, 
#<?php echo $module->containerinstanceid; ?> dl.level1 dd.closing,
#<?php echo $module->containerinstanceid; ?> dl.level1 dd.opened{
  display: block;
}

#<?php echo $module->containerinstanceid; ?> dl.level1 dd.parent.opened{
  height: auto;
}

#<?php echo $module->containerinstanceid; ?> dl.level1 dd.parent.opened > dl{
  position: relative;
}