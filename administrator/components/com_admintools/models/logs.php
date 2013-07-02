<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsModelLogs extends FOFModel
{
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select(array(
				$db->quoteName('l').'.*',
				'IF('.$db->quoteName('b').'.'.$db->quoteName('ip').', '.$db->quote(1).', '.$db->quote(0).') AS '.$db->quoteName('block')
			))
			->from($db->quoteName('#__admintools_log').' AS '.$db->quoteName('l'))
			->join('LEFT OUTER',
				$db->quoteName('#__admintools_ipblock').' AS '.$db->quoteName('b').
				'ON ('.$db->quoteName('b').'.'.$db->quoteName('ip').' = '.
				$db->quoteName('l').'.'.$db->quoteName('ip').')'
			);

		if($this->getState('groupbydate', 0) == 1)
		{
			$query->select(array(
				'DATE('.$db->quoteName('l').'.'.$db->qn('logdate').') AS '.$db->qn('date'),
				'COUNT('.$db->quoteName('l').'.'.$db->qn('id').') AS '.$db->qn('exceptions')
			));
		}
		elseif($this->getState('groupbytype', 0) == 1)
		{
			$query->select(array(
				'COUNT('.$db->quoteName('l').'.'.$db->qn('id').') AS '.$db->qn('exceptions')
			));
		}

		JLoader::import('joomla.utilities.date');

		$fltDateFrom			= $this->getState('datefrom', null, 'string');
		if($fltDateFrom) {
			$regex = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';
			if(!preg_match($regex, $fltDateFrom)) {
				$fltDateFrom = '2000-01-01 00:00:00';
				$this->setState('datefrom', '');
			}
			$date = new JDate($fltDateFrom);
			$query->where($db->quoteName('logdate').' >= '.$db->Quote($date->toSql()));
		}

		$fltDateTo				= $this->getState('dateto', null, 'string');
		if($fltDateTo) {
			$regex = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';
			if(!preg_match($regex, $fltDateTo)) {
				$fltDateTo = '2037-01-01 00:00:00';
				$this->setState('dateto', '');
			}
			$date = new JDate($fltDateTo);
			$query->where($db->quoteName('logdate').' <= '.$db->Quote($date->toSql()));
		}

		$fltIP					= $this->getState('ip', null, 'string');
		if($fltIP) {
			$fltIP = '%'.$fltIP.'%';
			$query->where($db->quoteName('l').'.'.$db->quoteName('ip').' LIKE '.$db->quote($fltIP));
		}

		$fltURL					= $this->getState('url', null, 'string');
		if($fltURL) {
			$fltURL = '%'.$fltURL.'%';
			$query->where($db->quoteName('url').' LIKE '.$db->Quote($fltURL));
		}

		$fltReason				= $this->getState('reason', null, 'cmd');
		if($fltReason) {
			$query->where($db->quoteName('reason').' = '.$db->quote($fltReason));
		}

		$this->_buildQueryGroup($query);

		if(!$overrideLimits) {
			$order = $this->getState('filter_order',null,'cmd');
			if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'logdate';
			$dir = $this->getState('filter_order_Dir', 'DESC', 'cmd');
			$query->order($order.' '.$dir);
		}

		return $query;
	}

	protected function _buildQueryGroup($query)
	{
		$db = $this->getDbo();

		if($this->getState('groupbydate', 0) == 1) {
			$query->group(array(
				'DATE('.$db->qn('l').'.'.$db->qn('logdate').')'
			));
		} elseif($this->getState('groupbytype', 0) == 1) {
			$query->group(array(
				$db->qn('l').'.'.$db->qn('reason')
			));
		}
	}
}