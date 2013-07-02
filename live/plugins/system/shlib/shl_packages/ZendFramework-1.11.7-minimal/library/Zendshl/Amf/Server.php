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
 * @version    $Id: Server.php 23897 2011-04-30 02:07:57Z yoshida@zend.co.jp $
 */
defined('_JEXEC') or die;

/** @see Zendshl_Server_Interface */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Interface.php';

/** @see Zendshl_Server_Reflection */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Server/Reflection.php';

/** @see Zendshl_Amf_Constants */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Constants.php';

/** @see Zendshl_Amf_Value_MessageBody */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Value/MessageBody.php';

/** @see Zendshl_Amf_Value_MessageHeader */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Value/MessageHeader.php';

/** @see Zendshl_Amf_Value_Messaging_CommandMessage */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Value/Messaging/CommandMessage.php';

/** @see Zendshl_Loader_PluginLoader */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Loader/PluginLoader.php';

/** @see Zendshl_Amf_Parse_TypeLoader */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Parse/TypeLoader.php';

/** @see Zendshl_Auth */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Auth.php';
/**
 * An AMF gateway server implementation to allow the connection of the Adobe Flash Player to
 * Zend Framework
 *
 * @todo       Make the reflection methods cache and autoload.
 * @package    Zendshl_Amf
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zendshl_Amf_Server implements Zendshl_Server_Interface
{
    /**
     * Array of dispatchables
     * @var array
     */
    protected $_methods = array();

    /**
     * Array of classes that can be called without being explicitly loaded
     *
     * Keys are class names.
     *
     * @var array
     */
    protected $_classAllowed = array();

    /**
     * Loader for classes in added directories
     * @var Zendshl_Loader_PluginLoader
     */
    protected $_loader;

    /**
     * @var bool Production flag; whether or not to return exception messages
     */
    protected $_production = true;

    /**
     * Request processed
     * @var null|Zendshl_Amf_Request
     */
    protected $_request = null;

    /**
     * Class to use for responses
     * @var null|Zendshl_Amf_Response
     */
    protected $_response;

    /**
     * Dispatch table of name => method pairs
     * @var array
     */
    protected $_table = array();

    /**
     *
     * @var bool session flag; whether or not to add a session to each response.
     */
    protected $_session = false;

    /**
     * Namespace allows all AMF calls to not clobber other PHP session variables
     * @var Zendshl_Session_NameSpace default session namespace Zendshl_amf
     */
    protected $_sesionNamespace = 'Zendshl_amf';

    /**
     * Set the default session.name if php_
     * @var string
     */
    protected $_sessionName = 'PHPSESSID';

    /**
     * Authentication handler object
     *
     * @var Zendshl_Amf_Auth_Abstract
     */
    protected $_auth;
    /**
     * ACL handler object
     *
     * @var Zendshl_Acl
     */
    protected $_acl;
    /**
     * The server constructor
     */
    public function __construct()
    {
        Zendshl_Amf_Parse_TypeLoader::setResourceLoader(new Zendshl_Loader_PluginLoader(array("Zendshl_Amf_Parse_Resource" => "Zend/Amf/Parse/Resource")));
    }

    /**
     * Set authentication adapter
     *
     * @param  Zendshl_Amf_Auth_Abstract $auth
     * @return Zendshl_Amf_Server
     */
    public function setAuth(Zendshl_Amf_Auth_Abstract $auth)
    {
        $this->_auth = $auth;
        return $this;
    }
   /**
     * Get authentication adapter
     *
     * @return Zendshl_Amf_Auth_Abstract
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * Set ACL adapter
     *
     * @param  Zendshl_Acl $acl
     * @return Zendshl_Amf_Server
     */
    public function setAcl(Zendshl_Acl $acl)
    {
        $this->_acl = $acl;
        return $this;
    }
   /**
     * Get ACL adapter
     *
     * @return Zendshl_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Set production flag
     *
     * @param  bool $flag
     * @return Zendshl_Amf_Server
     */
    public function setProduction($flag)
    {
        $this->_production = (bool) $flag;
        return $this;
    }

    /**
     * Whether or not the server is in production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->_production;
    }

    /**
     * @param namespace of all incoming sessions defaults to Zendshl_Amf
     * @return Zendshl_Amf_Server
     */
    public function setSession($namespace = 'Zendshl_Amf')
    {
        require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Session.php';
        $this->_session = true;
        $this->_sesionNamespace = new Zendshl_Session_Namespace($namespace);
        return $this;
    }

    /**
     * Whether of not the server is using sessions
     * @return bool
     */
    public function isSession()
    {
        return $this->_session;
    }

    /**
     * Check if the ACL allows accessing the function or method
     *
     * @param string|object $object Object or class being accessed
     * @param string $function Function or method being accessed
     * @return unknown_type
     */
    protected function _checkAcl($object, $function)
    {
        if(!$this->_acl) {
            return true;
        }
        if($object) {
            $class = is_object($object)?get_class($object):$object;
            if(!$this->_acl->has($class)) {
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Acl/Resource.php';
                $this->_acl->add(new Zendshl_Acl_Resource($class));
            }
            $call = array($object, "initAcl");
            if(is_callable($call) && !call_user_func($call, $this->_acl)) {
                // if initAcl returns false, no ACL check
                return true;
            }
        } else {
            $class = null;
        }

        $auth = Zendshl_Auth::getInstance();
        if($auth->hasIdentity()) {
            $role = $auth->getIdentity()->role;
        } else {
            if($this->_acl->hasRole(Zendshl_Amf_Constants::GUEST_ROLE)) {
                $role = Zendshl_Amf_Constants::GUEST_ROLE;
            } else {
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                throw new Zendshl_Amf_Server_Exception("Unauthenticated access not allowed");
            }
        }
        if($this->_acl->isAllowed($role, $class, $function)) {
            return true;
        } else {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception("Access not allowed");
        }
    }

    /**
     * Get PluginLoader for the Server
     *
     * @return Zendshl_Loader_PluginLoader
     */
    protected function getLoader()
    {
        if(empty($this->_loader)) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Loader/PluginLoader.php';
            $this->_loader = new Zendshl_Loader_PluginLoader();
        }
        return $this->_loader;
    }

    /**
     * Loads a remote class or method and executes the function and returns
     * the result
     *
     * @param  string $method Is the method to execute
     * @param  mixed $param values for the method
     * @return mixed $response the result of executing the method
     * @throws Zendshl_Amf_Server_Exception
     */
    protected function _dispatch($method, $params = null, $source = null)
    {
        if($source) {
            if(($mapped = Zendshl_Amf_Parse_TypeLoader::getMappedClassName($source)) !== false) {
                $source = $mapped;
            }
        }
        $qualifiedName = empty($source) ? $method : $source . '.' . $method;

        if (!isset($this->_table[$qualifiedName])) {
            // if source is null a method that was not defined was called.
            if ($source) {
                $className = str_replace('.', '_', $source);
                if(class_exists($className, false) && !isset($this->_classAllowed[$className])) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                    throw new Zendshl_Amf_Server_Exception('Can not call "' . $className . '" - use setClass()');
                }
                try {
                    $this->getLoader()->load($className);
                } catch (Exception $e) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                    throw new Zendshl_Amf_Server_Exception('Class "' . $className . '" does not exist: '.$e->getMessage(), 0, $e);
                }
                // Add the new loaded class to the server.
                $this->setClass($className, $source);
            }

            if (!isset($this->_table[$qualifiedName])) {
                // Source is null or doesn't contain specified method
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                throw new Zendshl_Amf_Server_Exception('Method "' . $method . '" does not exist');
            }
        }

        $info = $this->_table[$qualifiedName];
        $argv = $info->getInvokeArguments();

        if (0 < count($argv)) {
            $params = array_merge($params, $argv);
        }

        if ($info instanceof Zendshl_Server_Reflection_Function) {
            $func = $info->getName();
            $this->_checkAcl(null, $func);
            $return = call_user_func_array($func, $params);
        } elseif ($info instanceof Zendshl_Server_Reflection_Method) {
            // Get class
            $class = $info->getDeclaringClass()->getName();
            if ('static' == $info->isStatic()) {
                // for some reason, invokeArgs() does not work the same as
                // invoke(), and expects the first argument to be an object.
                // So, using a callback if the method is static.
                $this->_checkAcl($class, $info->getName());
                $return = call_user_func_array(array($class, $info->getName()), $params);
            } else {
                // Object methods
                try {
                    $object = $info->getDeclaringClass()->newInstance();
                } catch (Exception $e) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                    throw new Zendshl_Amf_Server_Exception('Error instantiating class ' . $class . ' to invoke method ' . $info->getName() . ': '.$e->getMessage(), 621, $e);
                }
                $this->_checkAcl($object, $info->getName());
                $return = $info->invokeArgs($object, $params);
            }
        } else {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Method missing implementation ' . get_class($info));
        }

        return $return;
    }

    /**
     * Handles each of the 11 different command message types.
     *
     * A command message is a flex.messaging.messages.CommandMessage
     *
     * @see    Zendshl_Amf_Value_Messaging_CommandMessage
     * @param  Zendshl_Amf_Value_Messaging_CommandMessage $message
     * @return Zendshl_Amf_Value_Messaging_AcknowledgeMessage
     */
    protected function _loadCommandMessage(Zendshl_Amf_Value_Messaging_CommandMessage $message)
    {
        require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Value/Messaging/AcknowledgeMessage.php';
        switch($message->operation) {
            case Zendshl_Amf_Value_Messaging_CommandMessage::DISCONNECT_OPERATION :
            case Zendshl_Amf_Value_Messaging_CommandMessage::CLIENT_PING_OPERATION :
                $return = new Zendshl_Amf_Value_Messaging_AcknowledgeMessage($message);
                break;
            case Zendshl_Amf_Value_Messaging_CommandMessage::LOGIN_OPERATION :
                $data = explode(':', base64_decode($message->body));
                $userid = $data[0];
                $password = isset($data[1])?$data[1]:"";
                if(empty($userid)) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                    throw new Zendshl_Amf_Server_Exception('Login failed: username not supplied');
                }
                if(!$this->_handleAuth($userid, $password)) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                    throw new Zendshl_Amf_Server_Exception('Authentication failed');
                }
                $return = new Zendshl_Amf_Value_Messaging_AcknowledgeMessage($message);
                break;
           case Zendshl_Amf_Value_Messaging_CommandMessage::LOGOUT_OPERATION :
                if($this->_auth) {
                    Zendshl_Auth::getInstance()->clearIdentity();
                }
                $return = new Zendshl_Amf_Value_Messaging_AcknowledgeMessage($message);
                break;
            default :
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                throw new Zendshl_Amf_Server_Exception('CommandMessage::' . $message->operation . ' not implemented');
                break;
        }
        return $return;
    }

    /**
     * Create appropriate error message
     *
     * @param int $objectEncoding Current AMF encoding
     * @param string $message Message that was being processed when error happened
     * @param string $description Error description
     * @param mixed $detail Detailed data about the error
     * @param int $code Error code
     * @param int $line Error line
     * @return Zendshl_Amf_Value_Messaging_ErrorMessage|array
     */
    protected function _errorMessage($objectEncoding, $message, $description, $detail, $code, $line)
    {
        $return = null;
        switch ($objectEncoding) {
            case Zendshl_Amf_Constants::AMF0_OBJECT_ENCODING :
                return array (
                        'description' => ($this->isProduction ()) ? '' : $description,
                        'detail' => ($this->isProduction ()) ? '' : $detail,
                        'line' => ($this->isProduction ()) ? 0 : $line,
                        'code' => $code
                );
            case Zendshl_Amf_Constants::AMF3_OBJECT_ENCODING :
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Value/Messaging/ErrorMessage.php';
                $return = new Zendshl_Amf_Value_Messaging_ErrorMessage ( $message );
                $return->faultString = $this->isProduction () ? '' : $description;
                $return->faultCode = $code;
                $return->faultDetail = $this->isProduction () ? '' : $detail;
                break;
        }
        return $return;
    }

    /**
     * Handle AMF authentication
     *
     * @param string $userid
     * @param string $password
     * @return boolean
     */
    protected function _handleAuth( $userid,  $password)
    {
        if (!$this->_auth) {
            return true;
        }
        $this->_auth->setCredentials($userid, $password);
        $auth = Zendshl_Auth::getInstance();
        $result = $auth->authenticate($this->_auth);
        if ($result->isValid()) {
            if (!$this->isSession()) {
                $this->setSession();
            }
            return true;
        } else {
            // authentication failed, good bye
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception(
                "Authentication failed: " . join("\n",
                    $result->getMessages()), $result->getCode());
        }

    }

    /**
     * Takes the deserialized AMF request and performs any operations.
     *
     * @todo   should implement and SPL observer pattern for custom AMF headers
     * @todo   DescribeService support
     * @param  Zendshl_Amf_Request $request
     * @return Zendshl_Amf_Response
     * @throws Zendshl_Amf_server_Exception|Exception
     */
    protected function _handle(Zendshl_Amf_Request $request)
    {
        // Get the object encoding of the request.
        $objectEncoding = $request->getObjectEncoding();

        // create a response object to place the output from the services.
        $response = $this->getResponse();

        // set response encoding
        $response->setObjectEncoding($objectEncoding);

        $responseBody = $request->getAmfBodies();

        $handleAuth = false;
        if ($this->_auth) {
            $headers = $request->getAmfHeaders();
            if (isset($headers[Zendshl_Amf_Constants::CREDENTIALS_HEADER]) &&
                isset($headers[Zendshl_Amf_Constants::CREDENTIALS_HEADER]->userid)) {
                $handleAuth = true;
            }
        }

        // Iterate through each of the service calls in the AMF request
        foreach($responseBody as $body)
        {
            try {
                if ($handleAuth) {
                    $message = '';
                    if ($this->_handleAuth(
                        $headers[Zendshl_Amf_Constants::CREDENTIALS_HEADER]->userid,
                        $headers[Zendshl_Amf_Constants::CREDENTIALS_HEADER]->password)) {
                        // use RequestPersistentHeader to clear credentials
                        $response->addAmfHeader(
                            new Zendshl_Amf_Value_MessageHeader(
                                Zendshl_Amf_Constants::PERSISTENT_HEADER,
                                false,
                                new Zendshl_Amf_Value_MessageHeader(
                                    Zendshl_Amf_Constants::CREDENTIALS_HEADER,
                                    false, null)));
                        $handleAuth = false;
                    }
                }

                if ($objectEncoding == Zendshl_Amf_Constants::AMF0_OBJECT_ENCODING) {
                    // AMF0 Object Encoding
                    $targetURI = $body->getTargetURI();
                    $message = '';

                    // Split the target string into its values.
                    $source = substr($targetURI, 0, strrpos($targetURI, '.'));

                    if ($source) {
                        // Break off method name from namespace into source
                        $method = substr(strrchr($targetURI, '.'), 1);
                        $return = $this->_dispatch($method, $body->getData(), $source);
                    } else {
                        // Just have a method name.
                        $return = $this->_dispatch($targetURI, $body->getData());
                    }
                } else {
                    // AMF3 read message type
                    $message = $body->getData();
                    if ($message instanceof Zendshl_Amf_Value_Messaging_CommandMessage) {
                        // async call with command message
                        $return = $this->_loadCommandMessage($message);
                    } elseif ($message instanceof Zendshl_Amf_Value_Messaging_RemotingMessage) {
                        require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Value/Messaging/AcknowledgeMessage.php';
                        $return = new Zendshl_Amf_Value_Messaging_AcknowledgeMessage($message);
                        $return->body = $this->_dispatch($message->operation, $message->body, $message->source);
                    } else {
                        // Amf3 message sent with netConnection
                        $targetURI = $body->getTargetURI();

                        // Split the target string into its values.
                        $source = substr($targetURI, 0, strrpos($targetURI, '.'));

                        if ($source) {
                            // Break off method name from namespace into source
                            $method = substr(strrchr($targetURI, '.'), 1);
                            $return = $this->_dispatch($method, $body->getData(), $source);
                        } else {
                            // Just have a method name.
                            $return = $this->_dispatch($targetURI, $body->getData());
                        }
                    }
                }
                $responseType = Zendshl_AMF_Constants::RESULT_METHOD;
            } catch (Exception $e) {
                $return = $this->_errorMessage($objectEncoding, $message,
                    $e->getMessage(), $e->getTraceAsString(),$e->getCode(),  $e->getLine());
                $responseType = Zendshl_AMF_Constants::STATUS_METHOD;
            }

            $responseURI = $body->getResponseURI() . $responseType;
            $newBody     = new Zendshl_Amf_Value_MessageBody($responseURI, null, $return);
            $response->addAmfBody($newBody);
        }
        // Add a session header to the body if session is requested.
        if($this->isSession()) {
           $currentID = session_id();
           $joint = "?";
           if(isset($_SERVER['QUERY_STRING'])) {
               if(!strpos($_SERVER['QUERY_STRING'], $currentID) !== FALSE) {
                   if(strrpos($_SERVER['QUERY_STRING'], "?") !== FALSE) {
                       $joint = "&";
                   }
               }
           }

            // create a new AMF message header with the session id as a variable.
            $sessionValue = $joint . $this->_sessionName . "=" . $currentID;
            $sessionHeader = new Zendshl_Amf_Value_MessageHeader(Zendshl_Amf_Constants::URL_APPEND_HEADER, false, $sessionValue);
            $response->addAmfHeader($sessionHeader);
        }

        // serialize the response and return serialized body.
        $response->finalize();
    }

    /**
     * Handle an AMF call from the gateway.
     *
     * @param  null|Zendshl_Amf_Request $request Optional
     * @return Zendshl_Amf_Response
     */
    public function handle($request = null)
    {
        // Check if request was passed otherwise get it from the server
        if ($request === null || !$request instanceof Zendshl_Amf_Request) {
            $request = $this->getRequest();
        } else {
            $this->setRequest($request);
        }
        if ($this->isSession()) {
             // Check if a session is being sent from the amf call
             if (isset($_COOKIE[$this->_sessionName])) {
                 session_id($_COOKIE[$this->_sessionName]);
             }
        }

        // Check for errors that may have happend in deserialization of Request.
        try {
            // Take converted PHP objects and handle service call.
            // Serialize to Zendshl_Amf_response for output stream
            $this->_handle($request);
            $response = $this->getResponse();
        } catch (Exception $e) {
            // Handle any errors in the serialization and service  calls.
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Handle error: ' . $e->getMessage() . ' ' . $e->getLine(), 0, $e);
        }

        // Return the Amf serialized output string
        return $response;
    }

    /**
     * Set request object
     *
     * @param  string|Zendshl_Amf_Request $request
     * @return Zendshl_Amf_Server
     */
    public function setRequest($request)
    {
        if (is_string($request) && class_exists($request)) {
            $request = new $request();
            if (!$request instanceof Zendshl_Amf_Request) {
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                throw new Zendshl_Amf_Server_Exception('Invalid request class');
            }
        } elseif (!$request instanceof Zendshl_Amf_Request) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Invalid request object');
        }
        $this->_request = $request;
        return $this;
    }

    /**
     * Return currently registered request object
     *
     * @return null|Zendshl_Amf_Request
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Request/Http.php';
            $this->setRequest(new Zendshl_Amf_Request_Http());
        }

        return $this->_request;
    }

    /**
     * Public access method to private Zendshl_Amf_Server_Response reference
     *
     * @param  string|Zendshl_Amf_Server_Response $response
     * @return Zendshl_Amf_Server
     */
    public function setResponse($response)
    {
        if (is_string($response) && class_exists($response)) {
            $response = new $response();
            if (!$response instanceof Zendshl_Amf_Response) {
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                throw new Zendshl_Amf_Server_Exception('Invalid response class');
            }
        } elseif (!$response instanceof Zendshl_Amf_Response) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Invalid response object');
        }
        $this->_response = $response;
        return $this;
    }

    /**
     * get a reference to the Zendshl_Amf_response instance
     *
     * @return Zendshl_Amf_Server_Response
     */
    public function getResponse()
    {
        if (null === ($response = $this->_response)) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Response/Http.php';
            $this->setResponse(new Zendshl_Amf_Response_Http());
        }
        return $this->_response;
    }

    /**
     * Attach a class or object to the server
     *
     * Class may be either a class name or an instantiated object. Reflection
     * is done on the class or object to determine the available public
     * methods, and each is attached to the server as and available method. If
     * a $namespace has been provided, that namespace is used to prefix
     * AMF service call.
     *
     * @param  string|object $class
     * @param  string $namespace Optional
     * @param  mixed $arg Optional arguments to pass to a method
     * @return Zendshl_Amf_Server
     * @throws Zendshl_Amf_Server_Exception on invalid input
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        if (is_string($class) && !class_exists($class)){
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Invalid method or class');
        } elseif (!is_string($class) && !is_object($class)) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Invalid method or class; must be a classname or object');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = array_slice(func_get_args(), 2);
        }

        // Use the class name as the name space by default.

        if ($namespace == '') {
            $namespace = is_object($class) ? get_class($class) : $class;
        }

        $this->_classAllowed[is_object($class) ? get_class($class) : $class] = true;

        $this->_methods[] = Zendshl_Server_Reflection::reflectClass($class, $argv, $namespace);
        $this->_buildDispatchTable();

        return $this;
    }

    /**
     * Attach a function to the server
     *
     * Additional arguments to pass to the function at dispatch may be passed;
     * any arguments following the namespace will be aggregated and passed at
     * dispatch time.
     *
     * @param  string|array $function Valid callback
     * @param  string $namespace Optional namespace prefix
     * @return Zendshl_Amf_Server
     * @throws Zendshl_Amf_Server_Exception
     */
    public function addFunction($function, $namespace = '')
    {
        if (!is_string($function) && !is_array($function)) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
            throw new Zendshl_Amf_Server_Exception('Unable to attach function');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = array_slice(func_get_args(), 2);
        }

        $function = (array) $function;
        foreach ($function as $func) {
            if (!is_string($func) || !function_exists($func)) {
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                throw new Zendshl_Amf_Server_Exception('Unable to attach function');
            }
            $this->_methods[] = Zendshl_Server_Reflection::reflectFunction($func, $argv, $namespace);
        }

        $this->_buildDispatchTable();
        return $this;
    }


    /**
     * Creates an array of directories in which services can reside.
     * TODO: add support for prefixes?
     *
     * @param string $dir
     */
    public function addDirectory($dir)
    {
        $this->getLoader()->addPrefixPath("", $dir);
    }

    /**
     * Returns an array of directories that can hold services.
     *
     * @return array
     */
    public function getDirectory()
    {
        return $this->getLoader()->getPaths("");
    }

    /**
     * (Re)Build the dispatch table
     *
     * The dispatch table consists of a an array of method name =>
     * Zendshl_Server_Reflection_Function_Abstract pairs
     *
     * @return void
     */
    protected function _buildDispatchTable()
    {
        $table = array();
        foreach ($this->_methods as $key => $dispatchable) {
            if ($dispatchable instanceof Zendshl_Server_Reflection_Function_Abstract) {
                $ns   = $dispatchable->getNamespace();
                $name = $dispatchable->getName();
                $name = empty($ns) ? $name : $ns . '.' . $name;

                if (isset($table[$name])) {
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                    throw new Zendshl_Amf_Server_Exception('Duplicate method registered: ' . $name);
                }
                $table[$name] = $dispatchable;
                continue;
            }

            if ($dispatchable instanceof Zendshl_Server_Reflection_Class) {
                foreach ($dispatchable->getMethods() as $method) {
                    $ns   = $method->getNamespace();
                    $name = $method->getName();
                    $name = empty($ns) ? $name : $ns . '.' . $name;

                    if (isset($table[$name])) {
                        require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Server/Exception.php';
                        throw new Zendshl_Amf_Server_Exception('Duplicate method registered: ' . $name);
                    }
                    $table[$name] = $method;
                    continue;
                }
            }
        }
        $this->_table = $table;
    }



    /**
     * Raise a server fault
     *
     * Unimplemented
     *
     * @param  string|Exception $fault
     * @return void
     */
    public function fault($fault = null, $code = 404)
    {
    }

    /**
     * Returns a list of registered methods
     *
     * Returns an array of dispatchables (Zendshl_Server_Reflection_Function,
     * _Method, and _Class items).
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->_table;
    }

    /**
     * Set server persistence
     *
     * Unimplemented
     *
     * @param  mixed $mode
     * @return void
     */
    public function setPersistence($mode)
    {
    }

    /**
     * Load server definition
     *
     * Unimplemented
     *
     * @param  array $definition
     * @return void
     */
    public function loadFunctions($definition)
    {
    }

    /**
     * Map ActionScript classes to PHP classes
     *
     * @param  string $asClass
     * @param  string $phpClass
     * @return Zendshl_Amf_Server
     */
    public function setClassMap($asClass, $phpClass)
    {
        require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Amf/Parse/TypeLoader.php';
        Zendshl_Amf_Parse_TypeLoader::setMapping($asClass, $phpClass);
        return $this;
    }

    /**
     * List all available methods
     *
     * Returns an array of method names.
     *
     * @return array
     */
    public function listMethods()
    {
        return array_keys($this->_table);
    }
}
