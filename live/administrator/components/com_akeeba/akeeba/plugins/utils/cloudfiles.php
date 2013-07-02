<?php
/**
  Copyright (C) 2008 Rackspace US, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Except as contained in this notice, the name of Rackspace US, Inc. shall not
be used in advertising or otherwise to promote the sale, use or other dealings
in this Software without prior written authorization from Rackspace US, Inc.

 *
 * <code>
 *   $auth = new AEUtilCFAuthentication($username, $api_key);
 *   $auth->authenticate();
 *   $conn = new AEUtilCFConnection($auth);
 *   $images = $conn->create_container("photos");
 *   $bday = $images->create_object("first_birthday.jpg");
 *   $bday->load_from_filename("/home/user/photos/birthdays/birthday1.jpg");
 * </code>
 *
 * @author Eric "EJ" Johnson <ej@racklabs.com>
 * @copyright Copyright (c) 2008, Rackspace US, Inc.
 * @package php-cloudfiles
 */

class AEUtilCloudfiles {}

define("DEFAULT_CF_API_VERSION", 1);
define("MAX_CONTAINER_NAME_LEN", 256);
define("MAX_OBJECT_NAME_LEN", 1024);
define("MAX_OBJECT_SIZE", 5*1024*1024*1024+1); # bigger than S3! ;-)
define("PHP_CF_VERSION", "1.7.0");
define("USER_AGENT", sprintf("PHP-CloudFiles/%s", PHP_CF_VERSION));
define("ACCOUNT_CONTAINER_COUNT", "X-Account-Container-Count");
define("ACCOUNT_BYTES_USED", "X-Account-Bytes-Used");
define("CONTAINER_OBJ_COUNT", "X-Container-Object-Count");
define("CONTAINER_BYTES_USED", "X-Container-Bytes-Used");
define("METADATA_HEADER", "X-Object-Meta-");
define("CDN_URI", "X-CDN-URI");
define("CDN_ENABLED", "X-CDN-Enabled");
define("CDN_LOG_RETENTION", "X-Log-Retention");
define("CDN_ACL_USER_AGENT", "X-User-Agent-ACL");
define("CDN_ACL_REFERRER", "X-Referrer-ACL");
define("CDN_TTL", "X-TTL");
define("CDNM_URL", "X-CDN-Management-Url");
define("STORAGE_URL", "X-Storage-Url");
define("AUTH_TOKEN", "X-Auth-Token");
define("AUTH_USER_HEADER", "X-Auth-User");
define("AUTH_KEY_HEADER", "X-Auth-Key");
define("AUTH_USER_HEADER_LEGACY", "X-Storage-User");
define("AUTH_KEY_HEADER_LEGACY", "X-Storage-Pass");
define("AUTH_TOKEN_LEGACY", "X-Storage-Token");

class SyntaxException extends Exception { }
class AuthenticationException extends Exception { }
class InvalidResponseException extends Exception { }
class NonEmptyContainerException extends Exception { }
class NoSuchObjectException extends Exception { }
class NoSuchContainerException extends Exception { }
class NoSuchAccountException extends Exception { }
class MisMatchedChecksumException extends Exception { }
class IOException extends Exception { }
class CDNNotEnabledException extends Exception { }
class BadContentTypeException extends Exception { }
class InvalidUTF8Exception extends Exception { }
class ConnectionNotOpenException extends Exception { }


/**
 * HTTP/cURL wrapper for Cloud Files
 *
 * This class should not be used directly.  It's only purpose is to abstract
 * out the HTTP communication from the main API.
 *
 * @package php-cloudfiles-http
 */
class AEUtilCfhttp
{
    private $error_str;
    private $dbug;
    private $cabundle_path;
    private $api_version;

    # Authentication instance variables
    #
    private $storage_url;
    private $cdnm_url;
    private $auth_token;

    # Request/response variables
    #
    private $response_status;
    private $response_reason;
    private $connections;

    # Variables used for content/header callbacks
    #
    private $_user_read_progress_callback_func;
    private $_user_write_progress_callback_func;
    private $_write_callback_type;
    private $_text_list;
    private $_account_container_count;
    private $_account_bytes_used;
    private $_container_object_count;
    private $_container_bytes_used;
    private $_obj_etag;
    private $_obj_last_modified;
    private $_obj_content_type;
    private $_obj_content_length;
    private $_obj_metadata;
    private $_obj_write_resource;
    private $_obj_write_string;
    private $_cdn_enabled;
    private $_cdn_uri;
    private $_cdn_ttl;
    private $_cdn_log_retention;
    private $_cdn_acl_user_agent;
    private $_cdn_acl_referrer;
	private $isUKAccount = false;

    function __construct($api_version, $isUKAccount = false)
    {
        $this->dbug = False;
        $this->cabundle_path = NULL;
        $this->api_version = $api_version;
        $this->error_str = NULL;

        $this->storage_url = NULL;
        $this->cdnm_url = NULL;
        $this->auth_token = NULL;

        $this->response_status = NULL;
        $this->response_reason = NULL;

        # Curl connections array - since there is no way to "re-set" the
        # connection paramaters for a cURL handle, we keep an array of
        # the unique use-cases and funnel all of those same type
        # requests through the appropriate curl connection.
        #
        $this->connections = array(
            "GET_CALL"  => NULL, # GET objects/containers/lists
            "PUT_OBJ"   => NULL, # PUT object
            "HEAD"      => NULL, # HEAD requests
            "PUT_CONT"  => NULL, # PUT container
            "DEL_POST"  => NULL, # DELETE containers/objects, POST objects
        );

        $this->_user_read_progress_callback_func = NULL;
        $this->_user_write_progress_callback_func = NULL;
        $this->_write_callback_type = NULL;
        $this->_text_list = array();
		$this->_return_list = NULL;
        $this->_account_container_count = 0;
        $this->_account_bytes_used = 0;
        $this->_container_object_count = 0;
        $this->_container_bytes_used = 0;
        $this->_obj_write_resource = NULL;
        $this->_obj_write_string = "";
        $this->_obj_etag = NULL;
        $this->_obj_last_modified = NULL;
        $this->_obj_content_type = NULL;
        $this->_obj_content_length = NULL;
        $this->_obj_metadata = array();
        $this->_cdn_enabled = NULL;
        $this->_cdn_uri = NULL;
        $this->_cdn_ttl = NULL;
        $this->_cdn_log_retention = NULL;
        $this->_cdn_acl_user_agent = NULL;
        $this->_cdn_acl_referrer = NULL;
		
		$this->isUKAccount = $isUKAccount;

    }

