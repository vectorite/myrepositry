<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/ 
// no direct access
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.controller' );
 
 
class JControllerBase extends JController
{
    function getViewName() { JError::raise(500,"getViewName() not implemented"); } /* abstract */

    function getModelName() { JError::raise(500,"getModelName() not implemented"); } /* abstract */

    function getLayoutName() { return 'default'; }
	
    function display($cache = false, $urlparams=false)
    {      
        $doc = JFactory::getDocument();
		$viewType = $doc->getType();
        $view = &$this->getView( ucfirst($this->getViewName()), $viewType);
        
        if (ucfirst($this->getViewName())=='Order_details')
        {
          $order_id = JRequest::getVar('order_id');
          if (!isset($order_id))
          {
           $view = &$this->getView( ucfirst('orders'), $viewType);
          }
         
        }
	else
        if ($model = &$this->getModel($this->getModelName()))
        {
         if ($this->getModelName()=='order_details')
         {
          $order_id = JRequest::getVar('order_id');
          if (!isset($order_id))
          {
           
           $model = &$this->getModel('orders');
          }
         }
            $view->setModel($model, true);
        } 
        else
        {
        $viewn = ucfirst($this->getViewName());
        if (!empty($viewn))
        {
           $view = &$this->getView( ucfirst($viewn), $viewType);
           $model = &$this->getModel($viewn);
           $view->setModel($model, true);
        }
        }
		
        $view->setLayout($this->getLayoutName());
        $view->display();
    }	
	
} 
?> 