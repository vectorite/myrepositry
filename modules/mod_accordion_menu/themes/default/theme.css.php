			<?php 
/*------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

?>
#<?php echo $module->containerinstanceid; ?>{
  margin: <?php echo OfflajnValueParser::parseUnit($params->get('margin'),' ');?>;
}

#<?php echo $module->containerinstanceid; ?> .<?php echo $module->containerinstanceid; ?>-inner {
  overflow: hidden;
  width: 100%;
}

#<?php echo $module->containerinstanceid; ?> dl,
#<?php echo $module->containerinstanceid; ?> dt{
  display: block;
  position: relative;
}

#<?php echo $module->containerinstanceid; ?> .title{
  background: none;
  min-height: 36px;
}

#<?php echo $module->containerinstanceid; ?> .title h3{
  margin: 0;
  padding: 5px 0;
  min-height: 26px;	
  
  box-shadow:inset 0px 0px 1px RGBA(255,255,255,0.7);
  /*
  border: solid 1px RGBA(0,0,0,0.1);
  */
  
  /*font chooser*/
  <?php $fonts->printFont('titlefont', 'Text'); ?>
  /*font chooser*/
}

#<?php echo $module->containerinstanceid; ?> dl,
#<?php echo $module->containerinstanceid; ?> dt,
#<?php echo $module->containerinstanceid; ?> dd{
  position: relative;
}

#<?php echo $module->containerinstanceid; ?> dl.level2 dt.last{
  margin-bottom: 0px;
}

/*
Level specific iteration
*/
<?php
$i=1;
do{
?>

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner{
  display: block;
  padding: <?php echo OfflajnValueParser::parseUnit($params->get('level'.$i.'padding'),' ') ?>;
  margin: 0 6px;
}

.dj_ie6 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner a,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner a{
  float: left;
}

/*
Productnum
*/
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
  -moz-box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3) inset;
  -webkit-box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3) inset;
  box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3) inset;
  border: 1px solid #bcbcbc;
  line-height: 14px;
  float: right;  
  border-radius: 9px;
  font-size: 10px;
  margin-top: 2px;
  margin-left: 5px;
  <?php if ($params->get('level'.$i.'plusimageposition') == 'right'): ?>
   margin-right: 20px;
  <?php endif; ?>
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner span {
  float: left;
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.one{
  padding-left: 6px;
  padding-right: 4px;
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.more{
  padding-left: 4px;
  padding-right: 6px;
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
  width: 10px;
  position: relative;
  float: right;
  margin-right: 50px;
  margin-top: -20px;
}
<?php 
  $f = 'level'.$i.'textfont';
?>

.dj_ie9 #<?php echo $module->containerinstanceid; ?> dl.level1 dt .inner .productnum {
  line-height: 12px;
  padding-top: 2px;
}

.dj_ie9 #<?php echo $module->containerinstanceid; ?> dl.level2 dt .inner .productnum {
  line-height: 12px;
  padding-top: 1px;
}


#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.one{
  padding-left: 8px;
  padding-right: 8px;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.more{
  padding-left: 6px;
  padding-right: 6px;
}
<?php
$plus = OfflajnValueParser::parseColorizedImage($this->params->get('level'.$i.'plus'));
?>
/*
Plus
*/
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.parent .inner{
  background-image: url('<?php echo $plus[0]; ?>');
  background-repeat: no-repeat;
  background-position: <?php echo $plus[1]; ?> center;
  cursor: pointer;
}

<?php
$minus = OfflajnValueParser::parseColorizedImage($this->params->get('level'.$i.'minus'));
?>
/*
Minus
*/
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.parent.opened .inner,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.parent.opening .inner{
  background-image: url('<?php echo $minus[0]; ?>');
  background-repeat: no-repeat;
  background-position: <?php echo $minus[1]; ?> center;
}
<?php
$border = OfflajnValueParser::parseBorder($params->get('level'.$i.'border'));
if($border[1][0] == '#') $border[1] = substr($border[1], 1, 6);
?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt{
  margin: <?php echo OfflajnValueParser::parseUnit($params->get('level'.$i.'margin'),' ') ?>;
  margin-bottom: 0px;
  border-width: <?php echo $border[0]; ?>;
  border-color: <?php echo $border[1]; ?>;
  border-style: <?php echo $border[2]; ?>;
  
}
<?php if($i == 1): ?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.last{
  margin: <?php echo $params->get('level'.$i.'margin'); ?>;
}
<?php else: ?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.last{
  margin: <?php echo $params->get('level'.$i.'margin'); ?>;
  margin-bottom: 0px;
}
<?php endif; ?>

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt span{
  /*font chooser*/
  <?php $fonts->printFont($f, 'Text'); ?>
  /*font chooser*/
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a span{
  /*font chooser*/
  <?php $fonts->printFont($f, 'Link', true); ?>
  /*font chooser*/
  background: transparent;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a:HOVER,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a:HOVER span,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt span:HOVER{
  /*font chooser*/
  <?php $fonts->printFont($f, 'Hover'); ?>
  /*font chooser*/
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opening a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opening a span,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opened a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opened a span,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.active a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.active a span{
  /*font chooser*/
  <?php $fonts->printFont($f, 'Active'); ?>
  /*font chooser*/
}

<?php
++$i;
}while($i <= $definedLevel);
?>