    # Uses separate cURL connection to authenticate
    #
    function authenticate($user, $pass, $acct=NULL, $host=NULL)
    {
        $path = array();
        if (isset($acct) || isset($host)) {
            $headers = array(
                sprintf("%s: %s", AUTH_USER_HEADER_LEGACY, $user),
                sprintf("%s: %s", AUTH_KEY_HEADER_LEGACY, $pass),
                );
            $path[] = $host;
            $path[] = rawurlencode(sprintf("v%d",$this->api_version));
            $path[] = rawurlencode($acct);
        } else {
            $headers = array(
                sprintf("%s: %s", AUTH_USER_HEADER, $user),
                sprintf("%s: %s", AUTH_KEY_HEADER, $pass),
                );
	        if(!$this->isUKAccount) {
		    	$path[] = "https://auth.api.rackspacecloud.com";
			} else {
				$path[] = "https://lon.auth.api.rackspacecloud.com";
			}
        }
		$path[] = "v1.0";
        $url = implode("/", $path);

        $curl_ch = curl_init();
		@curl_setopt($curl_ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
        curl_setopt($curl_ch, CURLOPT_VERBOSE, $this->dbug);
        curl_setopt($curl_ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($curl_ch, CURLOPT_HEADER, 0);
        curl_setopt($curl_ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_ch, CURLOPT_USERAGENT, USER_AGENT);
        curl_setopt($curl_ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_ch, CURLOPT_HEADERFUNCTION,array(&$this,'_auth_hdr_cb'));
        curl_setopt($curl_ch, CURLOPT_SSL_VERIFYPEER, (stristr(PHP_OS, 'WIN') ? false : true));
        curl_setopt($curl_ch, CURLOPT_URL, $url);
        curl_exec($curl_ch);
        curl_close($curl_ch);

        return array($this->response_status, $this->response_reason,
            $this->storage_url, $this->cdnm_url, $this->auth_token);
    }

    # HEAD /v1/Account
    #
    function head_account()
    {
        $conn_type = "HEAD";

        $url_path = $this->_make_path();
        $return_code = $this->_send_request($conn_type,$url_path);

        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            array(0,$this->error_str,0,0);
        }
        if ($return_code == 404) {
            return array($return_code,"Account not found.",0,0);
        }
        if ($return_code == 204) {
            return array($return_code,$this->response_reason,
                $this->_account_container_count, $this->_account_bytes_used);
        }
        return array($return_code,$this->response_reason,0,0);
    }

    # HEAD /v1/Account/Container
    #
    function head_container($container_name)
    {

        if ($container_name == "") {
            $this->error_str = "Container name not set.";
            return False;
        }

        if ($container_name != "0" and !isset($container_name)) {
            $this->error_str = "Container name not set.";
            return False;
        }

        $conn_type = "HEAD";

        $url_path = $this->_make_path("STORAGE", $container_name);
        $return_code = $this->_send_request($conn_type,$url_path);

        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            array(0,$this->error_str,0,0);
        }
        if ($return_code == 404) {
            return array($return_code,"Container not found.",0,0);
        }
        if ($return_code == 204 or 200) {
            return array($return_code,$this->response_reason,
                $this->_container_object_count, $this->_container_bytes_used);
        }
        return array($return_code,$this->response_reason,0,0);
    }
    
