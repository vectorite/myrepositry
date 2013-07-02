<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsModelWafexceptions extends FOFModel
{
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db)
			->select(array('*'))
			->from($db->nameQuote('#__admintools_wafexceptions'));

		$fltOption			= $this->getState('foption', null, 'string');
		if($fltOption) {
			$fltOption = '%'.$fltOption.'%';
			$query->where($db->nameQuote('option').' LIKE '.$db->quote($fltOption));
		}
		
		$fltView			= $this->getState('fview', null, 'string');
		if($fltView) {
			$fltView = '%'.$fltView.'%';
			$query->where($db->nameQuote('view').' LIKE '.$db->quote($fltView));
		}
		
		$fltQuery			= $this->getState('fquery', null, 'string');
		if($fltQuery) {
			$fltQuery = '%'.$fltQuery.'%';
			$query->where($db->nameQuote('query').' LIKE '.$db->quote($fltQuery));
		}

		if(!$overrideLimits) {
			$order = $this->getState('filter_order',null,'cmd');
			if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'id';
			$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
			$query->order($order.' '.$dir);
		}

		return $query;
	}
}