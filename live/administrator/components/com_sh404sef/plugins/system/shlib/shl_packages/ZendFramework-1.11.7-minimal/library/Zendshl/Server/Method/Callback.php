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
 * @subpackage Method
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Callback.php 23775 2011-03-01 17:25:24Z ralph $
 */
defined('_JEXEC') or die;

/**
 * Method callback metadata
 *
 * @category   Zend
 * @package    Zendshl_Server
 * @subpackage Method
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zendshl_Server_Method_Callback
{
    /**
     * @var string Class name for class method callback
     */
    protected $_class;

    /**
     * @var string Function name for function callback
     */
    protected $_function;

    /**
     * @var string Method name for class method callback
     */
    protected $_method;

    /**
     * @var string Callback type
     */
    protected $_type;

    /**
     * @var array Valid callback types
     */
    protected $_types = array('function', 'static', 'instance');

    /**
     * Constructor
     *
     * @param  null|array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ((null !== $options) && is_array($options))  {
            $this->setOptions($options);
        }
    }

    /**
     * Set object state from array of options
     *
     * @param  array $options
     * @return Zendshl_Server_Method_Callback
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set callback class
     *
     * @param  string $class
     * @return Zendshl_Server_Method_Callback
     */
    public function setClass($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $this->_class = $class;
        return $this;
    }

    /**
     * Get callback class
     *
     * @return string|null
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Set callback function
     *
     * @param  string $function
     * @return Zendshl_Server_Method_Callback
     */
    public function setFunction($function)
    {
        $this->_function = (string) $function;
        $this->setType('function');
        return $this;
    }

    /**
     * Get callback function
     *
     * @return null|string
     */
    public function getFunction()
    {
        return $this->_function;
    }

    /**
     * Set callback class method
     *
     * @param  string $method
     * @return Zendshl_Server_Method_Callback
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Get callback class  method
     *
     * @return null|string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set callback type
     *
     * @param  string $type
     * @return Zendshl_Server_Method_Callback
     * @throws Zendshl_Server_Exception
     */
    public function setType($type)
    {
        if (!in_array($type, $this->_types)) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Exception.php';
            throw new Zendshl_Server_Exception('Invalid method callback type  passed to ' . __CLASS__ . '::' . __METHOD__);
        }
        $this->_type = $type;
        return $this;
    }

    /**
     * Get callback type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Cast callback to array
     *
     * @return array
     */
    public function toArray()
    {
        $type = $this->getType();
        $array = array(
            'type' => $type,
        );
        if ('function' == $type) {
            $array['function'] = $this->getFunction();
        } else {
            $array['class']  = $this->getClass();
            $array['method'] = $this->getMethod();
        }
        return $array;
    }
}
