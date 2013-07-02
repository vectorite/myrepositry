<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.model');

class AdmintoolsModelHtmaker extends FOFModel
{
	var $defaultConfig = array(
		// System configuration
		'httpshost'		=> '',
		'httphost'		=> '',
		'symlinks'		=> 0,
		'rewritebase'	=> '',

		// Optimization and utility
		'fileorder'		=> 1,
		'exptime'		=> 0,
		'autocompress'	=> 0,
		'autoroot'		=> 1,
		'wwwredir'		=> 0,
		'olddomain'		=> '',
		'httpsurls'		=> array(),

		// Basic security
		'nodirlists'	=> 1,
		'fileinj'		=> 1,
		'phpeaster'		=> 1,
		'nohoggers'		=> 0,
		'leftovers'		=> 1,
		'hoggeragents'	=> array(
				"Indy Library",
				"libwww-perl",
				"Download Demon",
				"GetRight",
				"GetWeb!",
				"Go!Zilla",
				"Go-Ahead-Got-It",
				"GrabNet",
				"TurnitinBot"
				),

		// Server protection
		// -- Toggle protection
		'backendprot'	=> 1,
		'frontendprot'	=> 1,
		'allowxmlrpc'	=> 1,
		// -- Fine-tuning
		'bepexdirs'		=> array('components','modules','templates','images','plugins'),
		'bepextypes'	=> array(
				'jpe','jpg','jpeg','jp2','jpe2','png','gif','bmp','css','js',
				'swf','html','mpg','mp3','mpeg','mp4','avi','wav','ogg','ogv',
				'xls','xlsx','doc','docx','ppt','pptx','zip','rar','pdf','xps',
				'txt','7z','svg','odt','ods','odp','flv','mov', 'htm', 'ttf',
				'woff', 'eot'
				),
		'fepexdirs'		=> array('components','modules','templates','images','plugins','media','libraries'),
		'fepextypes'	=> array(
				'jpe','jpg','jpeg','jp2','jpe2','png','gif','bmp','css','js',
				'swf','html','mpg','mp3','mpeg','mp4','avi','wav','ogg','ogv',
				'xls','xlsx','doc','docx','ppt','pptx','zip','rar','pdf','xps',
				'txt','7z','svg','odt','ods','odp','flv','mov', 'ico', 'htm',
				'ttf', 'woff', 'eot'
				),
		// -- Exceptions
		'exceptionfiles'=> array(
				"administrator/components/com_akeeba/restore.php",
				"administrator/components/com_admintools/restore.php"
				),
		'exceptiondirs' => array(
				),
		'fullaccessdirs' => array(
				"templates/your_template_name_here"
				),

		// Custom .htaccess rules
		'custhead'		=> '',
		'custfoot'		=> ''
	);

	private $config = null;

	public function  __construct($config = array()) {
		parent::__construct($config);

		$myURI = JURI::getInstance();
		$path = $myURI->getPath();
		$path_parts = explode('/',$path);
		$path_parts = array_slice($path_parts, 0, count($path_parts) - 2 );
		$path = implode('/', $path_parts);
		$myURI->setPath($path);
		// Unset any query parameters
		$myURI->setQuery('');

		$host = $myURI->toString();
		$host = substr($host, strpos($host, '://') + 3 );

		$path = trim($path,'/');

		if(!empty($path))
		{
			$this->defaultConfig['rewritebase'] = $path;
		}
		else
		{
			$this->defaultConfig['rewritebase'] = '/';
		}
		$this->defaultConfig['httphost'] = $host;
		$this->defaultConfig['httpshost'] = $host;
		$this->defaultConfig = (object)$this->defaultConfig;
	}

	public function loadConfiguration()
	{
		if(is_null($this->config))
		{

			$params = JModel::getInstance('Storage','AdmintoolsModel');
			$savedConfig = $params->getValue('htconfig','');
			if(!empty($savedConfig))
			{
				if( function_exists('base64_encode') && function_exists('base64_encode') ) {
					$savedConfig = base64_decode($savedConfig);
				}
				$savedConfig = json_decode($savedConfig, true);
			}
			else
			{
				$savedConfig = array();
			}

			$config = $this->defaultConfig;
			if(!empty($savedConfig))
			{
				foreach($savedConfig as $key => $value)
				{
					$config->$key = $value;
				}
			}

			$this->config = $config;
		}

		return $this->config;
	}

