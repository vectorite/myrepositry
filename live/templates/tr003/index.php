<?php
defined('_JEXEC') or die;

/**
 * Template for Joomla! CMS, created with Artisteer.
 * See readme.txt for more details on how to use the template.
 */



require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

// Create alias for $this object reference:
$document = & $this;

// Shortcut for template base url:
$templateUrl = $document->baseurl . '/templates/' . $document->template;

// Initialize $view:
$view = $this->artx = new ArtxPage($this);

// Decorate component with Artisteer style:
$view->componentWrapper();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $document->language; ?>" lang="<?php echo $document->language; ?>" dir="ltr">
<html xmlns:fb="http://ogp.me/ns/fb#">
<head>
 <jdoc:include type="head" />
 <link rel="stylesheet" href="<?php echo $document->baseurl; ?>/templates/system/css/system.css" type="text/css" />
 <link rel="stylesheet" href="<?php echo $document->baseurl; ?>/templates/system/css/general.css" type="text/css" />
 <link rel="stylesheet" type="text/css" href="<?php echo $templateUrl; ?>/css/template.css" media="screen" />
 <!--[if IE 6]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie6.css" type="text/css" media="screen" /><![endif]-->
 <!--[if IE 7]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie7.css" type="text/css" media="screen" /><![endif]-->
 <script type="text/javascript">if ('undefined' != typeof jQuery) document._artxJQueryBackup = jQuery;</script>
 <script type="text/javascript" src="<?php echo $templateUrl; ?>/jquery.js"></script>
 <script type="text/javascript">jQuery.noConflict();</script>
 <script type="text/javascript" src="<?php echo $templateUrl; ?>/script.js"></script>
 <script type="text/javascript">if (document._artxJQueryBackup) jQuery = document._artxJQueryBackup;</script>
</head>
<body>
<div id="nim-page-background-glare-wrapper">
    <div id="nim-page-background-glare"></div>
</div>
<div id="nim-main">
    <div class="cleared reset-box"></div>
<div class="nim-header"><div class="nim-header-position"><div class="nim-header-jpeg"><div id="header_right"><jdoc:include type="modules" name="position-31" style="art-block" /></div></div>
   <div class="nim-header-wrapper">
        <div class="cleared reset-box"></div>
        <div class="go-home"><a href="/"><img src="<?php echo $templateUrl; ?>/images/logo.jpg" width="454" height="79" border="0" Hspace="30" alt="Home" id="logo"></a></div>
        <div class="nim-header-inner">
<div class="nim-logo">
</div>

        </div>
    </div>
</div>


</div>
<div class="cleared reset-box"></div>
<div class="nim-box nim-sheet">
    <div class="nim-box-body nim-sheet-body">
<?php if ($view->containsModules('position-1', 'position-28', 'position-29')) : ?>
<div class="nim-bar nim-nav">
<div class="nim-nav-outer">
	<?php if ($view->containsModules('position-28')) : ?>
	<div class="nim-hmenu-extra1"><?php echo $view->position('position-28'); ?></div>
	<?php endif; ?>
	<?php if ($view->containsModules('position-29')) : ?>
	<div class="nim-hmenu-extra2"><?php echo $view->position('position-29'); ?></div>
	<?php endif; ?>
	<div class="nim-nav-center">
	<?php echo $view->position('position-1'); ?>
	</div>
</div>
</div>
<div class="cleared reset-box"></div>
<?php endif; ?>
<?php echo $view->position('position-15', 'nim-nostyle'); ?>
<?php echo $view->positions(array('position-16' => 33, 'position-17' => 33, 'position-18' => 34), 'nim-block'); ?>
<div class="nim-layout-wrapper">
    <div class="nim-content-layout">
        <div class="nim-content-layout-row">
<?php if ($view->containsModules('position-7', 'position-4', 'position-5')) : ?>
<div class="nim-layout-cell nim-sidebar1">
<?php echo $view->position('position-7', 'nim-block'); ?>
<?php echo $view->position('position-4', 'nim-block'); ?>
<?php echo $view->position('position-5', 'nim-block'); ?>

  <div class="cleared"></div>
</div>
<?php endif; ?>
<div class="nim-layout-cell nim-content">

<?php
  echo $view->position('position-19', 'nim-nostyle');
  if ($view->containsModules('position-2'))
    echo artxPost($view->position('position-2'));
  echo $view->positions(array('position-20' => 50, 'position-21' => 50), 'nim-article');
  echo $view->position('position-12', 'nim-nostyle');
  if ($view->hasMessages())
    echo artxPost('<jdoc:include type="message" />');
  echo '<jdoc:include type="component" />';
  echo $view->position('position-22', 'nim-nostyle');
  echo $view->positions(array('position-23' => 50, 'position-24' => 50), 'nim-article');
  echo $view->position('position-25', 'nim-nostyle');
?>

  <div class="cleared"></div>
</div>

        </div>
    </div>
</div>
<div class="cleared"></div>


<?php echo $view->positions(array('position-9' => 33, 'position-10' => 33, 'position-11' => 34), 'nim-block'); ?>
<?php echo $view->position('position-26', 'nim-nostyle'); ?>
<div class="nim-footer">
    <div class="nim-footer-body">
                <div class="nim-footer-text">
                    <?php if ($view->containsModules('position-27')): ?>
                    <?php echo $view->position('position-27', 'nim-nostyle'); ?>
                    <?php else: ?>
                    <?php ob_start(); ?>
<p>Copyright © 2012. Tile Redi Sales, LLC. All Rights Reserved.</p>

                    <?php echo str_replace('%YEAR%', date('Y'), ob_get_clean()); ?>
                    <?php endif; ?>
                </div>
        <div class="cleared"></div>
    </div>
</div>

		<div class="cleared"></div>
    </div>
</div>
<div class="cleared"></div>
<p class="nim-page-footer"></p>

    <div class="cleared"></div>
</div>

<?php echo $view->position('debug'); ?>

  <script src="components/com_virtuemart/assets/js/jquery.noConflict.js" type="text/javascript"></script> 
  <script src="components/com_virtuemart/assets/js/facebox.js" type="text/javascript"></script>
  <script src="components/com_virtuemart/assets/js/vmprices.js" type="text/javascript"></script>
</body>
</html>