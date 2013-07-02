<?php
if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'))
  include ("ajax_dockcart_vm2.php");
else
  include ("ajax_dockcart_vm1.php");
?>