   # GET /v1/Account/Container/Object
    #
    function get_object_to_stream(&$obj, &$resource=NULL, $hdrs=array())
    {
        if (!is_object($obj) || get_class($obj) != "AEUtilCFObject") {
            throw new SyntaxException(
                "Method argument is not a valid AEUtilCFObject.");
        }
        if (!is_resource($resource)) {
            throw new SyntaxException(
                "Resource argument not a valid PHP resource.");
        }

        $conn_type = "GET_CALL";

        $url_path = $this->_make_path("STORAGE", $obj->container->name,$obj->name);
        $this->_obj_write_resource = $resource;
        $this->_write_callback_type = "OBJECT_STREAM";
        $return_code = $this->_send_request($conn_type,$url_path,$hdrs);

        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            return array($return_code,$this->error_str);
        }
        if ($return_code == 404) {
            $this->error_str = "Object not found.";
            return array($return_code,$this->error_str);
        }
        if (($return_code < 200) || ($return_code > 299
                && $return_code != 412 && $return_code != 304)) {
            $this->error_str = "Unexpected HTTP return code: $return_code";
            return array($return_code,$this->error_str);
        }
        return array($return_code,$this->response_reason);
    }

    # PUT /v1/Account/Container/Object
    #
    function put_object(&$obj, &$fp)
    {
        if (!is_object($obj) || get_class($obj) != "AEUtilCFObject") {
            throw new SyntaxException(
                "Method argument is not a valid AEUtilCFObject.");
        }
        if (!is_resource($fp)) {
            throw new SyntaxException(
                "File pointer argument is not a valid resource.");
        }

        $conn_type = "PUT_OBJ";
        $url_path = $this->_make_path("STORAGE", $obj->container->name,$obj->name);

        $hdrs = $this->_metadata_headers($obj);

        $etag = $obj->getETag();
        if (isset($etag)) {
            $hdrs[] = "ETag: " . $etag;
        }
        if (!$obj->content_type) {
            $hdrs[] = "Content-Type: application/octet-stream";
        } else {
            $hdrs[] = "Content-Type: " . $obj->content_type;
        }

        $this->_init($conn_type);
        curl_setopt($this->connections[$conn_type],
                CURLOPT_INFILE, $fp);
        if (!$obj->content_length) {
            # We don''t know the Content-Length, so assumed "chunked" PUT
            #
            curl_setopt($this->connections[$conn_type], CURLOPT_UPLOAD, True);
            $hdrs[] = 'Transfer-Encoding: chunked';
        } else {
            # We know the Content-Length, so use regular transfer
            #
            curl_setopt($this->connections[$conn_type],
                    CURLOPT_INFILESIZE, $obj->content_length);
        }
        $return_code = $this->_send_request($conn_type,$url_path,$hdrs);

        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            return array(0,$this->error_str,NULL);
        }
        if ($return_code == 412) {
            $this->error_str = "Missing Content-Type header";
            return array($return_code,$this->error_str,NULL);
        }
        if ($return_code == 422) {
            $this->error_str = "Derived and computed checksums do not match.";
            return array($return_code,$this->error_str,NULL);
        }
        if ($return_code != 201) {
            $this->error_str = "Unexpected HTTP return code: $return_code";
            return array($return_code,$this->error_str,NULL);
        }
        return array($return_code,$this->response_reason,$this->_obj_etag);
    }

    # HEAD /v1/Account/Container/Object
    #
    function head_object(&$obj)
    {
        if (!is_object($obj) || get_class($obj) != "AEUtilCFObject") {
            throw new SyntaxException(
                "Method argument is not a valid AEUtilCFObject.");
        }

        $conn_type = "HEAD";

        $url_path = $this->_make_path("STORAGE", $obj->container->name,$obj->name);
        $return_code = $this->_send_request($conn_type,$url_path);

        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            return array(0, $this->error_str." ".$this->response_reason,
                NULL, NULL, NULL, NULL, array());
        }

        if ($return_code == 404) {
            return array($return_code, $this->response_reason,
                NULL, NULL, NULL, NULL, array());
        }
        if ($return_code == 204 or 200) {
            return array($return_code,$this->response_reason,
                $this->_obj_etag,
                $this->_obj_last_modified,
                $this->_obj_content_type,
                $this->_obj_content_length,
                $this->_obj_metadata);
        }
        $this->error_str = "Unexpected HTTP return code: $return_code";
        return array($return_code, $this->error_str." ".$this->response_reason,
                NULL, NULL, NULL, NULL, array());
    }

	# GET /v1/Account/Container?format=json
    #
    function get_objects($cname,$limit=0,$marker=NULL,$prefix=NULL,$path=NULL)
    {
        if (!$cname) {
            $this->error_str = "Container name not set.";
            return array(0, $this->error_str, array());
        }

        $url_path = $this->_make_path("STORAGE", $cname);

        $limit = intval($limit);
        $params = array();
        $params[] = "format=json";
        if ($limit > 0) {
            $params[] = "limit=$limit";
        }
        if ($marker) {
            $params[] = "marker=".rawurlencode($marker);
        }
        if ($prefix) {
            $params[] = "prefix=".rawurlencode($prefix);
        }
        if ($path) {
            $params[] = "path=".rawurlencode($path);
        }
        if (!empty($params)) {
            $url_path .= "?" . implode("&", $params);
        }
 
        $conn_type = "GET_CALL";
        $this->_write_callback_type = "OBJECT_STRING";
        $return_code = $this->_send_request($conn_type,$url_path);

        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            return array(0,$this->error_str,array());
        }
        if ($return_code == 204) {
            $this->error_str = "Container has no Objects.";
            return array($return_code,$this->error_str,array());
        }
        if ($return_code == 404) {
            $this->error_str = "Container has no Objects.";
            return array($return_code,$this->error_str,array());
        }
        if ($return_code == 200) {
            $json_body = json_decode($this->_obj_write_string, True);
            return array($return_code,$this->response_reason, $json_body);
        }
        $this->error_str = "Unexpected HTTP response code: $return_code";
        return array(0,$this->error_str,array());
    }
	
    function get_error()
    {
        return $this->error_str;
    }

    function setDebug($bool)
    {
        $this->dbug = $bool;
        foreach ($this->connections as $k => $v) {
            if (!is_null($v)) {
                curl_setopt($this->connections[$k], CURLOPT_VERBOSE, $this->dbug);
            }
        }
    }

    function getStorageUrl()
    {
        return $this->storage_url;
    }

    function getAuthToken()
    {
        return $this->auth_token;
    }

    function setCFAuth($cfs_auth, $servicenet=False)
    {
        if ($servicenet) {
            $this->storage_url = "https://snet-" . substr($cfs_auth->storage_url, 8);
        } else {
            $this->storage_url = $cfs_auth->storage_url;
        }
        $this->auth_token = $cfs_auth->auth_token;
        $this->cdnm_url = $cfs_auth->cdnm_url;
    }

    function setReadProgressFunc($func_name)
    {
        $this->_user_read_progress_callback_func = $func_name;
    }

    function setWriteProgressFunc($func_name)
    {
        $this->_user_write_progress_callback_func = $func_name;
    }

    function getCDNMUrl()
    {
        return $this->cdnm_url;
    }

	
	# DELETE /v1/Account/Container/Object
    #
    function delete_object($container_name, $object_name)
    {
        if ($container_name == "") {
            $this->error_str = "Container name not set.";
            return 0;
        }
        
        if ($container_name != "0" and !isset($container_name)) {
            $this->error_str = "Container name not set.";
            return 0;
        }
        
        if (!$object_name) {
            $this->error_str = "Object name not set.";
            return 0;
        }

        $url_path = $this->_make_path("STORAGE", $container_name,$object_name);
        $return_code = $this->_send_request("DEL_POST",$url_path,NULL,"DELETE");
        switch ($return_code) {
        case 204:
            break;
        case 0:
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            $return_code = 0;
            break;
        case 404:
            $this->error_str = "Specified container did not exist to delete.";
            break;
        default:
            $this->error_str = "Unexpected HTTP return code: $return_code.";
        }
        return $return_code;
    }
	
    private function _header_cb($ch, $header)
    {
        preg_match("/^HTTP\/1\.[01] (\d{3}) (.*)/", $header, $matches);
        if (isset($matches[1])) {
            $this->response_status = $matches[1];
        }
        if (isset($matches[2])) {
            $this->response_reason = $matches[2];
        }

        if (stripos($header, ACCOUNT_CONTAINER_COUNT) === 0) {
            $this->_account_container_count = (float) trim(substr($header,
                    strlen(ACCOUNT_CONTAINER_COUNT)+1))+0;
            return strlen($header);
        }
        if (stripos($header, ACCOUNT_BYTES_USED) === 0) {
            $this->_account_bytes_used = (float) trim(substr($header,
                    strlen(ACCOUNT_BYTES_USED)+1))+0;
            return strlen($header);
        }
        if (stripos($header, CONTAINER_OBJ_COUNT) === 0) {
            $this->_container_object_count = (float) trim(substr($header,
                    strlen(CONTAINER_OBJ_COUNT)+1))+0;
            return strlen($header);
        }
        if (stripos($header, CONTAINER_BYTES_USED) === 0) {
            $this->_container_bytes_used = (float) trim(substr($header,
                    strlen(CONTAINER_BYTES_USED)+1))+0;
            return strlen($header);
        }
        if (stripos($header, METADATA_HEADER) === 0) {
            # $header => X-Object-Meta-Foo: bar baz
            $temp = substr($header, strlen(METADATA_HEADER));
            # $temp => Foo: bar baz
            $parts = explode(":", $temp);
            # $parts[0] => Foo
            $val = substr(strstr($temp, ":"), 1);
            # $val => bar baz
            $this->_obj_metadata[$parts[0]] = trim($val);
            return strlen($header);
        }
        if (stripos($header, "ETag:") === 0) {
            # $header => ETag: abc123def456...
            $val = substr(strstr($header, ":"), 1);
            # $val => abc123def456...
            $this->_obj_etag = trim($val);
            return strlen($header);
        }
        if (stripos($header, "Last-Modified:") === 0) {
            $val = substr(strstr($header, ":"), 1);
            $this->_obj_last_modified = trim($val);
            return strlen($header);
        }
        if (stripos($header, "Content-Type:") === 0) {
            $val = substr(strstr($header, ":"), 1);
            $this->_obj_content_type = trim($val);
            return strlen($header);
        }
        if (stripos($header, "Content-Length:") === 0) {
            $val = substr(strstr($header, ":"), 1);
            $this->_obj_content_length = (float) trim($val)+0;
            return strlen($header);
        }
        return strlen($header);
    }

    private function _read_cb($ch, $fd, $length)
    {
        $data = fread($fd, $length);
        $len = strlen($data);
        if (isset($this->_user_write_progress_callback_func)) {
            call_user_func($this->_user_write_progress_callback_func, $len);
        }
        return $data;
    }

    private function _write_cb($ch, $data)
    {
        $dlen = strlen($data);
        switch ($this->_write_callback_type) {
        case "TEXT_LIST":
	     $this->_return_list = $this->_return_list . $data;
	     //= explode("\n",$data); # keep tab,space
	     //his->_text_list[] = rtrim($data,"\n\r\x0B"); # keep tab,space
            break;
        case "OBJECT_STREAM":
            fwrite($this->_obj_write_resource, $data, $dlen);
            break;
        case "OBJECT_STRING":
            $this->_obj_write_string .= $data;
            break;
        }
        if (isset($this->_user_read_progress_callback_func)) {
            call_user_func($this->_user_read_progress_callback_func, $dlen);
        }
        return $dlen;
    }

    private function _auth_hdr_cb($ch, $header)
    {
        preg_match("/^HTTP\/1\.[01] (\d{3}) (.*)/", $header, $matches);
        if (isset($matches[1])) {
            $this->response_status = $matches[1];
        }
        if (isset($matches[2])) {
            $this->response_reason = $matches[2];
        }
        if (stripos($header, STORAGE_URL) === 0) {
            $this->storage_url = trim(substr($header, strlen(STORAGE_URL)+1));
        }
        if (stripos($header, AUTH_TOKEN) === 0) {
            $this->auth_token = trim(substr($header, strlen(AUTH_TOKEN)+1));
        }
        if (stripos($header, AUTH_TOKEN_LEGACY) === 0) {
            $this->auth_token = trim(substr($header,strlen(AUTH_TOKEN_LEGACY)+1));
        }
        return strlen($header);
    }

    private function _make_headers($hdrs=NULL)
    {
        $new_headers = array();
        $has_stoken = False;
        $has_uagent = False;
        if (is_array($hdrs)) {
            foreach ($hdrs as $h => $v) {
                if (is_int($h)) {
                    $parts = explode(":", $v);
                    $header = $parts[0];
                    $value = trim(substr(strstr($v, ":"), 1));
                } else {
                    $header = $h;
                    $value = trim($v);
                }

                if (stripos($header, AUTH_TOKEN) === 0) {
                    $has_stoken = True;
                }
                if (stripos($header, "user-agent") === 0) {
                    $has_uagent = True;
                }
                $new_headers[] = $header . ": " . $value;
            }
        }
        if (!$has_stoken) {
            $new_headers[] = AUTH_TOKEN . ": " . $this->auth_token;
        }
        if (!$has_uagent) {
            $new_headers[] = "User-Agent: " . USER_AGENT;
        }
        return $new_headers;
    }

    private function _init($conn_type, $force_new=False)
    {
        if (!array_key_exists($conn_type, $this->connections)) {
            $this->error_str = "Invalid CURL_XXX connection type";
            return False;
        }

        if (is_null($this->connections[$conn_type]) || $force_new) {
            $ch = curl_init();
			@curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
        } else {
            return;
        }

        if ($this->dbug) { curl_setopt($ch, CURLOPT_VERBOSE, 1); }

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (stristr(PHP_OS, 'WIN') ? false : true));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$this, '_header_cb'));

        if ($conn_type == "GET_CALL") {
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, array(&$this, '_write_cb'));
        }

        if ($conn_type == "PUT_OBJ") {
            curl_setopt($ch, CURLOPT_PUT, 1);
            curl_setopt($ch, CURLOPT_READFUNCTION, array(&$this, '_read_cb'));
        }
        if ($conn_type == "HEAD") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        if ($conn_type == "PUT_CONT") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_INFILESIZE, 0);
	    	curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        if ($conn_type == "DEL_POST") {
        	curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
        $this->connections[$conn_type] = $ch;
        return;
    }

    private function _reset_callback_vars()
    {
        $this->_text_list = array();
		$this->_return_list = NULL;
        $this->_account_container_count = 0;
        $this->_account_bytes_used = 0;
        $this->_container_object_count = 0;
        $this->_container_bytes_used = 0;
        $this->_obj_etag = NULL;
        $this->_obj_last_modified = NULL;
        $this->_obj_content_type = NULL;
        $this->_obj_content_length = NULL;
        $this->_obj_metadata = array();
        $this->_obj_write_string = "";
        $this->_cdn_enabled = NULL;
        $this->_cdn_uri = NULL;
        $this->_cdn_ttl = NULL;
        $this->response_status = 0;
        $this->response_reason = "";
    }

    private function _make_path($t="STORAGE",$c=NULL,$o=NULL)
    {
        $path = array();
        switch ($t) {
	        case "STORAGE":
	            $path[] = $this->storage_url; break;
        }
        if ($c == "0")
            $path[] = rawurlencode($c);

        if ($c) {
            $path[] = rawurlencode($c);
        }
        if ($o) {
            # mimic Python''s urllib.quote() feature of a "safe" '/' character
            #
            $path[] = str_replace("%2F","/",rawurlencode($o));
        }
        return implode("/",$path);
    }

    private function _metadata_headers(&$obj)
    {
        $hdrs = array();
        foreach ($obj->metadata as $k => $v) {
            if (strpos($k,":") !== False) {
                throw new SyntaxException(
                    "Metadata keys cannot contain a ':' character.");
            }
            $k = trim($k);
            $key = sprintf("%s%s", METADATA_HEADER, $k);
            if (!array_key_exists($key, $hdrs)) {
                if (strlen($k) > 128 || strlen($v) > 256) {
                    $this->error_str = "Metadata key or value exceeds ";
                    $this->error_str .= "maximum length: ($k: $v)";
                    return 0;
                }
                $hdrs[] = sprintf("%s%s: %s", METADATA_HEADER, $k, trim($v));
            }
        }
        return $hdrs;
    }

    private function _send_request($conn_type, $url_path, $hdrs=NULL, $method="GET")
    {
        $this->_init($conn_type);
        $this->_reset_callback_vars();
        $headers = $this->_make_headers($hdrs);

        if (gettype($this->connections[$conn_type]) == "unknown type")
            throw new ConnectionNotOpenException (
                "Connection is not open."
                );

        switch ($method) {
        case "DELETE":
            curl_setopt($this->connections[$conn_type],
                CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        case "POST":
            curl_setopt($this->connections[$conn_type],
                CURLOPT_CUSTOMREQUEST, "POST");
        default:
            break;
        }

        curl_setopt($this->connections[$conn_type],
                    CURLOPT_HTTPHEADER, $headers);

        curl_setopt($this->connections[$conn_type],
            CURLOPT_URL, $url_path);

        if (!curl_exec($this->connections[$conn_type])) {
            $this->error_str = "(curl error: "
                . curl_errno($this->connections[$conn_type]) . ") ";
            $this->error_str .= curl_error($this->connections[$conn_type]);
            return False;
        }
        return curl_getinfo($this->connections[$conn_type], CURLINFO_HTTP_CODE);
    }

    function close()
    {
        foreach ($this->connections as $cnx) {
            if (isset($cnx)) {
                curl_close($cnx);
                $this->connections[$cnx] = NULL;
            }
        }
    }
    private function create_array()
    {
	$this->_text_list = explode("\n",rtrim($this->_return_list,"\n\x0B"));
	return True;
    }

}

