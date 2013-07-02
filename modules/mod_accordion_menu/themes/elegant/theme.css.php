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

#<?php echo $module->containerinstanceid; ?> .<?php echo $module->containerinstanceid; ?>-inner {
  overflow: hidden;
  width: 100%;
  border-radius: <?php echo OfflajnValueParser::parseUnit($params->get('borderradius'), ' '); ?>;
  -moz-box-shadow: 0px 0px 3px #333;
  -webkit-box-shadow: 0px 0px 3px #333;
  box-shadow: 0px 0px 3px #333;
}

#<?php echo $module->containerinstanceid; ?> dl,
#<?php echo $module->containerinstanceid; ?> dt{
  display: block;
  position: relative;
}

<?php
  $gradient = explode('-', $this->params->get('titlegradient'));
  //$gradientimg = $helper->generateGradient(1, intval($this->params->get('gradientheight', 40)), $gradient[1], $gradient[2], 'vertical');
  $background = $bgHelper->generateGradientBackground($this->params->get('titlegradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');
?>

#<?php echo $module->containerinstanceid; ?> .title{
  <?php $br = OfflajnValueParser::parse($params->get('borderradius'), ' '); ?>
  border-top-left-radius: <?php echo isset($br[0]) ? $br[0]: 0; ?>px;
  border-top-right-radius: <?php echo isset($br[1]) ? $br[1]: 0; ?>px;
  <?php if($gradient[0] == 1): ?>
  <?php echo $background; ?>
  <?php else: ?>
  background: none;
  <?php endif; ?>
  min-height: 36px;
}

#<?php echo $module->containerinstanceid; ?> .title h3{
  margin: 0;
  padding-top: 8px;
  padding-bottom: 5px;
  min-height: 26px;  
  -moz-box-shadow:inset 0px 0px 1px RGBA(255,255,255,0.7);
  -webkit-box-shadow:inset 0px 0px 1px RGBA(255,255,255,0.7);
  box-shadow:inset 0px 0px 1px RGBA(255,255,255,0.7);
  border-top-left-radius: <?php echo isset($br[0]) ? $br[0]: 0; ?>px;
  border-top-right-radius: <?php echo isset($br[1]) ? $br[1]: 0; ?>px;
  border: solid 1px RGBA(0,0,0,0.1);
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
  padding: <?php echo OfflajnValueParser::parseUnit($params->get('level'.$i.'padding'), ' '); ?>;
  margin: 0 6px;
}

.dj_ie6 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner a,
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> .inner a{
  float: left;
}

/*
Productnum
*/
<?php
  $gradient = explode('-', $this->params->get('level'.$i.'bggradient'));
  $textfont = 'level'.$i.'textfont';
  //$f = $$t;
?>

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
  <?php if ($gradient[0] == 1) : ?>
    background-color: #<?php echo $gradient[2]; ?>;
    -moz-box-shadow: 2px 3px 2px rgba(0, 0, 0, 0.4) inset, 0 0 1px rgba(255, 255, 255, 0.8) inset;
    -webkit-box-shadow: 2px 3px 2px rgba(0, 0, 0, 0.4) inset, 0 0 1px rgba(255, 255, 255, 0.8) inset;
    box-shadow: 2px 3px 2px rgba(0, 0, 0, 0.4) inset, 0 0 1px rgba(255, 255, 255, 0.8) inset;
    margin-top: 2px;
  <?php else: ?>
    background-color: #e9e9e9;
    -moz-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    -webkit-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    border: 1px solid #bcbcbc;
    margin-top: -2px;
  <?php endif; ?>
  line-height: 18px;
  float: right;  
  border-radius: 9px;
  font-size: 11px;
  line-height: 18px;
  margin-left: 5px;
  <?php //if ($params->get('level'.$i.'plusimageposition') == 'right'): ?>
   margin-right: 20px;
  <?php //endif; ?>
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner{ 
  float: left;
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
 width: 50px;
 margin-top: -20px;
}

.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.more{
  padding-left: 10px;
  padding-right: 10px;
}

<?php //if ($f[15] == "right" || $f[15] == "center") : ?>
.dj_ie7 #<?php echo $module->containerinstanceid; ?> SPAN {
  width: auto;    
}
.dj_ie7 #<?php echo $module->containerinstanceid; ?> .inner SPAN {
  margin-right: 40px;
}
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum{
  width: auto;
  margin-right: 0px;
}
.dj_ie7 #<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.more{
  padding-left: 7px;
  padding-right: 7px;
}
<?php //endif; ?>


.dj_ie9 #<?php echo $module->containerinstanceid; ?> dl.level1 dt .inner .productnum {
  line-height: 16px;
  padding-top: 2px;
}

.dj_ie9 #<?php echo $module->containerinstanceid; ?> dl.level2 dt .inner .productnum {
  line-height: 14px;
  padding-top: 1px;
}

#<?php echo $module->containerinstanceid; ?> dl.level2 dt .inner .productnum {
  line-height: 16px;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.one{
  padding-left: 10px;
  padding-right: 10px;
}

#<?php echo $module->containerinstanceid; ?> dl.level2 dt .inner .productnum.one{
  padding-left: 9px;
  padding-right: 9px;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt .inner .productnum.more{
  padding-left: 8px;
  padding-right: 8px;
}



