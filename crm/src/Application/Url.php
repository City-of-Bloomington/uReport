<?php
/**
 * Helper class for URL handling.  Parses URLs and allows adding parameters from variables.
 *
 * $url = new URL('/path/to/webpage.php?initialParameter=whatever');
 * $url->parameters['somevar'] = $somevar;
 * $url->somevar = $somevar;
 * echo $url->getURL();
 *
 * @copyright 2006-2017 City of Bloomington, Indiana.
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application;

class Url
{
	public $scheme;
	public $host;
	public $port;
	public $path;
	public $anchor;

	public $parameters = [];

	/**
	 * Performs an HTTP GET and returns response string
	 *
	 * @param string $url
	 * @return string
	 */
	public static function get($url)
	{
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

		if (substr($url, 0, 5) == 'https://') {
			curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		}
		return curl_exec($request);
	}

	/**
	 * If you are behind a proxy, you can pass in the hostname to use.
	 * Otherwise it will just use $_SERVER[SERVER_NAME] of the localhost
	 *
	 * @param  string $hostname Optional hostname to use
	 * @return string
	 */
	public static function current_url($hostname=null)
	{
        if (!$hostname) { $hostname = $_SERVER['SERVER_NAME']; }

        return "$_SERVER[REQUEST_SCHEME]://$hostname$_SERVER[REQUEST_URI]";
	}

	public function __construct($url)
	{
		$script = urldecode($url);

		// If scheme wasn't provided add one to the start of the string
		if (!strpos(substr($script,0,20),'://')) {
			$scheme = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']==443)
				? 'https'
				: 'http';
			$script = "$scheme://$script";
		}

		$u = parse_url($script);
		$this->scheme = $u['scheme'];
		if (isset($u['host']))     { $this->host   = $u['host'];     }
		if (isset($u['path']))     { $this->path   = $u['path'];     }
		if (isset($u['port']))     { $this->port   = $u['port'];     }
		if (isset($u['fragment'])) { $this->anchor = $u['fragment']; }
		if (isset($u['query'])) { parse_str($u['query'],$this->parameters); }
	}

	/**
	 * Returns just the base portion of the url
	 * @return string
	 */
	public function getScript() {
        $url = "{$this->getScheme()}://{$this->host}";
        if ($this->port) { $url.= ":{$this->port}"; }
        $url.= $this->path;
        return $url;
	}

	/**
	 * Returns the full, properly formatted and escaped URL
	 * @return string
	 */
	public function __toString() {
		return $this->getUrl();
	}

	/**
	 * Returns the full, properly formatted and escaped URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$url = $this->getScript();

		if (count($this->parameters)) {
			$url.= '?'.http_build_query($this->parameters,'');
		}

		if ($this->anchor) {
			$url.= '#'.$this->anchor;
		}
		return $url;
	}

	/**
	 * Returns just the protocol (http://, https://) portion
	 * @return string
	 */
	public function getScheme() {
		if (!$this->scheme) {
			$this->scheme = 'http';
		}
		return $this->scheme;
	}

	/**
	 * Sets the protocol for the URL (http, https)
	 * @param string $protocol
	 */
	public function setScheme($string)
	{
		$string = preg_replace('|://|', '', $string);
		$this->scheme = $string;
	}

	/**
	 * Cleans out any query parameters that had empty values
	 */
	public function purgeEmptyParameters()
	{
		$this->parameters = $this->array_filter_recursive($this->parameters);
	}

	private function array_filter_recursive(array $input)
	{
		foreach ($input as &$value) {
			if (is_array($value)) {
				$value = $this->array_filter_recursive($value);
			}
		}
		return array_filter($input);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		if (isset($this->parameters[$key])) {
			return $this->parameters[$key];
		}
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function __set($key,$value)
	{
		$this->parameters[$key] = $value;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->parameters[$key]);
	}

	/**
	 * @param string $key
	 */
	public function __unset($key)
	{
		unset($this->parameters[$key]);
	}
}