/**
 * Class for handling Cloud Files Authentication, call it's {@link authenticate()}
 * method to obtain authorized service urls and an authentication token.
 *
 * Example:
 * <code>
 * # Create the authentication instance
 * #
 * $auth = new AEUtilCFAuthentication("username", "api_key");
 * # Perform authentication request
 * #
 * $auth->authenticate();
 * </code>
 *
 * @package php-cloudfiles
 */
class AEUtilCFAuthentication
{
    public $dbug;
    public $username;
    public $api_key;
    public $auth_host;
    public $account;
	public $isUKAccount = false;

    /**
     * Instance variables that are set after successful authentication
     */
    public $storage_url;
    public $cdnm_url;
    public $auth_token;

    /**
     * Class constructor (PHP 5 syntax)
     *
     * @param string $username Mosso username
     * @param string $api_key Mosso API Access Key
     * @param string $account <b>Deprecated</b> <i>Account name</i>
     * @param string $auth_host <b>Deprecated</b> <i>Authentication service URI</i>
     */
    function __construct($username=NULL, $api_key=NULL, $account=NULL, $auth_host=NULL, $isUKAccount = false)
    {

        $this->dbug = False;
        $this->username = $username;
        $this->api_key = $api_key;
        $this->account_name = $account;
        $this->auth_host = $auth_host;

        $this->storage_url = NULL;
        $this->cdnm_url = NULL;
        $this->auth_token = NULL;
		
		$this->isUKAccount = $isUKAccount;

        $this->cfs_http = new AEUtilCfhttp(DEFAULT_CF_API_VERSION, $this->isUKAccount);
    }