	public function saveConfiguration($data)
	{
		$config = $this->defaultConfig;
		if(!empty($data))
		{
			$ovars = get_object_vars($config);
			$okeys = array_keys($ovars);
			foreach($data as $key => $value)
			{
				if(in_array($key, $okeys))
				{
					// Clean up array types coming from textareas
					if(in_array($key,array('hoggeragents','bepexdirs',
						'bepextypes','fepexdirs','fepextypes',
						'exceptionfiles','exceptiondirs','fullaccessdirs',
						'httpsurls')))
					{
						if(empty($value)) {
							$value = array();
						} else {
							$value = trim($value);
							$value = explode("\n", $value);
							if(!empty($value))
							{
								$ret = array();
								foreach($value as $v)
								{
									$vv = trim($v);
									if(!empty($vv)) $ret[] = $vv;
								}
								if(!empty($ret)) {
									$value = $ret;
								} else {
									$value = array();
								}
							}
						}
					}
					$config->$key = $value;
				}
			}
		}

		$this->config = $config;
		$config = json_encode($config);
		// This keeps JRegistry from hapilly corrupting our data :@
		if( function_exists('base64_encode') && function_exists('base64_encode') ) {
			$config = base64_encode($config);
		}
		$params = JModel::getInstance('Storage','AdmintoolsModel');
		$params->setValue('htconfig', $config);
		$params->save();
	}

