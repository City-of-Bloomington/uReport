<?php
/**
 * @copyright 2006-2026 City of Bloomington, Indiana
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
    public function __construct(?array $vars=null)
    {
        if (defined('THEME')) {
            $dir = SITE_HOME.'/Themes/'.THEME;

            if (is_dir($dir)) {
                $this->theme = $dir;
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
    public function __isset(string $key): bool
    {
        return array_key_exists($key,$this->vars);
    }

    /**
     * Cleans strings for output
     *
     * There are more bad characters than htmlspecialchars deals with.  We just want
     * to add in some other characters to clean.  While here, we might as well
     * have it trim out the whitespace too.
     */
    public static function escape(array|string|null $input, int $quotes=ENT_QUOTES): ?string
    {
        if ($input) {
            if (is_array($input)) {
                foreach ($input as $key=>$value) {
                    $input[$key] = self::escape($value,$quotes);
                }
            }
            else {
                $input = htmlspecialchars(trim($input), $quotes, 'UTF-8');
            }
        }

        return $input;
    }

    /**
     * Reverses the escaping done by View::escape()
     */
    public static function unescape(array|string|null $input): ?string
    {
        if ($input) {
            if (is_array($input)) {
                foreach ($input as $key=>$value) {
                    $input[$key] = self::unescape($value);
                }
            }
            else {
                $input = htmlspecialchars_decode(trim($input), ENT_QUOTES);
            }
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
     */
    public function translate(array|string $msgid, ?string $domain=null): string
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
    public function _(array|string $msgid, ?string $domain=null): string
    {
        return $this->translate($msgid, $domain);
    }

    public static $supportedDateFormatStrings = [
        'm', 'n', 'd', 'j', 'Y', 'H', 'g', 'i', 's', 'a'
    ];

    /**
     * Converts the PHP date format string syntax into something for humans
     */
    public static function translateDateString(string $format): string
    {
        return str_replace(
            self::$supportedDateFormatStrings,
            ['mm', 'mm', 'dd', 'dd', 'yyyy', 'hh', 'hh', 'mm', 'ss', 'am'],
            $format
        );
    }

    public static function convertDateFormat(string $format, string $syntax): ?string
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
        return null;
    }

    /**
     * Creates a URI for a named route
     *
     * This imports the $ROUTES global variable and calls the
     * generate function on it.
     *
     * @see https://github.com/auraphp/Aura.Router/tree/2.x
     */
    public static function generateUri(string $route_name, array $params=[]): string
    {
        global $ROUTES;
        return $ROUTES->generate($route_name, $params);
    }
    public static function generateUrl(string $route_name, array $params=[]): string
    {
        return "$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]".self::generateUri($route_name, $params);
    }
}
