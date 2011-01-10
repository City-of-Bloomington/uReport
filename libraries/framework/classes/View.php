<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class View
{
	protected $vars = array();

	abstract public function render();

	/**
	 * Magic Method for setting object properties
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key,$value) {
		$this->vars[$key] = $value;
	}
	/**
	 * Magic method for getting object properties
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
			$input = htmlspecialchars(trim($input),$quotes);
		}

		return $input;
	}

	/**
	 * Return the first $n words of the given string
	 *
	 * @param string $string Source string
	 * @param int $numWords Number of words
	 * @return string
	 */
	public static function limitWords($string,$numWords)
	{
		$output = '';
		$words = preg_split('/\s+/',$string);
		$c = 0;
		foreach ($words as $word) {
			$output.= "$word ";
			$c++;
			if ($c >= $numWords) {
				$output.= '...';
				break;
			}
		}
		return $output;
	}
}
