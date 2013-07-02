<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();
include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
$loader = new OPCloader; 




$amount = JRequest::getVar('amount', 0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Conversion</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body onload="javascript:eval('parent.op_semafor=true;')">
<?php
// onload="javascript:eval('parent.op_semafor=true;')"

{
 $path = JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html';
 if (file_exists($path))
 {
  ob_start();
  echo @file_get_contents($path);
  $html = ob_get_clean();
  
  if (!empty($amount) && (!empty($adwords_amount[0])))
  $html = str_replace($adwords_amount[0], $amount, $html);
  echo $html;
 }

?>
<script type="text/javascript" src="<?php 
$url = $loader->getUrl(); 
echo $url; ?>components/com_onepage/trackers/footer.js">
</script>
</body>
</html>
<?php
}