<?php
   $gradient = explode('-', $this->params->get('level'.$i.'hoverbggradient'));
?>
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opened .inner .productnum,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opening .inner .productnum,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt:HOVER .inner .productnum{
  <?php if ($gradient[0] == 1) : ?>
    background-color: #<?php echo $gradient[2]; ?>;
    -moz-box-shadow: 1px 3px 2px rgba(0, 0, 0, 0.4) inset;
    -webkit-box-shadow: 1px 3px 2px rgba(0, 0, 0, 0.4) inset;
    box-shadow: 1px 3px 2px rgba(0, 0, 0, 0.4) inset;
  <?php else: ?>
    background-color: #e9e9e9;
    -moz-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    -webkit-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1) inset;
    border: 1px solid #bcbcbc;
  <?php endif; ?>
}


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

<?php
  $gradient = explode('-', $this->params->get('level'.$i.'bggradient'));
  //$gradientimg = $helper->generateGradient(1, intval($this->params->get('gradientheight', 40)), $gradient[1], $gradient[2], 'vertical');
  $background = $bgHelper->generateGradientBackground($this->params->get('level'.$i.'bggradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');
?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt{
  margin: <?php echo OfflajnValueParser::parseUnit($this->params->get('level'.$i.'margin'), ' '); ?>;
  margin-bottom: 0px;
}
<?php if($i == 1): ?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.last{
  margin: <?php echo OfflajnValueParser::parseUnit($this->params->get('level'.$i.'margin'), ' '); ?>;
}
<?php else: ?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.last{
  margin: <?php echo OfflajnValueParser::parseUnit($this->params->get('level'.$i.'margin'), ' '); ?>;
  margin-bottom: 0px;
}
<?php endif; ?>
/*
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opened,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opening,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.closing{
  margin-bottom: 0px;
}

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dl{
  margin: <?php echo $params->get('level'.$i.'margin'); ?>;
  margin-top: 0px;
  margin-right: 0px;
  margin-left: 0px;
}*/

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt:HOVER,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opened,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opening{
  border-radius: <?php echo OfflajnValueParser::parseUnit($this->params->get('level'.$i.'borderradius'), ' '); ?>;
  <?php if($gradient[0] == 1): ?>
  <?php echo $background; ?>
  <?php else: ?>
  background: none;
  <?php endif; ?>
}

<?php
  $gradient = explode('-', $this->params->get('level'.$i.'hoverbggradient'));
  //$gradientimg = $helper->generateGradient(1, intval($this->params->get('gradientheight', 40)), $gradient[1], $gradient[2], 'vertical');
  $background = $bgHelper->generateGradientBackground($this->params->get('level'.$i.'hoverbggradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');
?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt:HOVER{
  <?php if($gradient[0] == 1): ?>
  <?php echo $background; ?>
  <?php endif; ?>
}

<?php
  $gradient = explode('-', $this->params->get('level'.$i.'openedbggradient'));
  //$gradientimg = $helper->generateGradient(1, intval($this->params->get('gradientheight', 40)), $gradient[1], $gradient[2], 'vertical');
  $background = $bgHelper->generateGradientBackground($this->params->get('level'.$i.'openedbggradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');
?>
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opening,
#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.opened{
  <?php if($gradient[0] == 1): ?>
  <?php echo $background; ?>
  <?php endif; ?>
}

<?php
  $gradient = explode('-', $this->params->get('level'.$i.'activebggradient'));
  //$gradientimg = $helper->generateGradient(1, intval($this->params->get('gradientheight', 40)), $gradient[1], $gradient[2], 'vertical');
  $background = $bgHelper->generateGradientBackground($this->params->get('level'.$i.'activebggradient'), 1, intval($this->params->get('gradientheight', 400)), 'vertical');
?>

#<?php echo $module->containerinstanceid; ?> .level<?php echo $i ?> dt.active{
  <?php if($gradient[0] == 1): ?>
  <?php echo $background; ?>
  <?php endif; ?>
}

<?php $textfont = 'level'.$i.'textfont'; ?>
<?php $textfont = 'level'.$i.'textfont'; ?>
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt span{
  text-shadow: none;
  /*font chooser*/
  text-shadow: none;
  <?php $fonts->printFont($textfont, 'Text'); ?>
  text-decoration: none;
  /*font chooser*/
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a span{
  text-shadow: none;
  /*font chooser*/
  <?php $fonts->printFont($textfont, 'Link', true); ?>
  text-decoration: none;
  /*font chooser*/
  background: transparent;
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opening a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opening a span,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opened a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.opened a span,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.active a,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt.active a span{
    text-shadow: none;
  /*font chooser*/
  <?php $fonts->printFont($textfont, 'Active', true); ?>
  text-decoration: none;
  /*font chooser*/
}

#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a:HOVER,
#<?php echo $module->containerinstanceid; ?> dl.level<?php echo $i ?> dt a:HOVER span{
    text-shadow: none;
  /*font chooser*/
  <?php $fonts->printFont($textfont, 'Hover', true); ?>
  text-decoration: none;
  /*font chooser*/
}

<?php
++$i;
}while($i <= $definedLevel);
?>