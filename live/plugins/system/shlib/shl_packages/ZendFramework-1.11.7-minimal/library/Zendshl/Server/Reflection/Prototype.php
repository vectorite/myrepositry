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
 * @package    Zendshl_Server
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
defined('_JEXEC') or die;

/**
 * Zendshl_Server_Reflection_ReturnValue
 */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Reflection/ReturnValue.php';

/**
 * Zendshl_Server_Reflection_Parameter
 */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Reflection/Parameter.php';

/**
 * Method/Function prototypes
 *
 * Contains accessors for the return value and all method arguments.
 *
 * @category   Zend
 * @package    Zendshl_Server
 * @subpackage Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id: Prototype.php 23775 2011-03-01 17:25:24Z ralph $
 */
class Zendshl_Server_Reflection_Prototype
{
    /**
     * Constructor
     *
     * @param Zendshl_Server_Reflection_ReturnValue $return
     * @param array $params
     * @return void
     */
    public function __construct(Zendshl_Server_Reflection_ReturnValue $return, $params = null)
    {
        $this->_return = $return;

        if (!is_array($params) && (null !== $params)) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Reflection/Exception.php';
            throw new Zendshl_Server_Reflection_Exception('Invalid parameters');
        }

        if (is_array($params)) {
            foreach ($params as $param) {
                if (!$param instanceof Zendshl_Server_Reflection_Parameter) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Reflection/Exception.php';
                    throw new Zendshl_Server_Reflection_Exception('One or more params are invalid');
                }
            }
        }

        $this->_params = $params;
    }

    /**
     * Retrieve return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->_return->getType();
    }

    /**
     * Retrieve the return value object
     *
     * @access public
     * @return Zendshl_Server_Reflection_ReturnValue
     */
    public function getReturnValue()
    {
        return $this->_return;
    }

    /**
     * Retrieve method parameters
     *
     * @return array Array of {@link Zendshl_Server_Reflection_Parameter}s
     */
    public function getParameters()
    {
        return $this->_params;
    }
}