    /**
     * Attempt to validate Username/API Access Key
     *
     * Attempts to validate credentials with the authentication service.  It
     * either returns <kbd>True</kbd> or throws an Exception.  Accepts a single
     * (optional) argument for the storage system API version.
     *
     * Example:
     * <code>
     * # Create the authentication instance
     * #
     * $auth = new AEUtilCFAuthentication("username", "api_key");
     *
     * # Perform authentication request
     * #
     * $auth->authenticate();
     * </code>
     *
     * @param string $version API version for Auth service (optional)
     * @return boolean <kbd>True</kbd> if successfully authenticated
     * @throws AuthenticationException invalid credentials
     * @throws InvalidResponseException invalid response
     */
    function authenticate($version=DEFAULT_CF_API_VERSION)
    {
        list($status,$reason,$surl,$curl,$atoken) =
                $this->cfs_http->authenticate($this->username, $this->api_key,
                $this->account_name, $this->auth_host);

        if ($status == 401) {
            throw new AuthenticationException("Invalid username or access key.");
        }
        if ($status != 204) {
            throw new InvalidResponseException(
                "Unexpected response (".$status."): ".$reason);
        }

        if (!($surl || $curl) || !$atoken) {
            throw new InvalidResponseException(
                "Expected headers missing from auth service.");
        }
        $this->storage_url = $surl;
        $this->cdnm_url = $curl;
        $this->auth_token = $atoken;
        return True;
    }
	/**
	 * Use Cached Token and Storage URL's rather then grabbing from the Auth System
         *
         * Example:
 	 * <code>
         * #Create an Auth instance
         * $auth = new AEUtilCFAuthentication();
         * #Pass Cached URL's and Token as Args
	 * $auth->load_cached_credentials("auth_token", "storage_url", "cdn_management_url");
         * </code>
	 *
	 * @param string $auth_token A Cloud Files Auth Token (Required)
         * @param string $storage_url The Cloud Files Storage URL (Required)
         * @param string $cdnm_url CDN Management URL (Required)
         * @return boolean <kbd>True</kbd> if successful
	 * @throws SyntaxException If any of the Required Arguments are missing
         */
	function load_cached_credentials($auth_token, $storage_url, $cdnm_url)
    {
        if(!$storage_url || !$cdnm_url)
        {
                throw new SyntaxException("Missing Required Interface URL's!");
                return False;
        }
        if(!$auth_token)
        {
                throw new SyntaxException("Missing Auth Token!");
                return False;
        }

        $this->storage_url = $storage_url;
        $this->cdnm_url    = $cdnm_url;
        $this->auth_token  = $auth_token;
        return True;
    }
	/**
         * Grab Cloud Files info to be Cached for later use with the load_cached_credentials method.
         *
	 * Example:
         * <code>
         * #Create an Auth instance
         * $auth = new AEUtilCFAuthentication("UserName","API_Key");
         * $auth->authenticate();
         * $array = $auth->export_credentials();
         * </code>
         *
	 * @return array of url's and an auth token.
         */
    function export_credentials()
    {
        $arr = array();
        $arr['storage_url'] = $this->storage_url;
        $arr['cdnm_url']    = $this->cdnm_url;
        $arr['auth_token']  = $this->auth_token;

        return $arr;
    }


    /**
     * Make sure the AEUtilCFAuthentication instance has authenticated.
     *
     * Ensures that the instance variables necessary to communicate with
     * Cloud Files have been set from a previous authenticate() call.
     *
     * @return boolean <kbd>True</kbd> if successfully authenticated
     */
    function authenticated()
    {
        if (!($this->storage_url || $this->cdnm_url) || !$this->auth_token) {
            return False;
        }
        return True;
    }

    /**
     * Toggle debugging - set cURL verbose flag
     */
    function setDebug($bool)
    {
        $this->dbug = $bool;
        $this->cfs_http->setDebug($bool);
    }
}

/**
 * Class for establishing connections to the Cloud Files storage system.
 * Connection instances are used to communicate with the storage system at
 * the account level; listing and deleting Containers and returning Container
 * instances.
 *
 * Example:
 * <code>
 * # Create the authentication instance
 * #
 * $auth = new AEUtilCFAuthentication("username", "api_key");
 *
 * # Perform authentication request
 * #
 * $auth->authenticate();
 *
 * # Create a connection to the storage/cdn system(s) and pass in the
 * # validated AEUtilCFAuthentication instance.
 * #
 * $conn = new AEUtilCFConnection($auth);
 * </code>
 *
 * @package php-cloudfiles
 */
class AEUtilCFConnection
{
    public $dbug;
    public $cfs_http;
    public $cfs_auth;

    /**
     * Pass in a previously authenticated AEUtilCFAuthentication instance.
     *
     * Example:
     * <code>
     * # Create the authentication instance
     * #
     * $auth = new AEUtilCFAuthentication("username", "api_key");
     *
     * # Perform authentication request
     * #
     * $auth->authenticate();
     *
     * # Create a connection to the storage/cdn system(s) and pass in the
     * # validated AEUtilCFAuthentication instance.
     * #
     * $conn = new AEUtilCFConnection($auth);
     *
     * # If you are connecting via Rackspace servers and have access
     * # to the servicenet network you can set the $servicenet to True
     * # like this.
     *
     * $conn = new AEUtilCFConnection($auth, $servicenet=True);
     *
     * </code>
     *
     * If the environement variable RACKSPACE_SERVICENET is defined it will
     * force to connect via the servicenet.
     *
     * @param obj $cfs_auth previously authenticated AEUtilCFAuthentication instance
     * @param boolean $servicenet enable/disable access via Rackspace servicenet.
     * @throws AuthenticationException not authenticated
     */
    function __construct($cfs_auth, $servicenet=False)
    {
        if (isset($_ENV['RACKSPACE_SERVICENET']))
            $servicenet=True;
        $this->cfs_http = new AEUtilCfhttp(DEFAULT_CF_API_VERSION);
        $this->cfs_auth = $cfs_auth;
        if (!$this->cfs_auth->authenticated()) {
            $e = "Need to pass in a previously authenticated ";
            $e .= "AEUtilCFAuthentication instance.";
            throw new AuthenticationException($e);
        }
        $this->cfs_http->setCFAuth($this->cfs_auth, $servicenet=$servicenet);
        $this->dbug = False;
    }

    /**
     * Toggle debugging of instance and back-end HTTP module
     *
     * @param boolean $bool enable/disable cURL debugging
     */
    function setDebug($bool)
    {
        $this->dbug = (boolean) $bool;
        $this->cfs_http->setDebug($this->dbug);
    }

    /**
     * Close a connection
     *
     * Example:
     * <code>
     *
     * $conn->close();
     *
     * </code>
     *
     * Will close all current cUrl active connections.
     *
     */
    public function close()
    {
        $this->cfs_http->close();
    }

    /**
     * Return a Container instance
     *
     * For the given name, return a Container instance if the remote Container
     * exists, otherwise throw a Not Found exception.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new AEUtilCFAuthentication($auth);
     *
     * $images = $conn->get_container("my photos");
     * print "Number of Objects: " . $images->count . "\n";
     * print "Bytes stored in container: " . $images->bytes . "\n";
     * </code>
     *
     * @param string $container_name name of the remote Container
     * @return container AEUtilCFContainer instance
     * @throws NoSuchContainerException thrown if no remote Container
     * @throws InvalidResponseException unexpected response
     */
    function get_container($container_name=NULL)
    {
        list($status, $reason, $count, $bytes) =
                $this->cfs_http->head_container($container_name);
        if ($status == 404) {
            throw new NoSuchContainerException("Container not found.");
        }
        if ($status < 200 || $status > 299) {
            throw new InvalidResponseException(
                "Invalid response: ".$this->cfs_http->get_error());
        }
        return new AEUtilCFContainer($this->cfs_auth, $this->cfs_http,
            $container_name, $count, $bytes);
    }

    /**
     * Set a user-supplied callback function to report upload progress
     *
     * The callback function is used to report incremental progress of a data
     * upload functions (e.g. $obj->write() call).  The specified function will
     * be periodically called with the number of bytes transferred until the
     * entire upload is complete.  This callback function can be useful
     * for implementing "progress bars" for large uploads/downloads.
     *
     * The specified callback function should take a single integer parameter.
     *
     * <code>
     * function write_callback($bytes_transferred) {
     *     print ">> uploaded " . $bytes_transferred . " bytes.\n";
     *     # ... do other things ...
     *     return;
     * }
     *
     * $conn = new AEUtilCFConnection($auth_obj);
     * $conn->set_write_progress_function("write_callback");
     * $container = $conn->create_container("stuff");
     * $obj = $container->create_object("foo");
     * $obj->write("The callback function will be called during upload.");
     *
     * # output would look like this:
     * # >> uploaded 51 bytes.
     * #
     * </code>
     *
     * @param string $func_name the name of the user callback function
     */
    function set_write_progress_function($func_name)
    {
        $this->cfs_http->setWriteProgressFunc($func_name);
    }

}

/**
 * Container operations
 *
 * Containers are storage compartments where you put your data (objects).
 * A container is similar to a directory or folder on a conventional filesystem
 * with the exception that they exist in a flat namespace, you can not create
 * containers inside of containers.
 *
 * You also have the option of marking a Container as "public" so that the
 * Objects stored in the Container are publicly available via the CDN.
 *
 * @package php-cloudfiles
 */
