<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: compat.php 2012-2-17 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

class productbuilderControllerCompat extends JControllerAdmin
{

	function addTags(){
		$app=JFactory::getApplication();
		$model = $this->getModel('compat');
		$prod_id=JRequest::getInt('prod_id');
		if($model->setTags($prod_id)) {
			$tagnames=$model->getProdTagNames($prod_id);
			echo $tagnames;
		}
		else {
			echo'';
		}
		jexit();
	}

}
?>