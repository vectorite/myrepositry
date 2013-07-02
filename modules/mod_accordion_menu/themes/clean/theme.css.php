<?php
global $bgHelper;
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
  margin: <?php echo OfflajnValueParser::parseUnit($params->get('margin'), ' '); ?>;
}

<?php
  $gradient = explode('-', $this->params->get('gradient'));
  $background = $bgHelper->generateGradientBackground($this->params->get('gradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');
?>

#<?php echo $module->containerinstanceid; ?> .<?php echo $module->containerinstanceid; ?>-inner {
  <?php if($gradient[0] == 1): ?>
    <?php echo $background; ?>
  <?php else: ?>
  background: none;
  <?php endif; ?>
  overflow: hidden;
  width: 100%;
 ?>;
}

#<?php echo $module->containerinstanceid; ?> dl,
#<?php echo $module->containerinstanceid; ?> dt{
  display: block;
  position: relative;
}

#<?php echo $module->containerinstanceid; ?> .title{
  min-height: 36px;
}

#<?php echo $module->containerinstanceid; ?> .title h3{
  margin: 0;
  padding-top: 8px;
  padding-bottom: 5px;
  min-height: 26px;
  <?php $fonts->printFont('titlefont', 'Text'); ?>
}

#<?php echo $module->containerinstanceid; ?> dl,
#<?php echo $module->containerinstanceid; ?> dt,
#<?php echo $module->containerinstanceid; ?> dd{
  position: relative;
}

/*
border radius hack
*/
#<?php echo $module->containerinstanceid; ?> .level1 dt.last:HOVER,
#<?php echo $module->containerinstanceid; ?> .level1 dt.last.opening,
#<?php echo $module->containerinstanceid; ?> .level1 dt.last.opened,
#<?php echo $module->containerinstanceid; ?> .level1 dt.first:HOVER,
#<?php echo $module->containerinstanceid; ?> .level1 dt.first.opening,
#<?php echo $module->containerinstanceid; ?> .level1 dt.first.opened{
  -webkit-border-radius: <?php echo OfflajnValueParser::parseUnit($params->get('borderradius'), ' '); ?>;
  -moz-border-radius: <?php echo OfflajnValueParser::parseUnit($params->get('borderradius'), ' '); ?>;
  border-radius: <?php echo OfflajnValueParser::parseUnit($params->get('borderradius'), ' '); ?>;
}

#<?php echo $module->containerinstanceid; ?> .level1 dt.last:HOVER,
#<?php echo $module->containerinstanceid; ?> .level1 dt.last.opening,
#<?php echo $module->containerinstanceid; ?> .level1 dt.last.opened{
  -webkit-border-top-left-radius: 0px;
  -webkit-border-top-right-radius: 0px;
  -moz-border-radius-topleft: 0px;
  -moz-border-radius-topright: 0px;
  border-top-left-radius: 0px;
  border-top-right-radius: 0px;
}

#<?php echo $module->containerinstanceid; ?> .level1 dt.first:HOVER,
#<?php echo $module->containerinstanceid; ?> .level1 dt.first.opening,
#<?php echo $module->containerinstanceid; ?> .level1 dt.first.opened{
  -webkit-border-bottom-right-radius: 0px;
  -webkit-border-bottom-left-radius: 0px;
  -moz-border-radius-bottomright: 0px;
  -moz-border-radius-bottomleft: 0px;
  border-bottom-right-radius: 0px;
  border-bottom-left-radius: 0px;
}

#<?php echo $module->containerinstanceid; ?> .level2 dt.last:HOVER,
#<?php echo $module->containerinstanceid; ?> .level2 dt.last.opening,
#<?php echo $module->containerinstanceid; ?> .level2 dt.last.opened,
#<?php echo $module->containerinstanceid; ?> .level2 dt.first:HOVER,
#<?php echo $module->containerinstanceid; ?> .level2 dt.first.opening,
#<?php echo $module->containerinstanceid; ?> .level2 dt.first.opened{
  border-radius: 0;
}


/*
Level specific iteration
*/
<?php
$i=1;
do{
?>


/*
Productnum
*/
<?php
  $gradient = explode('-', $this->params->get('gradient'));
  $textfont = 'level'.$i.'textfont';
?>

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
  <?php if ($gradient[0] == 1) : ?>
    -moz-box-shadow: 2px 3px 3px rgba(0, 0, 0, 0.4) inset, 0 0 1px rgba(255, 255, 255, 0.8) inset;
    -webkit-box-shadow: 2px 3px 3px rgba(0, 0, 0, 0.4) inset, 0 0 1px rgba(255, 255, 255, 0.8) inset;
    box-shadow: 2px 3px 3px rgba(0, 0, 0, 0.4) inset, 0 0 1px rgba(255, 255, 255, 0.8) inset;
  <?php else: ?>
    -moz-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    -webkit-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    border: 1px solid #bcbcbc;
    margin-top: -2px;
  <?php endif; ?>
  line-height: 18px;
  <?php //if ($f[15] == "right") : ?>
   margin-left: 5px;    
  <?php //endif; ?>
  float: right;   
  border-radius: 9px;
  font-size: 10px;
  line-height: 18px;
  <?php //if ($params->get('level'.$i.'plusimageposition') == 'right'): ?>
   margin-right: 20px;
  <?php //endif; ?>
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner span{
 float: left;
}


.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
 width: 50px;
 float: right;
 margin-top: -15px;
}

