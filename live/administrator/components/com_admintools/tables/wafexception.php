<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsTableWafexception extends FOFTable
{
	public function __construct( $table, $key, &$db )
	{
		parent::__construct( '#__admintools_wafexceptions', 'id', $db );
	}

	public function check()
	{
		if(!$this->option && !$this->view && !$this->query)
		{
			$this->setError(JText::_('ATOOLS_ERR_WAFEXCEPTIONS_ALLNULL'));
			return false;
		}

		return true;
	}
}
