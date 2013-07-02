<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

//-- No direct access
defined('_JEXEC') or die('=;)');

/**
 * Class for table contact_enhanced_messages
 *
 */
class ContactenhancedTableMessagefields extends JTable
{
	
	/**
	 * Constructor
	 *
	 * @param $_db object Database connector object
	 */
	function __construct( &$_db )
	{
		parent::__construct( '#__ce_message_fields', 'id', $_db );
		$this->db = $_db;
	}// function

}// class
