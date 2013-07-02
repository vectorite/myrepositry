<?php
/*------------------------------------------------------------------------
# com_ajax_dockcart - AJAX Dock Cart for VirtueMart
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) )
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

header('Location: '.JURI::root(false).'/administrator/index.php?option=com_modules&filter_module=mod_ajax_dockcart');
exit;