<?php
/**
* product builder component
* @package productbuilder
* @version $Id:2 tables/group.php  2012-2-8 sakisTerz $
* @author Sakis Terz (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/


defined ('_JEXEC') or die ('restricted access');

class TableGroup extends JTable{


function TableGroup(&$db){
    parent::__construct('#__pb_groups', 'id', $db);
}

}
?>