class AEUtilCFContainer
{
    public $cfs_auth;
    public $cfs_http;
    public $name;
    public $object_count;
    public $bytes_used;

    public $cdn_enabled;
    public $cdn_uri;
    public $cdn_ttl;
    public $cdn_log_retention;
    public $cdn_acl_user_agent;
    public $cdn_acl_referrer;

    /**
     * Class constructor
     *
     * Constructor for Container
     *
     * @param obj $cfs_auth AEUtilCFAuthentication instance
     * @param obj $cfs_http HTTP connection manager
     * @param string $name name of Container
     * @param int $count number of Objects stored in this Container
     * @param int $bytes number of bytes stored in this Container
     * @throws SyntaxException invalid Container name
     */
    function __construct(&$cfs_auth, &$cfs_http, $name, $count=0,
        $bytes=0, $docdn=True)
    {
        if (strlen($name) > MAX_CONTAINER_NAME_LEN) {
            throw new SyntaxException("Container name exceeds "
                . "maximum allowed length.");
        }
        if (strpos($name, "/") !== False) {
            throw new SyntaxException(
                "Container names cannot contain a '/' character.");
        }
        $this->cfs_auth = $cfs_auth;
        $this->cfs_http = $cfs_http;
        $this->name = $name;
        $this->object_count = $count;
        $this->bytes_used = $bytes;
        $this->cdn_enabled = NULL;
        $this->cdn_uri = NULL;
        $this->cdn_ttl = NULL;
        $this->cdn_log_retention = NULL;
        $this->cdn_acl_user_agent = NULL;
        $this->cdn_acl_referrer = NULL;
        if ($this->cfs_http->getCDNMUrl() != NULL && $docdn) {
            $this->_cdn_initialize();
        }
    }

    /**
     * String representation of Container
     *
     * Pretty print the Container instance.
     *
     * @return string Container details
     */
    function __toString()
    {
        $me = sprintf("name: %s, count: %.0f, bytes: %.0f",
            $this->name, $this->object_count, $this->bytes_used);
        if ($this->cfs_http->getCDNMUrl() != NULL) {
            $me .= sprintf(", cdn: %s, cdn uri: %s, cdn ttl: %.0f, logs retention: %s",
                $this->is_public() ? "Yes" : "No",
                $this->cdn_uri, $this->cdn_ttl,
                $this->cdn_log_retention ? "Yes" : "No"
                );

            if ($this->cdn_acl_user_agent != NULL) {
                $me .= ", cdn acl user agent: " . $this->cdn_acl_user_agent;
            }

            if ($this->cdn_acl_referrer != NULL) {
                $me .= ", cdn acl referrer: " . $this->cdn_acl_referrer;
            }


        }
        return $me;
    }


    function create_object($obj_name=NULL)
    {
        return new AEUtilCFObject($this, $obj_name);
    }

    /**
     * Helper function to create "path" elements for a given Object name
     *
     * Given an Object whos name contains '/' path separators, this function
     * will create the "directory marker" Objects of one byte with the
     * Content-Type of "application/folder".
     *
     * It assumes the last element of the full path is the "real" Object
     * and does NOT create a remote storage Object for that last element.
     */
    function create_paths($path_name)
    {
        if ($path_name[0] == '/') {
            $path_name = mb_substr($path_name, 0, 1);
        }
        $elements = explode('/', $path_name, -1);
        $build_path = "";
        foreach ($elements as $idx => $val) {
            if (!$build_path) {
                $build_path = $val;
            } else {
                $build_path .= "/" . $val;
            }
            $obj = new AEUtilCFObject($this, $build_path);
            $obj->content_type = "application/directory";
            $obj->write(".", 1);
        }
    }

