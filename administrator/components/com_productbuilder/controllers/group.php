<?php
/**
* product builder component
* @package productbuilder
* @version controllers/groups.php  2012-2-6 sakisTerz $
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');


class productbuilderControllerGroup extends JControllerForm
{

/**
 * Returns the currenr order and the language for the created group
 * 
 */
    function getGroupInfo(){
        $app=JFactory::getApplication('administrator');
        $product_id=JRequest::getVar('product_id','','get','int');
       	$model =& $this->getModel( 'group' );
        $grInfo=$model->getGroupInfo($product_id);
        if($grInfo) {
            echo $grInfo;

        }else echo '';
        jexit();
        return false;
    }   
}
?>