<?php
/**
* product builder component
* @package productbuilder
* @version $Id:2 tables/groupquantity.php  2012-2-8 sakisTerz $
* @author Sakis Terz (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/


defined ('_JEXEC') or die ('restricted access');

class TableGroupquantity extends JTable{


function TableGroupquantity(&$db){
    parent::__construct('#__pb_quant_group', 'group_id', $db);
}

}
?>