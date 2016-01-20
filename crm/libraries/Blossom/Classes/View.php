<?php
/**
 * @copyright 2006-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;
use Zend\I18n\Translator\Translator;

abstract class View
{
	protected $vars = array();
	private static $translator;

	abstract public function render();

	/**
	 * Instantiates the Zend Translator
	 *
	 * See: ZendFramework documentation for full information
	 * http://framework.zend.com/manual/2.2/en/modules/zend.i18n.translating.html
	 * @see http://framework.zend.com/manual/2.2/en/modules/zend.i18n.translating.html
	 */
	public function __construct(array $vars=null)
	{
		if (count($vars)) {
			foreach ($vars as $name=>$value) {
				$this->vars[$name] = $value;
			}
		}

		if (!self::$translator) {
			self::$translator = new Translator();
			self::$translator->addTranslationFilePattern(
				'gettext',
				APPLICATION_HOME.'/language',
				'%s.mo'
			);
			self::$translator->setLocale(LOCALE);
		}
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
	 * @param array|string $string
	 * @param CONSTANT $quotes Optional, the desired constant to use for the htmlspecidalchars call
	 * @return string
	 */
	public static function escape($input,$quotes=ENT_QUOTES)
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
	 * Returns the gettext translation of msgid
	 *
	 * For entries in the PO that are plurals, you must pass msgid as an array
	 * $this->translate(array('msgid', 'msgid_plural', $num))
	 *
	 * See: ZendFramework documentation for full information
	 * http://framework.zend.com/manual/2.2/en/modules/zend.i18n.translating.html
	 *
	 * @see http://framework.zend.com/manual/2.2/en/modules/zend.i18n.translating.html
	 * @param mixed $msgid String or Array
	 * @return string
	 */
	public function translate($msgid)
	{
		if (is_array($msgid)) {
			return self::$translator->translatePlural($msgid[0], $msgid[1], $msgid[2]);
		}
		else {
			return self::$translator->translate($msgid);
		}
	}

	/**
	 * Alias of $this->translate()
	 */
	public function _($msgid)
	{
		return $this->translate($msgid);
	}
}
