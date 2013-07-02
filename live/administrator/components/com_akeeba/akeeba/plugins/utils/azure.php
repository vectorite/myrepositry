<?php
/**
 * Copyright (c) 2009, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

class AEUtilAzureBaseException extends Exception {}
class AEUtilAzureHttpException extends AEUtilAzureBaseException {}
class AEUtilAzureHttpTransportException extends AEUtilAzureHttpException {}
class AEUtilAzureAPIException extends AEUtilAzureBaseException {}
class AEUtilAzureRetryPolicyException extends AEUtilAzureAPIException {}

/**
 * @category   Microsoft
 * @package    Microsoft_Http
 * @subpackage Transport
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
abstract class AEUtilAzureHttpTransport
{
    /** HTTP VERBS */
    const VERB_GET      = 'GET';
    const VERB_PUT      = 'PUT';
    const VERB_POST     = 'POST';
    const VERB_DELETE   = 'DELETE';
    const VERB_HEAD     = 'HEAD';
    const VERB_MERGE    = 'MERGE';
    
	/**
	 * Use proxy?
	 * 
	 * @var boolean
	 */
	protected $_useProxy = false;
	
	/**
	 * Proxy url
	 * 
	 * @var string
	 */
	protected $_proxyUrl = '';
	
	/**
	 * Proxy port
	 * 
	 * @var int
	 */
	protected $_proxyPort = 80;
	
	/**
	 * Proxy credentials
	 * 
	 * @var string
	 */
	protected $_proxyCredentials = '';
	
	/**
	 * Set proxy
	 * 
	 * @param boolean $useProxy         Use proxy?
	 * @param string  $proxyUrl         Proxy URL
	 * @param int     $proxyPort        Proxy port
	 * @param string  $proxyCredentials Proxy credentials
	 */
	public function setProxy($useProxy = false, $proxyUrl = '', $proxyPort = 80, $proxyCredentials = '')
	{
	    $this->_useProxy = $useProxy;
	    $this->_proxyUrl = $proxyUrl;
	    $this->_proxyPort = $proxyPort;
	    $this->_proxyCredentials = $proxyCredentials;
	}
    
    /**
     * User agent string
     * 
     * @var string
     */
    protected $_userAgent = 'AEUtilAzureHttpTransport';
    
    /**
     * Perform GET request
     * 
     * @param $url              Url to request
     * @param $variables        Array of key-value pairs to use in the request
     * @param $headers          Array of key-value pairs to use as additional headers
     * @param $rawBody          Raw body to send to server
     * @return AEUtilAzureHttpResponse
     */
    public function get($url, $variables = array(), $headers = array(), $rawBody = null)
    {
        return $this->request(self::VERB_GET, $url, $variables, $headers, $rawBody);
    }
    
    /**
     * Perform PUT request
     * 
     * @param $url              Url to request
     * @param $variables        Array of key-value pairs to use in the request
     * @param $headers          Array of key-value pairs to use as additional headers
     * @param $rawBody          Raw body to send to server
     * @return AEUtilAzureHttpResponse
     */
    public function put($url, $variables = array(), $headers = array(), $rawBody = null)
    {
        return $this->request(self::VERB_PUT, $url, $variables, $headers, $rawBody);
    }
    
    /**
     * Perform POST request
     * 
     * @param $url              Url to request
     * @param $variables        Array of key-value pairs to use in the request
     * @param $headers          Array of key-value pairs to use as additional headers
     * @param $rawBody          Raw body to send to server
     * @return AEUtilAzureHttpResponse
     */
    public function post($url, $variables = array(), $headers = array(), $rawBody = null)
    {
        return $this->request(self::VERB_POST, $url, $variables, $headers, $rawBody);
    }
    
    /**
     * Perform DELETE request
     * 
     * @param $url              Url to request
     * @param $variables        Array of key-value pairs to use in the request
     * @param $headers          Array of key-value pairs to use as additional headers
     * @param $rawBody          Raw body to send to server
     * @return AEUtilAzureHttpResponse
     */
    public function delete($url, $variables = array(), $headers = array(), $rawBody = null)
    {
        return $this->request(self::VERB_DELETE, $url, $variables, $headers, $rawBody);
    }
    
    /**
     * Perform request
     * 
     * @param $httpVerb         Http verb to use in the request
     * @param $url              Url to request
     * @param $variables        Array of key-value pairs to use in the request
     * @param $headers          Array of key-value pairs to use as additional headers
     * @param $rawBody          Raw body to send to server
     * @return AEUtilAzureHttpResponse
     */
    public abstract function request($httpVerb, $url, $variables = array(), $headers = array(), $rawBody = null);
    
    /**
     * Create channel
     * 
     * @param $type string   Transport channel type
     * @return AEUtilAzureHttpTransport
     */
    public static function createChannel($type = 'AEUtilAzureHttpTransportCurl')
    {
        return new $type();
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_Http
 * @subpackage Transport
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class AEUtilAzureHttpTransportCurl extends AEUtilAzureHttpTransport
{
    /**
     * AEUtilAzureHttpTransportCurl constructor
     */
    public function __construct() 
    {
        if (!extension_loaded('curl')) {
            throw new AEUtilAzureHttpTransportException('cURL extension has to be loaded to use AEUtilAzureHttpTransportCurl.');
        }
    }
    
    /**
     * Perform request
     * 
     * @param $httpVerb         Http verb to use in the request
     * @param $url              Url to request
     * @param $variables        Array of key-value pairs to use in the request
     * @param $headers          Array of key-value pairs to use as additional headers
     * @param $rawBody          Raw body to send to server
     * @return AEUtilAzureHttpResponse
     */
    public function request($httpVerb, $url, $variables = array(), $headers = array(), $rawBody = null)
    {
        // Create a new cURL instance
        $curlHandle = curl_init();
		@curl_setopt($curlHandle, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
        curl_setopt($curlHandle, CURLOPT_USERAGENT,       $this->_userAgent);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION,  true);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,         120);

        // Set URL
        curl_setopt($curlHandle, CURLOPT_URL,             $url);
        
        // Set HTTP parameters (version and request method)
        curl_setopt($curlHandle, CURL_HTTP_VERSION_1_1,   true);
        switch ($httpVerb) {
            case AEUtilAzureHttpTransport::VERB_GET:
                curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
                break;
            case AEUtilAzureHttpTransport::VERB_POST:
                curl_setopt($curlHandle, CURLOPT_POST,    true);
                break;
            /*case AEUtilAzureHttpTransport::VERB_PUT:
                curl_setopt($curlHandle, CURLOPT_PUT,     true);
                break;*/
            case AEUtilAzureHttpTransport::VERB_HEAD:
                // http://stackoverflow.com/questions/770179/php-curl-head-request-takes-a-long-time-on-some-sites
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST,  'HEAD');
                curl_setopt($curlHandle, CURLOPT_NOBODY, true);
                break;
            default:
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST,  $httpVerb);
                break;
        }

        // Clear Content-Length header
        $headers["Content-Length"] = 0;

        // Ensure headers are returned
        curl_setopt($curlHandle, CURLOPT_HEADER,          true);
        
        // Do not verify SSl peer (Windows versions of cURL have an outdated CA)
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,  (stristr(PHP_OS, 'WIN') ? false : true));
        
        // Set proxy?
        if ($this->_useProxy)
        {
            curl_setopt($curlHandle, CURLOPT_PROXY,        $this->_proxyUrl); 
            curl_setopt($curlHandle, CURLOPT_PROXYPORT,    $this->_proxyPort); 
            curl_setopt($curlHandle, CURLOPT_PROXYUSERPWD, $this->_proxyCredentials); 
        }
        
        // Ensure response is returned
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER,  true);
        
        // Set post fields / raw data
        // http://www.php.net/manual/en/function.curl-setopt.php#81161
        if (!is_null($rawBody) || (!is_null($variables) && count($variables) > 0))
        {
            if (!is_null($rawBody))
            {
                unset($headers["Content-Length"]);
                $headers["Content-Length"] = strlen($rawBody);   
            }
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS,  is_null($rawBody) ? $variables : $rawBody);
        }

        // Set Content-Type header if required
        if (!isset($headers["Content-Type"])) {
            $headers["Content-Type"] = '';
        }
        
        // Disable Expect: 100-Continue
        // http://be2.php.net/manual/en/function.curl-setopt.php#82418
        $headers["Expect"] = '';

        // Add additional headers to cURL instance
        $curlHeaders = array();
        foreach ($headers as $key => $value)
        {
            $curlHeaders[] = $key.': '.$value;
        }
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER,      $curlHeaders);
        
        // DEBUG: curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
                
        // Execute request
        $rawResponse = curl_exec($curlHandle);
        $response    = null;
        if ($rawResponse)
        {
            $response = AEUtilAzureHttpResponse::fromString($rawResponse);
            // DEBUG: var_dump($url);  
            // DEBUG: var_dump(curl_getinfo($curlHandle,CURLINFO_HEADER_OUT));    
            // DEBUG: var_dump($rawResponse);
        }
        else
        {
            throw new AEUtilAzureHttpTransportException('cURL error occured during request for ' . $url . ': ' . curl_errno($curlHandle) . ' - ' . curl_error($curlHandle));
        }
        curl_close($curlHandle);
        
        return $response;
    }
}

