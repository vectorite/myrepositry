<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsModelScanalerts extends FOFModel
{
	public function buildQuery($overrideLimits = false) {
		$db = $this->getDbo();
		$query = parent::buildQuery($overrideLimits)
			->clear('select')
			->clear('order')
			->select(array(
				$db->nameQuote('admintools_scanalert_id'),
				'IF('.$db->nameQuote('diff').' != "",0,1) AS '.$db->nameQuote('newfile'),
				'IF('.$db->nameQuote('diff').' LIKE "###SUSPICIOUS FILE###%",1,0) AS '.$db->nameQuote('suspicious'),
				'IF('.$db->nameQuote('diff').' != "",'.
				'IF('.$db->nameQuote('diff').' LIKE "###SUSPICIOUS FILE###%",'.
				$db->quote('0-suspicious').','.$db->quote('2-modified').')'
				.','.$db->quote('1-new').') AS '.$db->nameQuote('filestatus'),
				$db->nameQuote('path'),
				$db->nameQuote('threat_score'),
				$db->nameQuote('acknowledged'),
			));

		if(!$overrideLimits) {
			$order = $this->getState('filter_order',null,'cmd');
			$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
			if(!in_array($order, array('path','threat_score','acknowledged','filestatus','newfile','suspcious'))) {
				$order = 'threat_score';
				$dir = 'DESC';
			}
			$query->order($db->nameQuote($order).' '.$dir);
		}

		return $query;
	}
}