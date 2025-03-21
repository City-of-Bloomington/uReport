<?php
/**
 * @copyright 2006-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application;

abstract class View
{
    protected $theme;
    protected $theme_config = [];
	protected $vars         = [];

	abstract public function render();

	/**
	 * Configures the gettext translations
	 */
	public function __construct(array $vars=null)
	{
        if (defined('THEME')) {
            $dir = SITE_HOME.'/Themes/'.THEME;

            if (is_dir($dir)) {
                $this->theme = $dir;
                $config_file = $dir.'/theme_config.inc';

                if (is_file($config_file)) { $this->theme_config = require $config_file; }
            }
        }

		if ($vars) {
			foreach ($vars as $name=>$value) {
				$this->vars[$name] = $value;
			}
		}

        $locale = LOCALE.'.utf8';

        putenv("LC_ALL=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain('labels',   APPLICATION_HOME.'/language');
        bindtextdomain('messages', APPLICATION_HOME.'/language');
        bindtextdomain('errors',   APPLICATION_HOME.'/language');
        textdomain('labels');
	}

	/**
	 * Magic Method for setting object properties
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key,$value) {
		$this->vars[$key] = $value;
	}
	/**
	 * Magic method for getting object properties
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->vars[$key])) {
			return $this->vars[$key];
		}
		return null;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key) {
		return array_key_exists($key,$this->vars);
	}

	/**
	 * Cleans strings for output
	 *
	 * There are more bad characters than htmlspecialchars deals with.  We just want
	 * to add in some other characters to clean.  While here, we might as well
	 * have it trim out the whitespace too.
	 *
	 * @param array|string $input
	 * @param CONSTANT $quotes Optional, the desired constant to use for the htmlspecidalchars call
	 * @return string
	 */
	public static function escape($input, $quotes=ENT_QUOTES)
	{
		if (is_array($input)) {
			foreach ($input as $key=>$value) {
				$input[$key] = self::escape($value,$quotes);
			}
		}
		else {
			$input = htmlspecialchars(trim($input), $quotes, 'UTF-8');
		}

		return $input;
	}

	/**
	 * Reverses the escaping done by View::escape()
	 *
	 * @param array|string $input
	 * @return string
	 */
	public static function unescape($input)
	{
        if (is_array($input)) {
            foreach ($input as $key=>$value) {
                $input[$key] = self::unescape($value);
            }
        }
        else {
            $input = htmlspecialchars_decode(trim($input), ENT_QUOTES);
        }
        return $input;
	}

    /**
     * Returns the gettext translation of msgid
     *
     * The default domain is "labels".  Any other text domains must be passed
     * in the second parameter.
     *
     * For entries in the PO that are plurals, you must pass msgid as an array
     * $this->translate( ['msgid', 'msgid_plural', $num] )
     *
     * @param mixed $msgid String or Array
     * @param string $domain Alternate domain
     * @return string
     */
    public function translate($msgid, $domain=null)
    {
        if (is_array($msgid)) {
            return $domain
                ? dngettext($domain, $msgid[0], $msgid[1], $msgid[2])
                : ngettext (         $msgid[0], $msgid[1], $msgid[2]);
        }
        else {
            return $domain
                ? dgettext($domain, $msgid)
                : gettext (         $msgid);
        }
    }

    /**
     * Alias of $this->translate()
     */
    public function _($msgid, $domain=null)
    {
        return $this->translate($msgid, $domain);
    }

    public static $supportedDateFormatStrings = [
        'm', 'n', 'd', 'j', 'Y', 'H', 'g', 'i', 's', 'a'
    ];

    /**
     * Converts the PHP date format string syntax into something for humans
     *
     * @param string $format
     * @return string
     */
    public static function translateDateString($format)
    {
        return str_replace(
            self::$supportedDateFormatStrings,
            ['mm', 'mm', 'dd', 'dd', 'yyyy', 'hh', 'hh', 'mm', 'ss', 'am'],
            $format
        );
    }

    public static function convertDateFormat($format, $syntax)
    {
        $languages = [
            'mysql'  => ['%m', '%c', '%d', '%e', '%Y', '%H', '%l', '%i', '%s', '%p'],
            'jquery' => ['mm', 'm',  'dd', 'd',  'yy', 'HH', 'h',  'mm', 'ss', 'a' ]
        ];

        if (array_key_exists($syntax, $languages)) {
            return str_replace(
                self::$supportedDateFormatStrings,
                $languages[$syntax],
                $format
            );
        }
    }

    /**
     * Creates a URI for a named route
     *
     * This imports the $ROUTES global variable and calls the
     * generate function on it.
     *
     * @see https://github.com/auraphp/Aura.Router/tree/2.x
     * @param string $route_name
     * @param array $params
     * @return string
     */
    public static function generateUri($route_name, $params=[])
    {
        global $ROUTES;
        return $ROUTES->generate($route_name, $params);
    }
    public static function generateUrl($route_name, $params=[])
    {
        return "$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]".self::generateUri($route_name, $params);
    }
}