<?php //if($f[15] == "right") : ?>
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
  margin-right: -20px;
  width: auto;
}  
<?php //endif; ?>

.dj_ie9 #<?php echo $module->containerinstanceid; ?> dl.level1 dt .inner .productnum {
  line-height: 16px;
  padding-top: 2px;
  box-shadow: 2px 3px 3px rgba(0, 0, 0, 0.4) inset, 0 0 2px rgba(255, 255, 255, 0.8) inset;  
}

.dj_ie9 #<?php echo $module->containerinstanceid; ?> dl.level2 dt .inner .productnum {
  line-height: 14px;
  padding-top: 1px;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.one{
  padding-left: 10px;
  padding-right: 10px;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.more{
  padding-left: 8px;
  padding-right: 8px;
}

<?php
  //$gradient = $helper->generateAlphaColor($this->params->get('level'.$i.'hoverbg', 'ffffff00'));
  //$background = $bgHelper->generateGradientBackground($this->params->get('gradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');

?>
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opened .inner .productnum,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opening .inner .productnum,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt:HOVER .inner .productnum{
  background: url('<?php //echo $background; ?>') repeat;
  -moz-box-shadow: 1px 3px 2px rgba(0, 0, 0, 0.4) inset;
  -webkit-box-shadow: 1px 3px 2px rgba(0, 0, 0, 0.4) inset;
  box-shadow: 1px 3px 2px rgba(0, 0, 0, 0.4) inset;
}

<?php //if($f[15] == "right") : ?>
.dj_ie7 #<?php echo $module->containerinstanceid; ?> SPAN {
  width: auto;
  padding-right: 10px;
}
<?php //endif; ?>
/*
Plus
*/
<?php $plus = OfflajnValueParser::parseColorizedImage($this->params->get('level'.$i.'plus')); ?>

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.parent .inner{
  <?php if($plus[0]): ?>
    background-image: url('<?php echo $plus[0]; ?>');
  <?php else: ?>
    background-image: none;
  <?php endif; ?>
  background-repeat: no-repeat;
  background-position: <?php echo $plus[1]; ?> center;
  cursor: pointer;
}

/*
Minus
*/
<?php $minus = OfflajnValueParser::parseColorizedImage($this->params->get('level'.$i.'minus')); ?>
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.parent.opened .inner,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.parent.opening .inner{
  <?php if($minus[0]): ?>
    background-image: url('<?php echo $minus[0]; ?>');
  <?php else: ?>
    background-image: none;
  <?php endif; ?>
  background-position: <?php echo $minus[1]; ?> center;
}
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt{
  margin: <?php echo OfflajnValueParser::parseUnit($params->get('padding'), ' '); ?>;
  margin-bottom: 0px;
}

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.last{
  margin: <?php echo OfflajnValueParser::parseUnit($params->get('padding'), ' '); ?>;
}
/*
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opened,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opening,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.closing{
  margin-bottom: 0px;
}

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dl{
  margin: <?php echo OfflajnValueParser::parseUnit($params->get('padding'), ' '); ?>;
  margin-top: 0px;
  margin-right: 0px;
  margin-left: 0px;
}*/

<?php
  $alphaColors = OfflajnValueParser::parse($this->params->get('level'.$i.'bg', 'ffffff00'));
?>

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt:HOVER,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opened{
  <?php echo $bgHelper->generateBackground($alphaColors[0], "repeat", "", 1); ?>
}

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt:HOVER{
  <?php echo $bgHelper->generateBackground($alphaColors[1], "repeat", "", 1); ?>
}

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opening,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opened{
  <?php echo $bgHelper->generateBackground($alphaColors[3], "repeat", "", 1); ?>
}


#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.active{
  <?php echo $bgHelper->generateBackground($alphaColors[2], "repeat", "", 1); ?>
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .outer{
  padding: <?php echo OfflajnValueParser::parseUnit($params->get('level'.$i.'margin'),' ') ?>;
  display: block;
  <?php echo $bgHelper->generateBackground($this->params->get('level'.$i.'border', 'ffffff55'), "repeat-x", "0 100%"); ?>
}
<?php if($i == 1): ?>
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?>{
  display: block;
  <?php echo $bgHelper->generateBackground($this->params->get('level'.$i.'border', 'ffffff55'), "repeat-x", "0 0"); ?>
}
<?php endif; ?>

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner{
  display: block;
  padding: <?php echo OfflajnValueParser::parseUnit($params->get('level'.$i.'padding'),' ') ?>;
  margin: 0 0 0 8px;
}

.dj_ie6 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner a,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner a{
  float: left;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt span{
  <?php $fonts->printFont($textfont, 'Text'); ?>
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a span{
  <?php $fonts->printFont($textfont, 'Link', true); ?>
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.active a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.active a span{
  <?php $fonts->printFont($textfont, 'Active', true); ?>
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a:HOVER,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a:HOVER span{
  <?php $fonts->printFont($textfont, 'Hover', true); ?>
  background: none;
}

<?php
++$i;
}while($i <= $definedLevel);
?>
