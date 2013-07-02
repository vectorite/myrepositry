<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsTableScan extends FOFTable
{
	public function __construct( $table, $key, &$db )
	{
		parent::__construct( '#__admintools_scans', 'id', $db );
	}
	
	protected function onAfterDelete($oid)
	{
		$result = parent::onAfterDelete($oid);
		if($result) {
			$result = $this->deleteScanResults($oid);
		}
		return $result;
	}
	
	public function deleteScanResults($scan_id)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->delete('#__admintools_scanalerts')
			->where($db->quoteName('scan_id').' = '.$db->quote($scan_id));
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
}