	public function makeHtaccess()
	{
		// Guess Apache features
		$apacheVersion = $this->apacheVersion();
		$serverCaps = (object)array(
			'customCodes'		=> version_compare($apacheVersion,'2.2','ge'),	// Cusstom redirections, e.g. R=301
			'deflate'			=> version_compare($apacheVersion,'2.0','ge')	// mod_deflate support
		);
		$redirCode = $serverCaps->customCodes ? '[R=301,L]' : '[R,L]';
		
		jimport('joomla.utilities.date');
		$date = new JDate();
		$d = $date->toFormat();
		$version = ADMINTOOLS_VERSION;
		$htaccess = <<<END
### ===========================================================================
### Security Enhanced & Highly Optimized .htaccess File for Joomla!
### automatically generated by Admin Tools $version on $d GMT
### Auto-detected Apache version: $apacheVersion (best guess)
### ===========================================================================
###
### The contents of this file are based on the same author's work "Master
### .htaccess", published on http://snipt.net/nikosdion/the-master-htaccess
###
### Admin Tools is Free Software, distributed under the terms of the GNU
### General Public License version 3 or, at your option, any later version
### published by the Free Software Foundation.
###
### !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! IMPORTANT !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
### !!                                                                       !!
### !!  If you get an Internal Server Error 500 or a blank page when trying  !!
### !!  to access your site, remove this file and try tweaking its settings  !!
### !!  in the back-end of the Admin Tools component.                        !!
### !!                                                                       !!
### !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
###


END;

		$config = $this->loadConfiguration();
		$htaccess .= "##### RewriteEngine enabled - BEGIN\n";
		$htaccess .= "RewriteEngine On\n";
		$htaccess .= "##### RewriteEngine enabled - END\n\n";

		$rewritebase = $config->rewritebase;
		if(!empty($rewritebase))
		{
			$htaccess .= "##### RewriteBase set - BEGIN\n";
			$rewritebase = rtrim($rewritebase,'/');
			$htaccess .= "RewriteBase /$rewritebase\n";
			$htaccess .= "##### RewriteBase set - END\n\n";
		}

		if(!empty($config->custhead)) {
			$htaccess .= "##### Custom Rules (Top of File) -- BEGIN\n";
			$htaccess .= $config->custhead."\n";
			$htaccess .= "##### Custom Rules (Top of File) -- END\n\n";
		}

		if($config->fileorder == 1) {
			$htaccess .= "##### File execution order -- BEGIN\n";
			$htaccess .= "DirectoryIndex index.php index.html\n";
			$htaccess .= "##### File execution order -- END\n\n";
		}

		if($config->nodirlists == 1)
		{
			$htaccess .= "##### No directory listings -- BEGIN\n";
			$htaccess .= "IndexIgnore *\n";
			if($config->symlinks == 1)
			{
				$htaccess .= "Options +FollowSymLinks All -Indexes\n";
			}
			else
			{
				$htaccess .= "Options All -Indexes\n";
			}
			$htaccess .= "##### No directory listings -- END\n\n";
		}
		elseif($config->symlinks == 1)
		{
			$htaccess .= "##### Follow symlinks -- BEGIN\n";
			$htaccess .= "Options +FollowSymLinks\n";
			$htaccess .= "##### Follow symlinks -- END\n\n";
		}

		if($config->exptime == 1)
		{
			$htaccess .= <<<END
##### Optimal default expiration time - BEGIN
<IfModule mod_expires.c>
	# Enable expiration control
	ExpiresActive On

	# Default expiration: 1 hour after request
	ExpiresDefault "now plus 1 hour"

	# CSS and JS expiration: 1 week after request
	ExpiresByType text/css "now plus 1 week"
	ExpiresByType application/javascript "now plus 1 week"
	ExpiresByType application/x-javascript "now plus 1 week"
	
	# Image files expiration: 1 month after request
	ExpiresByType image/bmp "now plus 1 month"
	ExpiresByType image/gif "now plus 1 month"
	ExpiresByType image/jpeg "now plus 1 month"
	ExpiresByType image/jp2 "now plus 1 month"
	ExpiresByType image/pipeg "now plus 1 month"
	ExpiresByType image/png "now plus 1 month"
	ExpiresByType image/svg+xml "now plus 1 month"
	ExpiresByType image/tiff "now plus 1 month"
	ExpiresByType image/vnd.microsoft.icon "now plus 1 month"
	ExpiresByType image/x-icon "now plus 1 month"
	ExpiresByType image/ico "now plus 1 month"
	ExpiresByType image/icon "now plus 1 month"
	ExpiresByType text/ico "now plus 1 month"
	ExpiresByType application/ico "now plus 1 month"
	ExpiresByType image/vnd.wap.wbmp "now plus 1 month"
	ExpiresByType application/vnd.wap.wbxml "now plus 1 month"
	ExpiresByType application/smil "now plus 1 month"
	
	# Audio files expiration: 1 month after request
	ExpiresByType audio/basic "now plus 1 month"
	ExpiresByType audio/mid "now plus 1 month"
	ExpiresByType audio/midi "now plus 1 month"
	ExpiresByType audio/mpeg "now plus 1 month"
	ExpiresByType audio/x-aiff "now plus 1 month"
	ExpiresByType audio/x-mpegurl "now plus 1 month"
	ExpiresByType audio/x-pn-realaudio "now plus 1 month"
	ExpiresByType audio/x-wav "now plus 1 month"
	
	# Movie files expiration: 1 month after request
	ExpiresByType application/x-shockwave-flash "now plus 1 month"
	ExpiresByType x-world/x-vrml "now plus 1 month"
	ExpiresByType video/x-msvideo "now plus 1 month"
	ExpiresByType video/mpeg "now plus 1 month"
	ExpiresByType video/mp4 "now plus 1 month"
	ExpiresByType video/quicktime "now plus 1 month"
	ExpiresByType video/x-la-asf "now plus 1 month"
	ExpiresByType video/x-ms-asf "now plus 1 month"
</IfModule>
##### Optimal default expiration time - END


END;
		}

		if(!empty($config->hoggeragents) && ($config->nohoggers == 1))
		{
			$htaccess .= "##### Common hacking tools and bandwidth hoggers block -- BEGIN\n";
			foreach($config->hoggeragents as $agent)
			{
				$htaccess .= "SetEnvIf user-agent \"$agent\" stayout=1\n";
			}
			$htaccess .= "deny from env=stayout\n";
			$htaccess .= "##### Common hacking tools and bandwidth hoggers block -- END\n\n";
		}

		if( ($config->autocompress == 1) && ($serverCaps->deflate) )
		{
			$htaccess .= <<<ENDHTCODE
##### Automatic compression of resources -- BEGIN
<ifmodule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/xml application/xhtml+xml application/rss+xml application/javascript application/x-javascript
</ifmodule>
<ifModule mod_gzip.c>
mod_gzip_on Yes
mod_gzip_dechunk Yes
mod_gzip_keep_workfiles No
mod_gzip_can_negotiate Yes
mod_gzip_add_header_count Yes
mod_gzip_send_vary Yes
mod_gzip_min_http 1000
mod_gzip_minimum_file_size 300
mod_gzip_maximum_file_size 512000
mod_gzip_maximum_inmem_size 60000
mod_gzip_handle_methods GET
mod_gzip_item_include file \.(html?|txt|css|js|php|pl|xml|rb|py)$
mod_gzip_item_include mime ^text/plain$
mod_gzip_item_include mime ^text/html$
mod_gzip_item_include mime ^text/xml$
mod_gzip_item_include mime ^text/css$
mod_gzip_item_include mime ^application/xml$
mod_gzip_item_include mime ^application/xhtml+xml$
mod_gzip_item_include mime ^application/rss+xml$
mod_gzip_item_include mime ^application/javascript$
mod_gzip_item_include mime ^application/x-javascript$
mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
mod_gzip_item_include handler ^cgi-script$
mod_gzip_item_include handler ^server-status$
mod_gzip_item_include handler ^server-info$
mod_gzip_item_include handler ^application/x-httpd-php
mod_gzip_item_exclude mime ^image/.*
</ifmodule>
##### Automatic compression of resources -- END

ENDHTCODE;
		}

		if($config->autoroot)
			$htaccess .= <<<END
##### Redirect index.php to / -- BEGIN
RewriteCond %{THE_REQUEST} !^POST
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.php\ HTTP/
RewriteCond %{SERVER_PORT}>s ^(443>(s)|[0-9]+>s)$
RewriteRule ^index\.php$ http%2://{$config->httphost}/ $redirCode
##### Redirect index.php to / -- END

END;

		switch($config->wwwredir)
		{
			case 1:
				// non-www to www
				$htaccess .= <<<END
##### Redirect non-www to www -- BEGIN
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 $redirCode
##### Redirect non-www to www -- END


END;
				break;

			case 2:
				// www to non-www
				$htaccess .= <<<END
##### Redirect www to non-www -- BEGIN
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 $redirCode
##### Redirect www to non-www -- END


END;
				break;
		}

		if(!empty($config->olddomain))
		{
			$htaccess .= "##### Redirect old to new domain -- BEGIN\n";
			$domains = trim($config->olddomain);
			$domains = explode(',', $domains);
			$newdomain = $config->httphost;
			foreach($domains as $olddomain)
			{
				$olddomain = trim($olddomain);
				if(empty($olddomain)) continue;

				$olddomain = $this->escape_string_for_regex($olddomain);
				$htaccess .= <<<END
RewriteCond %{HTTP_HOST} ^$olddomain [NC]
RewriteRule (.*) http://$newdomain/$1 $redirCode

END;
			}
			$htaccess .= "##### Redirect old to new domain -- END\n\n";
		}

		if(!empty($config->httpsurls))
		{
			$htaccess .= "##### Force HTTPS for certain pages -- BEGIN\n";
			foreach($config->httpsurls as $url)
			{
				$urlesc = $this->escape_string_for_regex($url);
				$htaccess .= <<<END
RewriteCond %{HTTPS} ^off$ [NC,OR]
RewriteCond %{SERVER_PORT} !^443$
RewriteRule ^$urlesc$ https://{$config->httpshost}/$url $redirCode

END;
			}
			$htaccess .= "##### Force HTTPS for certain pages -- END\n\n";
		}

		$htaccess .= <<<END
##### Rewrite rules to block out some common exploits -- BEGIN
RewriteCond %{QUERY_STRING} proc/self/environ [OR]
RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|\%3D) [OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code\(.*\) [OR]
RewriteCond %{QUERY_STRING} (<|%3C).*script.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
RewriteRule .* index.php [F]
##### Rewrite rules to block out some common exploits -- END

END;

		if($config->fileinj == 1)
		{
			$htaccess .= <<<END
##### File injection protection -- BEGIN
RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http:// [OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC]
RewriteRule .* - [F]
##### File injection protection -- END


END;
		}

		$htaccess .= "##### Advanced server protection rules exceptions -- BEGIN\n";

		if(!empty($config->exceptionfiles))
		{
			foreach($config->exceptionfiles as $file)
			{
				$file = $this->escape_string_for_regex($file);
				$htaccess .= <<<END
RewriteRule ^$file$ - [L]

END;
			}
		}

		if(!empty($config->exceptiondirs))
		{
			foreach($config->exceptiondirs as $dir)
			{
				$dir = trim($dir,'/');
				$dir = $this->escape_string_for_regex($dir);
				$htaccess .= <<<END
RewriteCond %{REQUEST_FILENAME} !(\.php)$
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^$dir/ - [L]

END;
			}
		}

		if(!empty($config->fullaccessdirs))
		{
			foreach($config->fullaccessdirs as $dir)
			{
				$dir = trim($dir,'/');
				$dir = $this->escape_string_for_regex($dir);
				$htaccess .= <<<END
RewriteRule ^$dir/ - [L]

END;
			}
		}

		$htaccess .= "##### Advanced server protection rules exceptions -- END\n\n";

		$htaccess .= "##### Advanced server protection -- BEGIN\n\n";

		if($config->phpeaster == 1)
		{
			$htaccess .= <<<END
RewriteCond %{QUERY_STRING} \=PHP[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12} [NC]
RewriteRule .* - [F]

END;
		}

		if($config->backendprot == 1)
		{
			$bedirs = implode('|',$config->bepexdirs);
			$betypes = implode('|',$config->bepextypes);
			$htaccess .= <<<END
## Back-end protection
RewriteRule ^administrator/?$ - [L]
RewriteRule ^administrator/index\.(php|html?)$ - [L]
RewriteRule ^administrator/index[23]\.php$ - [L]
RewriteRule ^administrator/($bedirs)/.*\.($betypes)$ - [L]
RewriteRule ^administrator/ - [F]

END;
		}

		if($config->allowxmlrpc == 1)
		{
			$htaccess .= <<<END
## Explicitly allow access only to XML-RPC's xmlrpc/index.php or plain xmlrpc/ directory
RewriteRule ^xmlrpc/(index\.php)?$ - [L]
RewriteRule ^xmlrpc/ - [F]

END;
		}

		if($config->frontendprot == 1)
		{
			$fedirs = implode('|',$config->fepexdirs);
			$fetypes = implode('|',$config->fepextypes);
			$htaccess .= <<<END
## Allow limited access for certain Joomla! system directories with client-accessible content
RewriteRule ^($fedirs)/.*\.($fetypes)$ - [L]
RewriteRule ^($fedirs)/.*index\.php - [L]
RewriteRule ^($fedirs)/ - [F]
## Disallow front-end access for certain Joomla! system directories (unless access to their files is allowed above)
RewriteRule ^includes/js/ - [L]
RewriteRule ^(cache|includes|language|logs|tmp)/ - [F]

## Disallow access to rogue PHP files throughout the site, unless they are explicitly allowed
RewriteCond %{REQUEST_FILENAME} (\.php)$
RewriteCond %{REQUEST_FILENAME} !(/index[23]?\.php)$
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule (.*\.php)$ - [F]

END;
		}

		if($config->leftovers == 1)
		{
			$htaccess .= <<<END
## Disallow access to htaccess.txt, php.ini and configuration.php-dist
RewriteRule ^(htaccess\.txt|configuration\.php-dist|php\.ini)$ - [F]

END;
		}

		$htaccess .= "##### Advanced server protection -- END\n\n";

		$htaccess .= <<<END
##### Joomla! core SEF Section -- BEGIN
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{REQUEST_URI} !^/index\.php
RewriteCond %{REQUEST_URI} /component/|(/[^.]*|\.(php|html?|feed|pdf|raw|ini|zip|json|file|vcf))$ [NC]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* index.php [L]
##### Joomla! core SEF Section -- END


END;

		$htaccess .= "\n\n".$config->custfoot."\n";

		return $htaccess;
	}