    /**
	* Delete a remote storage Object
	*
	* Given an Object instance or name, permanently remove the remote Object
	* and all associated metadata.
	*
	* Example:
	* <code>
	* # ... authentication code excluded (see previous examples) ...
	* #
	* $conn = new CF_Authentication($auth);
	*
	* $images = $conn->get_container("my photos");
	*
	* # Delete specific object
	* #
	* $images->delete_object("disco_dancing.jpg");
	* </code>
	*
	* @param obj $obj name or instance of Object to delete
	* @return boolean <kbd>True</kbd> if successfully removed
	* @throws SyntaxException invalid Object name
	* @throws NoSuchObjectException remote Object does not exist
	* @throws InvalidResponseException unexpected response
	*/
    function delete_object($obj)
    {
        $obj_name = NULL;
        if (is_object($obj)) {
            if (get_class($obj) == "AEUtilCFObject") {
                $obj_name = $obj->name;
            }
        }
        if (is_string($obj)) {
            $obj_name = $obj;
        }
        if (!$obj_name) {
            throw new SyntaxException("Object name not set.");
        }
        $status = $this->cfs_http->delete_object($this->name, $obj_name);
        #if ($status == 401 && $this->_re_auth()) {
        # return $this->delete_object($obj);
        #}
        if ($status == 404) {
            $m = "Specified object '".$this->name."/".$obj_name;
            $m.= "' did not exist to delete.";
            throw new NoSuchObjectException($m);
        }
        if ($status != 204) {
            throw new InvalidResponseException(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return True;
    }
	
	/**
     * Return an Object instance for the remote storage Object
     *
     * Given a name, return a Object instance representing the
     * remote storage object.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # This call only fetches header information and not the content of
     * # the storage object.  Use the Object's read() or stream() methods
     * # to obtain the object's data.
     * #
     * $pic = $public_container->get_object("baby.jpg");
     * </code>
     *
     * @param string $obj_name name of storage Object
     * @return obj CF_Object instance
     */
    function get_object($obj_name=NULL)
    {
        return new AEUtilCFObject($this, $obj_name, True);
    }
	
	/**
     * Return a list of Objects
     *
     * Return an array of strings listing the Object names in this Container.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $images = $conn->get_container("my photos");
     *
     * # Grab the list of all storage objects
     * #
     * $all_objects = $images->list_objects();
     *
     * # Grab subsets of all storage objects
     * #
     * $first_ten = $images->list_objects(10);
     * 
     * # Note the use of the previous result's last object name being
     * # used as the 'marker' parameter to fetch the next 10 objects
     * #
     * $next_ten = $images->list_objects(10, $first_ten[count($first_ten)-1]);
     *
     * # Grab images starting with "birthday_party" and default limit/marker
     * # to match all photos with that prefix
     * #
     * $prefixed = $images->list_objects(0, NULL, "birthday");
     *
     * # Assuming you have created the appropriate directory marker Objects,
     * # you can traverse your pseudo-hierarchical containers
     * # with the "path" argument.
     * #
     * $animals = $images->list_objects(0,NULL,NULL,"pictures/animals");
     * $dogs = $images->list_objects(0,NULL,NULL,"pictures/animals/dogs");
     * </code>
     *
     * @param int $limit <i>optional</i> only return $limit names
     * @param int $marker <i>optional</i> subset of names starting at $marker
     * @param string $prefix <i>optional</i> Objects whose names begin with $prefix
     * @param string $path <i>optional</i> only return results under "pathname"
     * @return array array of strings
     * @throws InvalidResponseException unexpected response
     */
    function list_objects($limit=0, $marker=NULL, $prefix=NULL, $path=NULL)
    {
        list($status, $reason, $obj_list) =
            $this->cfs_http->list_objects($this->name, $limit,
                $marker, $prefix, $path);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->list_objects($limit, $marker, $prefix, $path);
        #}
        if ($status < 200 || $status > 299) {
            throw new InvalidResponseException(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return $obj_list;
    }
}


/**
 * Object operations
 *
 * An Object is analogous to a file on a conventional filesystem. You can
 * read data from, or write data to your Objects. You can also associate
 * arbitrary metadata with them.
 *
 * @package php-cloudfiles
 */
class AEUtilCFObject
{
    public $container;
    public $name;
    public $last_modified;
    public $content_type;
    public $content_length;
    public $metadata;
    private $etag;

    /**
     * Class constructor
     *
     * @param obj $container AEUtilCFContainer instance
     * @param string $name name of Object
     * @param boolean $force_exists if set, throw an error if Object doesn't exist
     */
    function __construct(&$container, $name, $force_exists=False, $dohead=True)
    {
        if ($name[0] == "/") {
            $r = "Object name '".$name;
            $r .= "' cannot contain begin with a '/' character.";
            throw new SyntaxException($r);
        }
        if (strlen($name) > MAX_OBJECT_NAME_LEN) {
            throw new SyntaxException("Object name exceeds "
                . "maximum allowed length.");
        }
        $this->container = $container;
        $this->name = $name;
        $this->etag = NULL;
        $this->_etag_override = False;
        $this->last_modified = NULL;
        $this->content_type = NULL;
        $this->content_length = 0;
        $this->metadata = array();
        if ($dohead) {
            if (!$this->_initialize() && $force_exists) {
                throw new NoSuchObjectException("No such object '".$name."'");
            }
        }
    }

    /**
     * String representation of Object
     *
     * Pretty print the Object's location and name
     *
     * @return string Object information
     */
    function __toString()
    {
        return $this->container->name . "/" . $this->name;
    }

    /**
     * Internal check to get the proper mimetype.
     *
     * This function would go over the available PHP methods to get
     * the MIME type.
     *
     * By default it will try to use the PHP fileinfo library which is
     * available from PHP 5.3 or as an PECL extension
     * (http://pecl.php.net/package/Fileinfo).
     *
     * It will get the magic file by default from the system wide file
     * which is usually available in /usr/share/magic on Unix or try
     * to use the file specified in the source directory of the API
     * (share directory).
     *
     * if fileinfo is not available it will try to use the internal
     * mime_content_type function.
     *
     * @param string $handle name of file or buffer to guess the type from
     * @return boolean <kbd>True</kbd> if successful
     * @throws BadContentTypeException
     */
    function _guess_content_type($handle) {
        if ($this->content_type)
            return;

        if (function_exists("finfo_open")) {
            $local_magic = dirname(__FILE__) . "/share/magic";
            $finfo = @finfo_open(FILEINFO_MIME, $local_magic);

            if (!$finfo)
                $finfo = @finfo_open(FILEINFO_MIME);

            if ($finfo) {

                if (is_file((string)$handle))
                    $ct = @finfo_file($finfo, $handle);
                else
                    $ct = @finfo_buffer($finfo, $handle);

                /* PHP 5.3 fileinfo display extra information like
                   charset so we remove everything after the ; since
                   we are not into that stuff */
                if ($ct) {
                    $extra_content_type_info = strpos($ct, "; ");
                    if ($extra_content_type_info)
                        $ct = substr($ct, 0, $extra_content_type_info);
                }

                if ($ct && $ct != 'application/octet-stream')
                    $this->content_type = $ct;

                @finfo_close($finfo);
            }
        }

        if (!$this->content_type && (string)is_file($handle) && function_exists("mime_content_type")) {
            $this->content_type = @mime_content_type($handle);
        }

        if (!$this->content_type) {
            throw new BadContentTypeException("Required Content-Type not set");
        }
        return True;
    }
	
	/**
     * String representation of the Object's public URI
     *
     * A string representing the Object's public URI assuming that it's
     * parent Container is CDN-enabled.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Print out the Object's CDN URI (if it has one) in an HTML img-tag
     * #
     * print "<img src='$pic->public_uri()' />\n";
     * </code>
     *
     * @return string Object's public URI or NULL
     */
    function public_uri()
    {
        if ($this->container->cdn_enabled) {
            return $this->container->cdn_uri . "/" . $this->name;
        }
        return NULL;
    }

       /**
     * String representation of the Object's public SSL URI
     *
     * A string representing the Object's public SSL URI assuming that it's
     * parent Container is CDN-enabled.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Print out the Object's CDN SSL URI (if it has one) in an HTML img-tag
     * #
     * print "<img src='$pic->public_ssl_uri()' />\n";
     * </code>
     *
     * @return string Object's public SSL URI or NULL
     */
    function public_ssl_uri()
    {
        if ($this->container->cdn_enabled) {
            return $this->container->cdn_ssl_uri . "/" . $this->name;
        }
        return NULL;
    }
	
	/**
     * Read the remote Object's data
     *
     * Returns the Object's data.  This is useful for smaller Objects such
     * as images or office documents.  Object's with larger content should use
     * the stream() method below.
     *
     * Pass in $hdrs array to set specific custom HTTP headers such as
     * If-Match, If-None-Match, If-Modified-Since, Range, etc.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     * $data = $doc->read(); # read image content into a string variable
     * print $data;
     *
     * # Or see stream() below for a different example.
     * #
     * </code>
     *
     * @param array $hdrs user-defined headers (Range, If-Match, etc.)
     * @return string Object's data
     * @throws InvalidResponseException unexpected response
     */
    function read($hdrs=array())
    {
        list($status, $reason, $data) =
            $this->container->cfs_http->get_object_to_string($this, $hdrs);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->read($hdrs);
        #}
        if (($status < 200) || ($status > 299
                && $status != 412 && $status != 304)) {
            throw new InvalidResponseException("Invalid response (".$status."): "
                . $this->container->cfs_http->get_error());
        }
        return $data;
    }
    
    /**
     * Streaming read of Object's data
     *
     * Given an open PHP resource (see PHP's fopen() method), fetch the Object's
     * data and write it to the open resource handle.  This is useful for
     * streaming an Object's content to the browser (videos, images) or for
     * fetching content to a local file.
     *
     * Pass in $hdrs array to set specific custom HTTP headers such as
     * If-Match, If-None-Match, If-Modified-Since, Range, etc.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Assuming this is a web script to display the README to the
     * # user's browser:
     * #
     * <?php
     * // grab README from storage system
     * //
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * // Hand it back to user's browser with appropriate content-type
     * //
     * header("Content-Type: " . $doc->content_type);
     * $output = fopen("php://output", "w");
     * $doc->stream($output); # stream object content to PHP's output buffer
     * fclose($output);
     * ?>
     *
     * # See read() above for a more simple example.
     * #
     * </code>
     *
     * @param resource $fp open resource for writing data to
     * @param array $hdrs user-defined headers (Range, If-Match, etc.)
     * @return string Object's data
     * @throws InvalidResponseException unexpected response
     */
    function stream(&$fp, $hdrs=array())
    {
        list($status, $reason) = 
                $this->container->cfs_http->get_object_to_stream($this,$fp,$hdrs);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->stream($fp, $hdrs);
        #}
        if (($status < 200) || ($status > 299
                && $status != 412 && $status != 304)) {
            throw new InvalidResponseException("Invalid response (".$status."): "
                .$reason);
        }
        return True;
    }

    /**
     * Store new Object metadata
     *
     * Write's an Object's metadata to the remote Object.  This will overwrite
     * an prior Object metadata.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Define new metadata for the object
     * #
     * $doc->metadata = array(
     *     "Author" => "EJ",
     *     "Subject" => "How to use the PHP tests",
     *     "Version" => "1.2.2"
     * );
     *
     * # Push the new metadata up to the storage system
     * #
     * $doc->sync_metadata();
     * </code>
     *
     * @return boolean <kbd>True</kbd> if successful, <kbd>False</kbd> otherwise
     * @throws InvalidResponseException unexpected response
     */
    function sync_metadata()
    {
        if (!empty($this->metadata)) {
            $status = $this->container->cfs_http->update_object($this);
            #if ($status == 401 && $this->_re_auth()) {
            #    return $this->sync_metadata();
            #}
            if ($status != 202) {
                throw new InvalidResponseException("Invalid response ("
                    .$status."): ".$this->container->cfs_http->get_error());
            }
            return True;
        }
        return False;
    }

    /**
     * Upload Object's data to Cloud Files
     *
     * Write data to the remote Object.  The $data argument can either be a
     * PHP resource open for reading (see PHP's fopen() method) or an in-memory
     * variable.  If passing in a PHP resource, you must also include the $bytes
     * parameter.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Upload placeholder text in my README
     * #
     * $doc->write("This is just placeholder text for now...");
     * </code>
     *
     * @param string|resource $data string or open resource
     * @param float $bytes amount of data to upload (required for resources)
     * @param boolean $verify generate, send, and compare MD5 checksums
     * @return boolean <kbd>True</kbd> when data uploaded successfully
     * @throws SyntaxException missing required parameters
     * @throws BadContentTypeException if no Content-Type was/could be set
     * @throws MisMatchedChecksumException $verify is set and checksums unequal
     * @throws InvalidResponseException unexpected response
     */
    function write($data=NULL, $bytes=0, $verify=True)
    {
        if (!$data) {
            throw new SyntaxException("Missing data source.");
        }
        if ($bytes > MAX_OBJECT_SIZE) {
            throw new SyntaxException("Bytes exceeds maximum object size.");
        }
        if ($verify) {
            if (!$this->_etag_override) {
                $this->etag = $this->compute_md5sum($data);
            }
        } else {
            $this->etag = NULL;
        }

        $close_fh = False;
        if (!is_resource($data)) {
            # A hack to treat string data as a file handle.  php://memory feels
            # like a better option, but it seems to break on Windows so use
            # a temporary file instead.
            #
            $fp = fopen("php://temp", "wb+");
            #$fp = fopen("php://memory", "wb+");
            fwrite($fp, $data, strlen($data));
            rewind($fp);
            $close_fh = True;
            $this->content_length = (float) strlen($data);
            if ($this->content_length > MAX_OBJECT_SIZE) {
                throw new SyntaxException("Data exceeds maximum object size");
            }
            $ct_data = substr($data, 0, 64);
        } else {
            $this->content_length = $bytes;
            $fp = $data;
            $ct_data = fread($data, 64);
            rewind($data);
        }

        $this->_guess_content_type($ct_data);

        list($status, $reason, $etag) =
                $this->container->cfs_http->put_object($this, $fp);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->write($data, $bytes, $verify);
        #}
        if ($status == 412) {
            if ($close_fh) { fclose($fp); }
            throw new SyntaxException("Missing Content-Type header");
        }
        if ($status == 422) {
            if ($close_fh) { fclose($fp); }
            throw new MisMatchedChecksumException(
                "Supplied and computed checksums do not match.");
        }
        if ($status != 201) {
            if ($close_fh) { fclose($fp); }
            throw new InvalidResponseException("Invalid response (".$status."): "
                . $this->container->cfs_http->get_error());
        }
        if (!$verify) {
            $this->etag = $etag;
        }
        if ($close_fh) { fclose($fp); }
        return True;
    }

    /**
     * Upload Object data from local filename
     *
     * This is a convenience function to upload the data from a local file.  A
     * True value for $verify will cause the method to compute the Object's MD5
     * checksum prior to uploading.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Upload my local README's content
     * #
     * $doc->load_from_filename("/home/ej/cloudfiles/readme");
     * </code>
     *
     * @param string $filename full path to local file
     * @param boolean $verify enable local/remote MD5 checksum validation
     * @return boolean <kbd>True</kbd> if data uploaded successfully
     * @throws SyntaxException missing required parameters
     * @throws BadContentTypeException if no Content-Type was/could be set
     * @throws MisMatchedChecksumException $verify is set and checksums unequal
     * @throws InvalidResponseException unexpected response
     * @throws IOException error opening file
     */
    function load_from_filename($filename, $verify=True)
    {
        $fp = @fopen($filename, "r");
        if (!$fp) {
            throw new IOException("Could not open file for reading: ".$filename);
        }

        clearstatcache();

        $size = (float) sprintf("%u", filesize($filename));
        if ($size > MAX_OBJECT_SIZE) {
            throw new SyntaxException("File size exceeds maximum object size.");
        }

        $this->_guess_content_type($filename);

        $this->write($fp, $size, $verify);
        fclose($fp);
        return True;
    }
    
	/**
	* Save Object's data to local filename
	*
	* Given a local filename, the Object's data will be written to the newly
	* created file.
	*
	* Example:
	* <code>
	* # ... authentication/connection/container code excluded
	* # ... see previous examples
	*
	* # Whoops! I deleted my local README, let me download/save it
	* #
	* $my_docs = $conn->get_container("documents");
	* $doc = $my_docs->get_object("README");
	*
	* $doc->save_to_filename("/home/ej/cloudfiles/readme.restored");
	* </code>
	*
	* @param string $filename name of local file to write data to
	* @param array $hdrs extra headers to pass to the request
	* @return boolean <kbd>True</kbd> if successful
	* @throws IOException error opening file
	* @throws InvalidResponseException unexpected response
	*/
    function save_to_filename($filename, $hdrs=array())
    {
        $fp = @fopen($filename, "wb");
        if (!$fp) {
            throw new IOException("Could not open file for writing: ".$filename);
        }
        $result = $this->stream($fp, $hdrs);
        fclose($fp);
        return $result;
    }

    /**
     * Set Object's MD5 checksum
     *
     * Manually set the Object's ETag.  Including the ETag is mandatory for
     * Cloud Files to perform end-to-end verification.  Omitting the ETag forces
     * the user to handle any data integrity checks.
     *
     * @param string $etag MD5 checksum hexidecimal string
     */
    function set_etag($etag)
    {
        $this->etag = $etag;
        $this->_etag_override = True;
    }

    /**
     * Object's MD5 checksum
     *
     * Accessor method for reading Object's private ETag attribute.
     *
     * @return string MD5 checksum hexidecimal string
     */
    function getETag()
    {
        return $this->etag;
    }

    /**
     * Compute the MD5 checksum
     *
     * Calculate the MD5 checksum on either a PHP resource or data.  The argument
     * may either be a local filename, open resource for reading, or a string.
     *
     * <b>WARNING:</b> if you are uploading a big file over a stream
     * it could get very slow to compute the md5 you probably want to
     * set the $verify parameter to False in the write() method and
     * compute yourself the md5 before if you have it.
     *
     * @param filename|obj|string $data filename, open resource, or string
     * @return string MD5 checksum hexidecimal string
     */
    function compute_md5sum(&$data)
    {

        if (function_exists("hash_init") && is_resource($data)) {
            $ctx = hash_init('md5');
            while (!feof($data)) {
                $buffer = fgets($data, 65536);
                hash_update($ctx, $buffer);
            }
            $md5 = hash_final($ctx, false);
            rewind($data);
        } elseif ((string)is_file($data)) {
            $md5 = md5_file($data);
        } else {
            $md5 = md5($data);
        }
        return $md5;
    }

    /**
     * PRIVATE: fetch information about the remote Object if it exists
     */
    private function _initialize()
    {
        list($status, $reason, $etag, $last_modified, $content_type,
            $content_length, $metadata) =
                $this->container->cfs_http->head_object($this);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->_initialize();
        #}
        if ($status == 404) {
            return False;
        }
        if ($status < 200 || $status > 299) {
            throw new InvalidResponseException("Invalid response (".$status."): "
                . $this->container->cfs_http->get_error());
        }
        $this->etag = $etag;
        $this->last_modified = $last_modified;
        $this->content_type = $content_type;
        $this->content_length = $content_length;
        $this->metadata = $metadata;
        return True;
    }

}