/**
 * AEUtilAzureHttpResponse
 * 
 * This class is partially based on Zend Framework Zend_Http_Response - http://framework.zend.com
 * 
 * @category   Microsoft
 * @package    Microsoft_Http
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class AEUtilAzureHttpResponse
{
    /**
     * List of all known HTTP response status codes
     *
     * @var array
     */
    protected static $_statusMessages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    
    /**
     * The HTTP version (1.0, 1.1)
     *
     * @var string
     */
    protected $_version;

    /**
     * The HTTP response code
     *
     * @var int
     */
    protected $_code;

    /**
     * The HTTP response code as string
     * (e.g. 'Not Found' for 404 or 'Internal Server Error' for 500)
     *
     * @var string
     */
    protected $_message;

    /**
     * The HTTP response headers array
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * The HTTP response body
     *
     * @var string
     */
    protected $_body;

    /**
     * HTTP response constructor
     *
     * @param int $code Response code (200, 404, 500, ...)
     * @param array $headers Headers array
     * @param string $body Response body
     * @param string $version HTTP version
     * @throws AEUtilAzureHttpException
     */
    public function __construct($code, $headers, $body = null, $version = '1.1')
    {
        // Code
        $this->_code = $code;
        
        // Message
        $this->_message = self::$_statusMessages[$code];
        
        // Body
        $this->_body = $body;
        
        // Version
        if (! preg_match('|^\d\.\d$|', $version)) {
            throw new AEUtilAzureHttpException('No valid HTTP version was passed: ' . $version);
        }
        $this->_version = $version;
        
        // Headers
        if (!is_array($headers))
        {
            throw new AEUtilAzureHttpException('No valid headers were passed');
        }
        else 
        {
            foreach ($headers as $name => $value) {
                if (is_int($name))
                    list($name, $value) = explode(":", $value, 1);

                $this->_headers[ucwords(strtolower($name))] = trim($value);
            }
        }
    }

    /**
     * Check whether the response is an error
     *
     * @return boolean
     */
    public function isError()
    {
        $restype = floor($this->code / 100);
        return ($restype == 4 || $restype == 5);
    }

    /**
     * Check whether the response in successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        $restype = floor($this->_code / 100);
        return ($restype == 2 || $restype == 1);
    }

    /**
     * Check whether the response is a redirection
     *
     * @return boolean
     */
    public function isRedirect()
    {
        $restype = floor($this->_code / 100);
        return ($restype == 3);
    }

    /**
     * Get the HTTP version (1.0, 1.1)
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get the HTTP response code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_code;   
    }

    /**
     * Get the HTTP response code as string
     * (e.g. 'Not Found' for 404 or 'Internal Server Error' for 500)
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;   
    }

    /**
     * Get the HTTP response headers array
     *
     * @return array
     */
    public function getHeaders()
    {
        if (!is_array($this->_headers))
        {
            $this->_headers = array();
        }
        return $this->_headers;
    }
    
    /**
     * Get a specific header as string, or null if it is not set
     *
     * @param string $header
     * @return string|array|null
     */
    public function getHeader($header)
    {
        $header = ucwords(strtolower($header));
        if (!is_string($header) || ! isset($this->_headers[$header])) return null;

        return $this->_headers[$header];
    }

    /**
     * The HTTP response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Extract the response code from a response string
     *
     * @param string $responseString
     * @return int
     */
    public static function extractCode($responseString)
    {
        preg_match("|^HTTP/[\d\.x]+ (\d+)|", $responseString, $m);

        if (isset($m[1])) {
            return (int) $m[1];
        } else {
            return false;
        }
    }

    /**
     * Extract the HTTP message from a response
     *
     * @param string $responseString
     * @return string
     */
    public static function extractMessage($responseString)
    {
        preg_match("|^HTTP/[\d\.x]+ \d+ ([^\r\n]+)|", $responseString, $m);

        if (isset($m[1])) {
            return $m[1];
        } else {
            return false;
        }
    }

    /**
     * Extract the HTTP version from a response
     *
     * @param string $responseString
     * @return string
     */
    public static function extractVersion($responseString)
    {
        preg_match("|^HTTP/([\d\.x]+) \d+|", $responseString, $m);

        if (isset($m[1])) {
            return $m[1];
        } else {
            return false;
        }
    }

    /**
     * Extract the headers from a response string
     *
     * @param string $responseString
     * @return array
     */
    public static function extractHeaders($responseString)
    {
        $headers = array();
        
        // First, split body and headers
        $parts = preg_split('|(?:\r?\n){2}|m', $responseString, 2);
        if (! $parts[0]) return $headers;
        
        // Split headers part to lines
        $lines = explode("\n", $parts[0]);
        unset($parts);
        $last_header = null;

        foreach($lines as $line) {
            $line = trim($line, "\r\n");
            if ($line == "") break;

            if (preg_match("|^([\w-]+):\s+(.+)|", $line, $m)) {
                unset($last_header);
                $h_name = strtolower($m[1]);
                $h_value = $m[2];

                if (isset($headers[$h_name])) {
                    if (! is_array($headers[$h_name])) {
                        $headers[$h_name] = array($headers[$h_name]);
                    }

                    $headers[$h_name][] = $h_value;
                } else {
                    $headers[$h_name] = $h_value;
                }
                $last_header = $h_name;
            } elseif (preg_match("|^\s+(.+)$|", $line, $m) && $last_header !== null) {
                if (is_array($headers[$last_header])) {
                    end($headers[$last_header]);
                    $last_header_key = key($headers[$last_header]);
                    $headers[$last_header][$last_header_key] .= $m[1];
                } else {
                    $headers[$last_header] .= $m[1];
                }
            }
        }

        return $headers;
    }

    /**
     * Extract the body from a response string
     *
     * @param string $response_str
     * @return string
     */
    public static function extractBody($responseString)
    {
        $parts = preg_split('|(?:\r?\n){2}|m', $responseString, 2);
        if (isset($parts[1])) { 
            return $parts[1];
        }
        return '';
    }
    
    /**
     * Create a new AEUtilAzureHttpResponse object from a string
     *
     * @param string $response_str
     * @return AEUtilAzureHttpResponse
     */
    public static function fromString($response_str)
    {
        $code    = self::extractCode($response_str);
        $headers = self::extractHeaders($response_str);
        $body    = self::extractBody($response_str);
        $version = self::extractVersion($response_str);
        $message = self::extractMessage($response_str);

        return new AEUtilAzureHttpResponse($code, $headers, $body, $version, $message);
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */ 
abstract class AEUtilAzureCredentials
{
	/**
	 * Development storage account and key
	 */
	const DEVSTORE_ACCOUNT       = "devstoreaccount1";
	const DEVSTORE_KEY           = "Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==";
	
	/**
	 * HTTP header prefixes
	 */
	const PREFIX_PROPERTIES      = "x-ms-prop-";
	const PREFIX_METADATA        = "x-ms-meta-";
	const PREFIX_STORAGE_HEADER  = "x-ms-";
	
	/**
	 * Permissions
	 */
	const PERMISSION_READ        = "r";
	const PERMISSION_WRITE       = "w";
	const PERMISSION_DELETE      = "d";
	const PERMISSION_LIST        = "l";

	/**
	 * Account name for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountName = '';
	
	/**
	 * Account key for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountKey = '';
	
	/**
	 * Use path-style URI's
	 *
	 * @var boolean
	 */
	protected $_usePathStyleUri = false;
	
	/**
	 * Creates a new AEUtilAzureCredentials instance
	 *
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 */
	public function __construct($accountName = AEUtilAzureCredentials::DEVSTORE_ACCOUNT, $accountKey = AEUtilAzureCredentials::DEVSTORE_KEY, $usePathStyleUri = false)
	{
		$this->_accountName = $accountName;
		$this->_accountKey = base64_decode($accountKey);
		$this->_usePathStyleUri = $usePathStyleUri;
	}
	
	/**
	 * Set account name for Windows Azure
	 *
	 * @param string $value
	 */
	public function setAccountName($value = AEUtilAzureCredentials::DEVSTORE_ACCOUNT)
	{
		$this->_accountName = $value;
	}
	
	/**
	 * Set account key for Windows Azure
	 *
	 * @param string $value
	 */
	public function setAccountkey($value = AEUtilAzureCredentials::DEVSTORE_KEY)
	{
		$this->_accountKey = base64_decode($value);
	}
	
	/**
	 * Set use path-style URI's
	 *
	 * @param boolean $value
	 */
	public function setUsePathStyleUri($value = false)
	{
		$this->_usePathStyleUri = $value;
	}
	
	/**
	 * Sign request URL with credentials
	 *
	 * @param string $requestUrl Request URL
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return string Signed request URL
	 */
	public abstract function signRequestUrl($requestUrl = '');
	
	/**
	 * Sign request headers with credentials
	 *
	 * @param string $httpVerb HTTP verb the request will use
	 * @param string $path Path for the request
	 * @param string $queryString Query string for the request
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return array Array of headers
	 */
	public abstract function signRequestHeaders($httpVerb = AEUtilAzureHttpTransport::VERB_GET, $path = '/', $queryString = '', $headers = null, $forTableStorage = false, $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ);
	
	
	/**
	 * Prepare query string for signing
	 * 
	 * @param  string $value Original query string
	 * @return string        Query string for signing
	 */
	protected function prepareQueryStringForSigning($value)
	{
	    // Check for 'comp='
	    if (strpos($value, 'comp=') === false)
	    {
	        // If not found, no query string needed
	        return '';
	    }
	    else
	    {
	        // If found, make sure it is the only parameter being used      
    		if (strlen($value) > 0 && strpos($value, '?') === 0)
    			$value = substr($value, 1);
    		
    		// Split parts
    		$queryParts = explode('&', $value);
    		foreach ($queryParts as $queryPart)
    		{
    		    if (strpos($queryPart, 'comp=') !== false)
    		        return '?' . $queryPart;
    		}

    		// Should never happen...
			return '';
	    }
	}
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */ 
class AEUtilAzureSharedKeyCredentials extends AEUtilAzureCredentials
{
    /**
	 * Sign request URL with credentials
	 *
	 * @param string $requestUrl Request URL
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return string Signed request URL
	 */
	public function signRequestUrl($requestUrl = '', $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ)
	{
	    return $requestUrl;
	}
	
	/**
	 * Sign request headers with credentials
	 *
	 * @param string $httpVerb HTTP verb the request will use
	 * @param string $path Path for the request
	 * @param string $queryString Query string for the request
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return array Array of headers
	 */
	public function signRequestHeaders($httpVerb = AEUtilAzureHttpTransport::VERB_GET, $path = '/', $queryString = '', $headers = null, $forTableStorage = false, $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ)
	{
		// http://github.com/sriramk/winazurestorage/blob/214010a2f8931bac9c96dfeb337d56fe084ca63b/winazurestorage.py

		// Determine path
		if ($this->_usePathStyleUri)
			$path = substr($path, strpos($path, '/'));

		// Determine query
		$queryString = $this->prepareQueryStringForSigning($queryString);
	
		// Canonicalized headers
		$canonicalizedHeaders = array();
		
		// Request date
		$requestDate = '';
		if (isset($headers[self::PREFIX_STORAGE_HEADER . 'date']))
		{
		    $requestDate = $headers[self::PREFIX_STORAGE_HEADER . 'date'];
		}
		else 
		{
		    $requestDate = gmdate('D, d M Y H:i:s', time()) . ' GMT'; // RFC 1123
		    $canonicalizedHeaders[] = self::PREFIX_STORAGE_HEADER . 'date:' . $requestDate;
		}
		
		// Build canonicalized headers
		if (!is_null($headers))
		{
			foreach ($headers as $header => $value) {
				if (is_bool($value))
					$value = $value === true ? 'True' : 'False';

				$headers[$header] = $value;
				if (substr($header, 0, strlen(self::PREFIX_STORAGE_HEADER)) == self::PREFIX_STORAGE_HEADER)
				    $canonicalizedHeaders[] = strtolower($header) . ':' . $value;
			}
		}
		sort($canonicalizedHeaders);

		// Build canonicalized resource string
		$canonicalizedResource  = '/' . $this->_accountName;
		if ($this->_usePathStyleUri)
			$canonicalizedResource .= '/' . $this->_accountName;
		$canonicalizedResource .= $path;
		if ($queryString !== '')
		    $canonicalizedResource .= $queryString;

		// Create string to sign   
		$stringToSign = array();
		$stringToSign[] = strtoupper($httpVerb); 	// VERB
    	$stringToSign[] = "";						// Content-MD5
    	$stringToSign[] = "";						// Content-Type
    	$stringToSign[] = "";
        // Date already in $canonicalizedHeaders
    	// $stringToSign[] = self::PREFIX_STORAGE_HEADER . 'date:' . $requestDate; // Date
    	
    	if (!$forTableStorage && count($canonicalizedHeaders) > 0)
    		$stringToSign[] = implode("\n", $canonicalizedHeaders); // Canonicalized headers
    		
    	$stringToSign[] = $canonicalizedResource;		 			// Canonicalized resource
    	$stringToSign = implode("\n", $stringToSign);
    	$signString = base64_encode(hash_hmac('sha256', $stringToSign, $this->_accountKey, true));

    	// Sign request
    	$headers[self::PREFIX_STORAGE_HEADER . 'date'] = $requestDate;
    	$headers['Authorization'] = 'SharedKey ' . $this->_accountName . ':' . $signString;
    	
    	// Return headers
    	return $headers;
	}
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */ 
class AEUtilAzureSharedSignatureCredentials extends AEUtilAzureCredentials
{
    /**
     * Permission set
     * 
     * @var array
     */
    protected $_permissionSet = array();
    
	/**
	 * Creates a new AEUtilAzureSharedSignatureCredentials instance
	 *
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 * @param array $permissionSet Permission set
	 */
	public function __construct($accountName = AEUtilAzureCredentials::DEVSTORE_ACCOUNT, $accountKey = AEUtilAzureCredentials::DEVSTORE_KEY, $usePathStyleUri = false, $permissionSet = array())
	{
	    parent::__construct($accountName, $accountKey, $usePathStyleUri);
	    $this->_permissionSet = $permissionSet;
	}
	
	/**
	 * Get permission set
	 * 
	 * @return array
	 */
    public function getPermissionSet()
	{
	    return $this->_permissionSet;   
	}
	
	/**
	 * Set permisison set
	 * 
	 * Warning: fine-grained permissions should be added prior to coarse-grained permissions.
	 * For example: first add blob permissions, end with container-wide permissions.
	 * 
	 * Warning: the signed access signature URL must match the account name of the
	 * AEUtilAzureSharedSignatureCredentials instance
	 * 
	 * @param array $value Permission set
	 */
    public function setPermissionSet($value = array())
	{
		foreach ($value as $url)
		{
			if (strpos($url, $this->_accountName) === false)
				throw new AEUtilAzureAPIException('The permission set can only contain URLs for the account name specified in the AEUtilAzureSharedSignatureCredentials instance.');
		}
	    $this->_permissionSet = $value;
	}
    
    /**
     * Create signature
     * 
     * @param string $path 		   Path for the request
     * @param string $resource     Signed resource - container (c) - blob (b)
     * @param string $permissions  Signed permissions - read (r), write (w), delete (d) and list (l)
     * @param string $start        The time at which the Shared Access Signature becomes valid.
     * @param string $expiry       The time at which the Shared Access Signature becomes invalid.
     * @param string $identifier   Signed identifier
     * @return string 
     */
    public function createSignature($path = '/', $resource = 'b', $permissions = 'r', $start = '', $expiry = '', $identifier = '')
    {
		// Determine path
		if ($this->_usePathStyleUri)
			$path = substr($path, strpos($path, '/'));
			
		// Add trailing slash to $path
		if (substr($path, 0, 1) !== '/')
		    $path = '/' . $path;

		// Build canonicalized resource string
		$canonicalizedResource  = '/' . $this->_accountName;
		if ($this->_usePathStyleUri)
			$canonicalizedResource .= '/' . $this->_accountName;
		$canonicalizedResource .= $path;
		    
		// Create string to sign   
		$stringToSign = array();
		$stringToSign[] = $permissions;
    	$stringToSign[] = $start;
    	$stringToSign[] = $expiry;
    	$stringToSign[] = $canonicalizedResource;
    	$stringToSign[] = $identifier;

    	$stringToSign = implode("\n", $stringToSign);
    	$signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->_accountKey, true));

    	return $signature;
    }

    /**
     * Create signed query string
     * 
     * @param string $path 		   Path for the request
     * @param string $queryString  Query string for the request
     * @param string $resource     Signed resource - container (c) - blob (b)
     * @param string $permissions  Signed permissions - read (r), write (w), delete (d) and list (l)
     * @param string $start        The time at which the Shared Access Signature becomes valid.
     * @param string $expiry       The time at which the Shared Access Signature becomes invalid.
     * @param string $identifier   Signed identifier
     * @return string 
     */
    public function createSignedQueryString($path = '/', $queryString = '', $resource = 'b', $permissions = 'r', $start = '', $expiry = '', $identifier = '')
    {
        // Parts
        $parts = array();
        if ($start !== '')
            $parts[] = 'st=' . urlencode($start);
        $parts[] = 'se=' . urlencode($expiry);
        $parts[] = 'sr=' . $resource;
        $parts[] = 'sp=' . $permissions;
        if ($identifier !== '')
            $parts[] = 'si=' . urlencode($identifier);
        $parts[] = 'sig=' . urlencode($this->createSignature($path, $resource, $permissions, $start, $expiry, $identifier));

        // Assemble parts and query string
        if ($queryString != '')
            $queryString .= '&';
        $queryString .= implode('&', $parts);

        return $queryString;
    }
    
    /**
	 * Permission matches request?
	 *
	 * @param string $permissionUrl Permission URL
	 * @param string $requestUrl Request URL
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return string Signed request URL
	 */
    public function permissionMatchesRequest($permissionUrl = '', $requestUrl = '', $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ)
    {
        // Build requirements
        $requiredResourceType = $resourceType;
        if ($requiredResourceType == AEUtilAzureStorage::RESOURCE_BLOB)
            $requiredResourceType .= AEUtilAzureStorage::RESOURCE_CONTAINER;

        // Parse permission url
	    $parsedPermissionUrl = parse_url($permissionUrl);
	    
	    // Parse permission properties
	    $permissionParts = explode('&', $parsedPermissionUrl['query']);
	    
	    // Parse request url
	    $parsedRequestUrl = parse_url($requestUrl);
	    
	    // Check if permission matches request
	    $matches = true;
	    foreach ($permissionParts as $part)
	    {
	        list($property, $value) = explode('=', $part, 2);
	        if ($property == 'sr')
	        {
	            $matches = $matches && (strpbrk($value, $requiredResourceType) !== false);
	        }
	    	if ($property == 'sp')
	        {
	            $matches = $matches && (strpbrk($value, $requiredPermission) !== false);
	        }
	    }
	    
	    // Ok, but... does the resource match?
	    $matches = $matches && (strpos($parsedRequestUrl['path'], $parsedPermissionUrl['path']) !== false);
	    
        // Return
	    return $matches;
    }    
    
    /**
	 * Sign request URL with credentials
	 *
	 * @param string $requestUrl Request URL
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return string Signed request URL
	 */
	public function signRequestUrl($requestUrl = '', $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ)
	{
	    // Look for a matching permission
	    foreach ($this->getPermissionSet() as $permittedUrl)
	    {
	        if ($this->permissionMatchesRequest($permittedUrl, $requestUrl, $resourceType, $requiredPermission))
	        {
	            // This matches, append signature data
	            $parsedPermittedUrl = parse_url($permittedUrl);

	            if (strpos($requestUrl, '?') === false)
	                $requestUrl .= '?';
	            else
	                $requestUrl .= '&';
	            
	            $requestUrl .= $parsedPermittedUrl['query'];

	            // Return url
	            return $requestUrl;
	        }
	    }
	    
	    // Return url, will be unsigned...
	    return $requestUrl;
	}
    
	/**
	 * Sign request with credentials
	 *
	 * @param string $httpVerb HTTP verb the request will use
	 * @param string $path Path for the request
	 * @param string $queryString Query string for the request
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return array Array of headers
	 */
	public function signRequestHeaders($httpVerb = AEUtilAzureHttpTransport::VERB_GET, $path = '/', $queryString = '', $headers = null, $forTableStorage = false, $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ)
	{
	    return $headers;
	}
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage RetryPolicy
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
abstract class AEUtilAzureRetryPolicy
{
    /**
     * Execute function under retry policy
     * 
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @return mixed
     */
    public abstract function execute($function, $parameters = array());
    
    /**
     * Create a AEUtilAzureNoRetryPolicy instance
     * 
     * @return AEUtilAzureNoRetryPolicy
     */
    public static function noRetry()
    {
        return new AEUtilAzureNoRetryPolicy();
    }
    
    /**
     * Create a AEUtilAzureRetryNPolicy instance
     * 
     * @param int $count                    Number of retries
     * @param int $intervalBetweenRetries   Interval between retries (in milliseconds)
     * @return AEUtilAzureRetryNPolicy
     */
    public static function retryN($count = 1, $intervalBetweenRetries = 0)
    {
        return new AEUtilAzureRetryNPolicy($count, $intervalBetweenRetries);
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage RetryPolicy
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class AEUtilAzureNoRetryPolicy extends AEUtilAzureRetryPolicy
{
    /**
     * Execute function under retry policy
     * 
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @return mixed
     */
    public function execute($function, $parameters = array())
    {
        $returnValue = null;
        
        try
        {
            $returnValue = call_user_func_array($function, $parameters);
            return $returnValue;
        }
        catch (Exception $ex)
        {
            throw $ex;
        }
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage RetryPolicy
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class AEUtilAzureRetryNPolicy extends AEUtilAzureRetryPolicy
{
    /**
     * Number of retries
     * 
     * @var int
     */
    protected $_retryCount = 1;
    
    /**
     * Interval between retries (in milliseconds)
     * 
     * @var int
     */
    protected $_retryInterval = 0;
    
    /**
     * Constructor
     * 
     * @param int $count                    Number of retries
     * @param int $intervalBetweenRetries   Interval between retries (in milliseconds)
     */
    public function __construct($count = 1, $intervalBetweenRetries = 0)
    {
        $this->_retryCount = $count;
        $this->_retryInterval = $intervalBetweenRetries;
    }
    
    /**
     * Execute function under retry policy
     * 
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @return mixed
     */
    public function execute($function, $parameters = array())
    {
        $returnValue = null;
        
        for ($retriesLeft = $this->_retryCount; $retriesLeft >= 0; --$retriesLeft)
        {
            try
            {
                $returnValue = call_user_func_array($function, $parameters);
                return $returnValue;
            }
            catch (Exception $ex)
            {
                if ($retriesLeft == 1)
                    throw new AEUtilAzureRetryPolicyException("Exceeded retry count of " . $this->_retryCount . ". " . $ex->getMessage());
                    
                usleep($this->_retryInterval * 1000);
            }
        }
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class AEUtilAzureStorage
{
	/**
	 * Development storage URLS
	 */
	const URL_DEV_BLOB      = "127.0.0.1:10000";
	const URL_DEV_QUEUE     = "127.0.0.1:10001";
	const URL_DEV_TABLE     = "127.0.0.1:10002";
	
	/**
	 * Live storage URLS
	 */
	const URL_CLOUD_BLOB    = "blob.core.windows.net";
	const URL_CLOUD_QUEUE   = "queue.core.windows.net";
	const URL_CLOUD_TABLE   = "table.core.windows.net";
	
	/**
	 * Resource types
	 */
	const RESOURCE_UNKNOWN     = "unknown";
	const RESOURCE_CONTAINER   = "c";
	const RESOURCE_BLOB        = "b";
	const RESOURCE_TABLE       = "t";
	const RESOURCE_ENTITY      = "e";
	const RESOURCE_QUEUE       = "q";
	
	/**
	 * Current API version
	 * 
	 * @var string
	 */
	protected $_apiVersion = '2009-04-14';
	
	/**
	 * Storage host name
	 *
	 * @var string
	 */
	protected $_host = '';
	
	/**
	 * Account name for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountName = '';
	
	/**
	 * Account key for Windows Azure
	 *
	 * @var string
	 */
	protected $_accountKey = '';
	
	/**
	 * Use path-style URI's
	 *
	 * @var boolean
	 */
	protected $_usePathStyleUri = false;
	
	/**
	 * AEUtilAzureCredentials instance
	 *
	 * @var AEUtilAzureCredentials
	 */
	protected $_credentials = null;
	
	/**
	 * AEUtilAzureRetryPolicy instance
	 * 
	 * @var AEUtilAzureRetryPolicy
	 */
	protected $_retryPolicy = null;
	
	/**
	 * Use proxy?
	 * 
	 * @var boolean
	 */
	protected $_useProxy = false;
	
	/**
	 * Proxy url
	 * 
	 * @var string
	 */
	protected $_proxyUrl = '';
	
	/**
	 * Proxy port
	 * 
	 * @var int
	 */
	protected $_proxyPort = 80;
	
	/**
	 * Proxy credentials
	 * 
	 * @var string
	 */
	protected $_proxyCredentials = '';
	
	/**
	 * Creates a new AEUtilAzureStorage instance
	 *
	 * @param string $host Storage host name
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 * @param AEUtilAzureRetryPolicy $retryPolicy Retry policy to use when making requests
	 */
	public function __construct($host = self::URL_DEV_BLOB, $accountName = AEUtilAzureCredentials::DEVSTORE_ACCOUNT, $accountKey = AEUtilAzureCredentials::DEVSTORE_KEY, $usePathStyleUri = false, AEUtilAzureRetryPolicy $retryPolicy = null)
	{
		$this->_host = $host;
		$this->_accountName = $accountName;
		$this->_accountKey = $accountKey;
		$this->_usePathStyleUri = $usePathStyleUri;
		
		// Using local storage?
		if (!$this->_usePathStyleUri && ($this->_host == self::URL_DEV_BLOB || $this->_host == self::URL_DEV_QUEUE || $this->_host == self::URL_DEV_TABLE)) // Local storage
			$this->_usePathStyleUri = true;
		
		if (is_null($this->_credentials))
		    $this->_credentials = new AEUtilAzureSharedKeyCredentials($this->_accountName, $this->_accountKey, $this->_usePathStyleUri);
		
		$this->_retryPolicy = $retryPolicy;
		if (is_null($this->_retryPolicy))
		    $this->_retryPolicy = AEUtilAzureRetryPolicy::noRetry();
	}
	
	/**
	 * Set retry policy to use when making requests
	 *
	 * @param AEUtilAzureRetryPolicy $retryPolicy Retry policy to use when making requests
	 */
	public function setRetryPolicy(AEUtilAzureRetryPolicy $retryPolicy = null)
	{
		$this->_retryPolicy = $retryPolicy;
		if (is_null($this->_retryPolicy))
		    $this->_retryPolicy = AEUtilAzureRetryPolicy::noRetry();
	}
	
	/**
	 * Set proxy
	 * 
	 * @param boolean $useProxy         Use proxy?
	 * @param string  $proxyUrl         Proxy URL
	 * @param int     $proxyPort        Proxy port
	 * @param string  $proxyCredentials Proxy credentials
	 */
	public function setProxy($useProxy = false, $proxyUrl = '', $proxyPort = 80, $proxyCredentials = '')
	{
	    $this->_useProxy = $useProxy;
	    $this->_proxyUrl = $proxyUrl;
	    $this->_proxyPort = $proxyPort;
	    $this->_proxyCredentials = $proxyCredentials;
	}
	
	/**
	 * Returns the Windows Azure account name
	 * 
	 * @return string
	 */
	public function getAccountName()
	{
		return $this->_accountName;
	}
	
	/**
	 * Get base URL for creating requests
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		if ($this->_usePathStyleUri)
			return 'http://' . $this->_host . '/' . $this->_accountName;
		else
			return 'http://' . $this->_accountName . '.' . $this->_host;
	}
	
	/**
	 * Set AEUtilAzureCredentials instance
	 * 
	 * @param AEUtilAzureCredentials $credentials AEUtilAzureCredentials instance to use for request signing.
	 */
	public function setCredentials(AEUtilAzureCredentials $credentials)
	{
	    $this->_credentials = $credentials;
	    $this->_credentials->setAccountName($this->_accountName);
	    $this->_credentials->setAccountkey($this->_accountKey);
	    $this->_credentials->setUsePathStyleUri($this->_usePathStyleUri);
	}
	
	/**
	 * Get AEUtilAzureCredentials instance
	 * 
	 * @return AEUtilAzureCredentials
	 */
	public function getCredentials()
	{
	    return $this->_credentials;
	}
	
	/**
	 * Perform request using AEUtilAzureHttpTransport channel
	 *
	 * @param string $path Path
	 * @param string $queryString Query string
	 * @param string $httpVerb HTTP verb the request will use
	 * @param array $headers x-ms headers to add
	 * @param boolean $forTableStorage Is the request for table storage?
	 * @param mixed $rawData Optional RAW HTTP data to be sent over the wire
	 * @param string $resourceType Resource type
	 * @param string $requiredPermission Required permission
	 * @return AEUtilAzureHttpResponse
	 */
	protected function performRequest($path = '/', $queryString = '', $httpVerb = AEUtilAzureHttpTransport::VERB_GET, $headers = array(), $forTableStorage = false, $rawData = null, $resourceType = AEUtilAzureStorage::RESOURCE_UNKNOWN, $requiredPermission = AEUtilAzureCredentials::PERMISSION_READ)
	{
	    // Clean path
		if (strpos($path, '/') !== 0) 
			$path = '/' . $path;
			
		// Clean headers
		if (is_null($headers))
		    $headers = array();
		    
		// Add version header
		$headers['x-ms-version'] = $this->_apiVersion;
		    
		// URL encoding
		$path           = self::urlencode($path);
		$queryString    = self::urlencode($queryString);

		// Generate URL and sign request
		$requestUrl     = $this->_credentials->signRequestUrl($this->getBaseUrl() . $path . $queryString, $resourceType, $requiredPermission);
		$requestHeaders = $this->_credentials->signRequestHeaders($httpVerb, $path, $queryString, $headers, $forTableStorage, $resourceType, $requiredPermission);

		$requestClient  = AEUtilAzureHttpTransport::createChannel();
		if ($this->_useProxy)
		{
		    $requestClient->setProxy($this->_useProxy, $this->_proxyUrl, $this->_proxyPort, $this->_proxyCredentials);
		}
		$response = $this->_retryPolicy->execute(
		    array($requestClient, 'request'),
		    array($httpVerb, $requestUrl, array(), $requestHeaders, $rawData)
		);
		
		$requestClient = null;
		unset($requestClient);
		
		return $response;
	}
	
    /**
     * Builds a query string from an array of elements
     * 
     * @param array     Array of elements
     * @return string   Assembled query string
     */
    public static function createQueryStringFromArray($queryString)
    {
    	return count($queryString) > 0 ? '?' . implode('&', $queryString) : '';
    }
	
	/** 
	 * Parse result from AEUtilAzureHttpResponse
	 *
	 * @param AEUtilAzureHttpResponse $response Response from HTTP call
	 * @return object
	 * @throws AEUtilAzureAPIException
	 */
	protected function parseResponse(AEUtilAzureHttpResponse $response = null)
	{
		if (is_null($response))
			throw new AEUtilAzureAPIException('Response should not be null.');
		
        $xml = @simplexml_load_string($response->getBody());
        
        if ($xml !== false)
        {
            // Fetch all namespaces 
            $namespaces = array_merge($xml->getNamespaces(true), $xml->getDocNamespaces(true)); 
            
            // Register all namespace prefixes
            foreach ($namespaces as $prefix => $ns) { 
                if ($prefix != '')
                    $xml->registerXPathNamespace($prefix, $ns); 
            } 
        }
        
        return $xml;
	}
	
	/**
	 * Generate ISO 8601 compliant date string in UTC time zone
	 * 
	 * @param int $timestamp
	 * @return string
	 */
	public function isoDate($timestamp = null) 
	{        
	    $tz = @date_default_timezone_get();
	    @date_default_timezone_set('UTC');
	    
	    if (is_null($timestamp))
	        $timestamp = time();
	        
	    $returnValue = str_replace('+00:00', '.0000000Z', @date('c', $timestamp));
	    @date_default_timezone_set($tz);
	    return $returnValue;
	}
	
	/**
	 * URL encode function
	 * 
	 * @param  string $value Value to encode
	 * @return string        Encoded value
	 */
	public static function urlencode($value)
	{
	    return str_replace(' ', '%20', $value);
	}
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @property string $Name          Name of the container
 * @property string $Etag          Etag of the container
 * @property string $LastModified  Last modified date of the container
 * @property array  $Metadata      Key/value pairs of meta data
 */
class AEUtilAzureBlobContainer
{
    /**
     * Data
     * 
     * @var array
     */
    protected $_data = null;
    
    /**
     * Constructor
     * 
     * @param string $name          Name
     * @param string $etag          Etag
     * @param string $lastModified  Last modified date
     * @param array  $metadata      Key/value pairs of meta data
     */
    public function __construct($name, $etag, $lastModified, $metadata = array()) 
    {
        $this->_data = array(
            'name'         => $name,
            'etag'         => $etag,
            'lastmodified' => $lastModified,
            'metadata'     => $metadata
        );
    }
    
    /**
     * Magic overload for setting properties
     * 
     * @param string $name     Name of the property
     * @param string $value    Value to set
     */
    public function __set($name, $value) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new Exception("Unknown property: " . $name);
    }

    /**
     * Magic overload for getting properties
     * 
     * @param string $name     Name of the property
     */
    public function __get($name) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new Exception("Unknown property: " . $name);
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @property string  $Container       Container name
 * @property string  $Name            Name
 * @property string  $Etag            Etag
 * @property string  $LastModified    Last modified date
 * @property string  $Url             Url
 * @property int     $Size            Size
 * @property string  $ContentType     Content Type
 * @property string  $ContentEncoding Content Encoding
 * @property string  $ContentLanguage Content Language
 * @property boolean $IsPrefix        Is Prefix?
 * @property array   $Metadata        Key/value pairs of meta data
 */
class AEUtilAzureBlobInstance
{
    /**
     * Data
     * 
     * @var array
     */
    protected $_data = null;
    
    /**
     * Constructor
     * 
     * @param string  $containerName   Container name
     * @param string  $name            Name
     * @param string  $etag            Etag
     * @param string  $lastModified    Last modified date
     * @param string  $url             Url
     * @param int     $size            Size
     * @param string  $contentType     Content Type
     * @param string  $contentEncoding Content Encoding
     * @param string  $contentLanguage Content Language
     * @param boolean $isPrefix        Is Prefix?
     * @param array   $metadata        Key/value pairs of meta data
     */
    public function __construct($containerName, $name, $etag, $lastModified, $url = '', $size = 0, $contentType = '', $contentEncoding = '', $contentLanguage = '', $isPrefix = false, $metadata = array()) 
    {	        
        $this->_data = array(
            'container'        => $containerName,
            'name'             => $name,
            'etag'             => $etag,
            'lastmodified'     => $lastModified,
            'url'              => $url,
            'size'             => $size,
            'contenttype'      => $contentType,
            'contentencoding'  => $contentEncoding,
            'contentlanguage'  => $contentLanguage,
            'isprefix'         => $isPrefix,
            'metadata'         => $metadata
        );
    }
    
    /**
     * Magic overload for setting properties
     * 
     * @param string $name     Name of the property
     * @param string $value    Value to set
     */
    public function __set($name, $value) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new Exception("Unknown property: " . $name);
    }

    /**
     * Magic overload for getting properties
     * 
     * @param string $name     Name of the property
     */
    public function __get($name) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new Exception("Unknown property: " . $name);
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @property string $Id           Id for the signed identifier
 * @property string $Start        The time at which the Shared Access Signature becomes valid.
 * @property string $Expiry       The time at which the Shared Access Signature becomes invalid.
 * @property string $Permissions  Signed permissions - read (r), write (w), delete (d) and list (l)
 */
class AEUtilAzureSignedIdentifier
{
    /**
     * Data
     * 
     * @var array
     */
    protected $_data = null;
    
    /**
     * Constructor
     * 
     * @param string $id           Id for the signed identifier
     * @param string $start        The time at which the Shared Access Signature becomes valid.
     * @param string $expiry       The time at which the Shared Access Signature becomes invalid.
     * @param string $permissions  Signed permissions - read (r), write (w), delete (d) and list (l)
     */
    public function __construct($id = '', $start = '', $expiry = '', $permissions = '') 
    {
        $this->_data = array(
            'id'           => $id,
            'start'        => $start,
            'expiry'       => $expiry,
            'permissions'  => $permissions
        );
    }
    
    /**
     * Magic overload for setting properties
     * 
     * @param string $name     Name of the property
     * @param string $value    Value to set
     */
    public function __set($name, $value) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new Exception("Unknown property: " . $name);
    }

    /**
     * Magic overload for getting properties
     * 
     * @param string $name     Name of the property
     */
    public function __get($name) {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new Exception("Unknown property: " . $name);
    }
}

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class AEUtilAzure extends AEUtilAzureStorage
{
	/**
	 * ACL - Private access
	 */
	const ACL_PRIVATE = false;
	
	/**
	 * ACL - Public access
	 */
	const ACL_PUBLIC = true;
	
	/**
	 * Maximal blob size (in bytes)
	 */
	const MAX_BLOB_SIZE = 67108864;

	/**
	 * Maximal blob transfer size (in bytes)
	 */
	const MAX_BLOB_TRANSFER_SIZE = 4194304;
	
    /**
     * Stream wrapper clients
     *
     * @var array
     */
    protected static $_wrapperClients = array();
    
    /**
     * SharedAccessSignature credentials
     * 
     * @var AEUtilAzureSharedSignatureCredentials
     */
    private $_sharedAccessSignatureCredentials = null;
	
	/**
	 * Creates a new AEUtilAzure instance
	 *
	 * @param string $host Storage host name
	 * @param string $accountName Account name for Windows Azure
	 * @param string $accountKey Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 * @param AEUtilAzureRetryPolicy $retryPolicy Retry policy to use when making requests
	 */
	public function __construct($host = AEUtilAzureStorage::URL_DEV_BLOB, $accountName = AEUtilAzureSharedKeyCredentials::DEVSTORE_ACCOUNT, $accountKey = AEUtilAzureSharedKeyCredentials::DEVSTORE_KEY, $usePathStyleUri = false, AEUtilAzureRetryPolicy $retryPolicy = null)
	{
		parent::__construct($host, $accountName, $accountKey, $usePathStyleUri, $retryPolicy);
		
		// API version
		$this->_apiVersion = '2009-07-17';
		
		// SharedAccessSignature credentials
		$this->_sharedAccessSignatureCredentials = new AEUtilAzureSharedSignatureCredentials($accountName, $accountKey, $usePathStyleUri);
	}
	
	/**
	 * Get container
	 * 
	 * @param string $containerName  Container name
	 * @return AEUtilAzureBlobContainer
	 * @throws AEUtilAzureAPIException
	 */
	public function getContainer($containerName = '')
	{
		if ($containerName === '')
			throw new AEUtilAzureAPIException('Container name is not specified.');
		if (!self::isValidContainerName($containerName))
		    throw new AEUtilAzureAPIException('Container name does not adhere to container naming conventions. See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
		    
		// Perform request
		$response = $this->performRequest($containerName, '?restype=container', AEUtilAzureHttpTransport::VERB_GET, array(), false, null, AEUtilAzureStorage::RESOURCE_CONTAINER, AEUtilAzureCredentials::PERMISSION_READ);
		if ($response->isSuccessful())
		{
		    // Parse metadata
		    $metadata = array();
		    foreach ($response->getHeaders() as $key => $value)
		    {
		        if (substr(strtolower($key), 0, 10) == "x-ms-meta-")
		        {
		            $metadata[str_replace("x-ms-meta-", '', strtolower($key))] = $value;
		        }
		    }

		    // Return container
		    return new AEUtilAzureBlobContainer(
		        $containerName,
		        $response->getHeader('Etag'),
		        $response->getHeader('Last-modified'),
		        $metadata
		    );
		}
		else
		{
		    throw new AEUtilAzureAPIException($this->getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}
	
	/**
	 * Put blob
	 *
	 * @param string $containerName      Container name
	 * @param string $blobName           Blob name
	 * @param string $localFileName      Local file name to be uploaded
	 * @param array  $metadata           Key/value pairs of meta data
	 * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
	 * @return object Partial blob properties
	 * @throws AEUtilAzureAPIException
	 */
	public function putBlob($containerName = '', $blobName = '', $localFileName = '', $metadata = array(), $additionalHeaders = array())
	{
		if ($containerName === '')
			throw new AEUtilAzureAPIException('Container name is not specified.');
		if (!self::isValidContainerName($containerName))
		    throw new AEUtilAzureAPIException('Container name does not adhere to container naming conventions. See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
		if ($blobName === '')
			throw new AEUtilAzureAPIException('Blob name is not specified.');
		if ($localFileName === '')
			throw new AEUtilAzureAPIException('Local file name is not specified.');
		if (!file_exists($localFileName))
			throw new AEUtilAzureAPIException('Local file not found.');
		if ($containerName === '$root' && strpos($blobName, '/') !== false)
		    throw new AEUtilAzureAPIException('Blobs stored in the root container can not have a name containing a forward slash (/).');
			
		// Check file size
		if (filesize($localFileName) >= self::MAX_BLOB_SIZE)
		{
			throw new AEUtilAzureAPIException('The maximum part size for Windows Azure is 64Mb. Please set the Part Size for Archive Splitting to 64Mb or lower and retry backup.');
		}

		// Create metadata headers
		$headers = array();
		foreach ($metadata as $key => $value)
		{
		    $headers["x-ms-meta-" . strtolower($key)] = $value;
		}
		
		// Additional headers?
		foreach ($additionalHeaders as $key => $value)
		{
		    $headers[$key] = $value;
		}
		
		// File contents
		$fileContents = file_get_contents($localFileName);
		
		// Resource name
		$resourceName = self::createResourceName($containerName , $blobName);
		
		// Perform request
		$response = $this->performRequest($resourceName, '', AEUtilAzureHttpTransport::VERB_PUT, $headers, false, $fileContents, AEUtilAzureStorage::RESOURCE_BLOB, AEUtilAzureCredentials::PERMISSION_WRITE);
		if ($response->isSuccessful())
		{
			return new AEUtilAzureBlobInstance(
				$containerName,
				$blobName,
				$response->getHeader('Etag'),
				$response->getHeader('Last-modified'),
				$this->getBaseUrl() . '/' . $containerName . '/' . $blobName,
				strlen($fileContents),
				'',
				'',
				'',
				false,
		        $metadata
			);
		}
		else
		{
		    throw new AEUtilAzureAPIException($this->getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}					
	
	/**
	 * Set blob metadata
	 * 
	 * Calling the Set Blob Metadata operation overwrites all existing metadata that is associated with the blob. It's not possible to modify an individual name/value pair.
	 *
	 * @param string $containerName      Container name
	 * @param string $blobName           Blob name
	 * @param array  $metadata           Key/value pairs of meta data
	 * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
	 * @throws AEUtilAzureAPIException
	 */
	public function setBlobMetadata($containerName = '', $blobName = '', $metadata = array(), $additionalHeaders = array())
	{
		if ($containerName === '')
			throw new AEUtilAzureAPIException('Container name is not specified.');
		if (!self::isValidContainerName($containerName))
		    throw new AEUtilAzureAPIException('Container name does not adhere to container naming conventions. See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
		if ($blobName === '')
			throw new AEUtilAzureAPIException('Blob name is not specified.');
		if ($containerName === '$root' && strpos($blobName, '/') !== false)
		    throw new AEUtilAzureAPIException('Blobs stored in the root container can not have a name containing a forward slash (/).');
		if (count($metadata) == 0)
		    return;
		    
		// Create metadata headers
		$headers = array();
		foreach ($metadata as $key => $value)
		{
		    $headers["x-ms-meta-" . strtolower($key)] = $value;
		}
		
		// Additional headers?
		foreach ($additionalHeaders as $key => $value)
		{
		    $headers[$key] = $value;
		}
		
		// Perform request
		$response = $this->performRequest($containerName . '/' . $blobName, '?comp=metadata', AEUtilAzureHttpTransport::VERB_PUT, $headers, false, null, AEUtilAzureStorage::RESOURCE_BLOB, AEUtilAzureCredentials::PERMISSION_WRITE);
		if (!$response->isSuccessful())
		{
		    throw new AEUtilAzureAPIException($this->getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}
	
	/**
	 * Get blob
	 *
	 * @param string $containerName      Container name
	 * @param string $blobName           Blob name
	 * @param string $localFileName      Local file name to store downloaded blob
	 * @param string $snapshotId         Snapshot identifier
	 * @param string $leaseId            Lease identifier
	 * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
	 * @throws AEUtilAzureAPIException
	 */
	public function getBlob($containerName = '', $blobName = '', $localFileName = '', $snapshotId = null, $leaseId = null, $additionalHeaders = array())
	{
		if ($containerName === '') {
			throw new AEUtilAzureAPIException('Container name is not specified.');
		}
		if (!self::isValidContainerName($containerName)) {
			throw new AEUtilAzureAPIException('Container name does not adhere to container naming conventions. See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
		}
		if ($blobName === '') {
			throw new AEUtilAzureAPIException('Blob name is not specified.');
		}
		if ($localFileName === '') {
			throw new AEUtilAzureAPIException('Local file name is not specified.');
		}

		// Fetch data
		file_put_contents($localFileName, $this->getBlobData($containerName, $blobName, $snapshotId, $leaseId, $additionalHeaders));
	}

	/**
	 * Get blob data
	 *
	 * @param string $containerName      Container name
	 * @param string $blobName           Blob name
	 * @param string $snapshotId         Snapshot identifier
	 * @param string $leaseId            Lease identifier
	 * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
	 * @return mixed Blob contents
	 * @throws AEUtilAzureAPIException
	 */
	public function getBlobData($containerName = '', $blobName = '', $snapshotId = null, $leaseId = null, $additionalHeaders = array())
	{
		if ($containerName === '') {
			throw new AEUtilAzureAPIException('Container name is not specified.');
		}
		if (!self::isValidContainerName($containerName)) {
			throw new AEUtilAzureAPIException('Container name does not adhere to container naming conventions. See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
		}
		if ($blobName === '') {
			throw new AEUtilAzureAPIException('Blob name is not specified.');
		}

		// Build query string
		$queryString = array();
		if (!is_null($snapshotId)) {
			$queryString[] = 'snapshot=' . $snapshotId;
		}
		$queryString = self::createQueryStringFromArray($queryString);

		// Additional headers?
		$headers = array();
		if (!is_null($leaseId)) {
			$headers['x-ms-lease-id'] = $leaseId;
		}
		foreach ($additionalHeaders as $key => $value) {
			$headers[$key] = $value;
		}

		// Resource name
		$resourceName = self::createResourceName($containerName , $blobName);

		// Perform request
		$response = $this->performRequest($resourceName, $queryString, 'GET', $headers, false, null, self::RESOURCE_BLOB, AEUtilAzureCredentials::PERMISSION_READ);
		if ($response->isSuccessful()) {
			return $response->getBody();
		} else {
			throw new AEUtilAzureAPIException($this->getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}
	
	/**
	 * Delete blob
	 *
	 * @param string $containerName      Container name
	 * @param string $blobName           Blob name
	 * @param string $snapshotId         Snapshot identifier
	 * @param string $leaseId            Lease identifier
	 * @param array  $additionalHeaders  Additional headers. See http://msdn.microsoft.com/en-us/library/dd179371.aspx for more information.
	 * @throws AEUtilAzureAPIException
	 */
	public function deleteBlob($containerName = '', $blobName = '', $snapshotId = null, $leaseId = null, $additionalHeaders = array())
	{
		if ($containerName === '') {
			throw new AEUtilAzureAPIException('Container name is not specified.');
		}
		if (!self::isValidContainerName($containerName)) {
			throw new AEUtilAzureAPIException('Container name does not adhere to container naming conventions. See http://msdn.microsoft.com/en-us/library/dd135715.aspx for more information.');
		}
		if ($blobName === '') {
			throw new AEUtilAzureAPIException('Blob name is not specified.');
		}
		if ($containerName === '$root' && strpos($blobName, '/') !== false) {
			throw new AEUtilAzureAPIException('Blobs stored in the root container can not have a name containing a forward slash (/).');
		}

		// Build query string
		$queryString = array();
		if (!is_null($snapshotId)) {
			$queryString[] = 'snapshot=' . $snapshotId;
		}
		$queryString = self::createQueryStringFromArray($queryString);
			
		// Additional headers?
		$headers = array();
		if (!is_null($leaseId)) {
			$headers['x-ms-lease-id'] = $leaseId;
		}
		foreach ($additionalHeaders as $key => $value) {
			$headers[$key] = $value;
		}

		// Resource name
		$resourceName = self::createResourceName($containerName , $blobName);

		// Perform request
		$response = $this->performRequest($resourceName, $queryString, 'DELETE', $headers, false, null, self::RESOURCE_BLOB, AEUtilAzureCredentials::PERMISSION_WRITE);
		if (!$response->isSuccessful()) {
			throw new AEUtilAzureAPIException($this->getErrorMessage($response, 'Resource could not be accessed.'));
		}
	}
    
    /**
     * Create resource name
     * 
	 * @param string $containerName  Container name
	 * @param string $blobName Blob name
     * @return string
     */
    public static function createResourceName($containerName = '', $blobName = '')
    {
		// Resource name
		$resourceName = $containerName . '/' . $blobName;
		if ($containerName === '' || $containerName === '$root')
		    $resourceName = $blobName;
		if ($blobName === '')
		    $resourceName = $containerName;
		    
		return $resourceName;
    }
	
	/**
	 * Is valid container name?
	 *
	 * @param string $containerName Container name
	 * @return boolean
	 */
    public static function isValidContainerName($containerName = '')
    {
        if ($containerName == '$root')
            return true;
            
        if (!ereg("^[a-z0-9][a-z0-9-]*$", $containerName))
            return false;
    
        if (strpos($containerName, '--') !== false)
            return false;
    
        if (strtolower($containerName) != $containerName)
            return false;
    
        if (strlen($containerName) < 3 || strlen($containerName) > 63)
            return false;
            
        if (substr($containerName, -1) == '-')
            return false;
    
        return true;
    }
    
	/**
	 * Get error message from AEUtilAzureHttpResponse
	 * 
	 * @param AEUtilAzureHttpResponse $response Repsonse
	 * @param string $alternativeError Alternative error message
	 * @return string
	 */
	protected function getErrorMessage(AEUtilAzureHttpResponse $response, $alternativeError = 'Unknown error.')
	{
		$response = $this->parseResponse($response);
		if ($response && $response->Message)
		    return (string)$response->Message;
		else
		    return $alternativeError;
	}
	
	/**
	 * Generate block id
	 * 
	 * @param int $part Block number
	 * @return string Windows Azure Blob Storage block number
	 */
	protected function generateBlockId($part = 0)
	{
		$returnValue = $part;
		while (strlen($returnValue) < 64)
		{
			$returnValue = '0' . $returnValue;
		}
		
		return $returnValue;
	}
}
