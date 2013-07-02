<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

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
		$query = FOFQueryAbstract::getNew($db)
			->delete('#__admintools_scanalerts')
			->where($db->nameQuote('scan_id').' = '.$db->quote($scan_id));
		$db->setQuery($query);
		$db->query();
		
		return true;
	}
}