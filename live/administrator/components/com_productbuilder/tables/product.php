<?php
/**
* product builder component
* @package productbuilder
* @version $Id:tables/product.php  2012-2-3 sakisTerz $
* @author Sakis Terz(sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/


defined ('_JEXEC') or die ('restricted access');

class TableProduct extends JTable{


function __construct(&$db){
	parent::__construct('#__pb_products', 'id', $db);
}

}
?>