	public function writeHtaccess()
	{
		$htaccess = $this->makeHtaccess();

		jimport('joomla.filesystem.file');
		JFile::copy('.htaccess','.htaccess.admintools', JPATH_ROOT);
		return JFile::write(JPATH_ROOT.DS.'.htaccess', $htaccess);
	}

	private function escape_string_for_regex($str)
	{
		//All regex special chars (according to arkani at iol dot pt below):
		// \ ^ . $ | ( ) [ ]
		// * + ? { } ,

		$patterns = array('/\//', '/\^/', '/\./', '/\$/', '/\|/',
		'/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/',
		'/\?/', '/\{/', '/\}/', '/\,/');
		$replace = array('\/', '\^', '\.', '\$', '\|', '\(', '\)',
		'\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,');

		return preg_replace($patterns,$replace, $str);
	}
	
	/**
	 * Guesses and returns the Apache version family.
	 * @return string 1.1, 1.3, 2.0, 2.2 or 0.0 (if no match)
	 */
	private function apacheVersion()
	{
		// Get the server string
		$serverString = $_SERVER['SERVER_SOFTWARE'];
		// Not defined? Assume Apache 2.0.
		if(empty($serverString)) return '2.0';
		// Not Apache? Return 0.0
		if(strtoupper(substr($serverString,0,6)) !== 'APACHE') {
			return '0.0';
		}
		// No slash after Apache? Assume 2.0
		if(strlen($serverString) < 7) return '2.0';
		if(substr($serverString,6,1) != '/') return '2.0';
		// Strip part past the version string
		$serverString = substr($serverString, 7, strpos($serverString,' ')-6);
		
		$v = substr($serverString,0,3);
		switch($v) {
			case '1.3':
			case '2.0':
			case '2.2':
				return $v;
				break;
			default:
				if(version_compare($v,'1.3','lt')) {
					return '1.1';
				} else {
					return '2.2';
				}
				break;
		}
		
		return $serverString;
	}
}