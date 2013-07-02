<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zendshl_Amf
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Auth.php 23775 2011-03-01 17:25:24Z ralph $
 */
defined('_JEXEC') or die;

/** @see Zendshl_Amf_Auth_Abstract */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Auth/Abstract.php';

/** @see Zendshl_Acl */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Acl.php';

/** @see Zendshl_Auth_Result */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Auth/Result.php';

/**
 * This class implements authentication against XML file with roles for Flex Builder.
 *
 * @package    Zendshl_Amf
 * @subpackage Adobe
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zendshl_Amf_Adobe_Auth extends Zendshl_Amf_Auth_Abstract
{

    /**
     * ACL for authorization
     *
     * @var Zendshl_Acl
     */
    protected $_acl;

    /**
     * Username/password array
     *
     * @var array
     */
    protected $_users = array();

    /**
     * Create auth adapter
     *
     * @param string $rolefile File containing XML with users and roles
     */
    public function __construct($rolefile)
    {
        $this->_acl = new Zendshl_Acl();
        $xml = simplexml_load_file($rolefile);
/*
Roles file format:
 <roles>
   <role id=”admin”>
        <user name=”user1” password=”pwd”/>
    </role>
   <role id=”hr”>
        <user name=”user2” password=”pwd2”/>
    </role>
</roles>
*/
        foreach($xml->role as $role) {
            $this->_acl->addRole(new Zendshl_Acl_Role((string)$role["id"]));
            foreach($role->user as $user) {
                $this->_users[(string)$user["name"]] = array("password" => (string)$user["password"],
                                                             "role" => (string)$role["id"]);
            }
        }
    }

    /**
     * Get ACL with roles from XML file
     *
     * @return Zendshl_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Perform authentication
     *
     * @throws Zendshl_Auth_Adapter_Exception
     * @return Zendshl_Auth_Result
     * @see Zendshl_Auth_Adapter_Interface#authenticate()
     */
    public function authenticate()
    {
        if (empty($this->_username) ||
            empty($this->_password)) {
            /**
             * @see Zendshl_Auth_Adapter_Exception
             */
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Auth/Adapter/Exception.php';
            throw new Zendshl_Auth_Adapter_Exception('Username/password should be set');
        }

        if(!isset($this->_users[$this->_username])) {
            return new Zendshl_Auth_Result(Zendshl_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                array('Username not found')
                );
        }

        $user = $this->_users[$this->_username];
        if($user["password"] != $this->_password) {
            return new Zendshl_Auth_Result(Zendshl_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                null,
                array('Authentication failed')
                );
        }

        $id = new stdClass();
        $id->role = $user["role"];
        $id->name = $this->_username;
        return new Zendshl_Auth_Result(Zendshl_Auth_Result::SUCCESS, $id);
    }
}
