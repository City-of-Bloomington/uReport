<?php
/**
 * Pluralize and singularize English words.
 *
 * Orinally written by the CakePHP Project
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-02 00:33:52 -0600 (Wed, 02 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Modified to work in City of Bloomington's Framework
 * Relicensed under GPL
 * Redistributions must retain all copyright notices
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class Inflector
{
	/**
	 * Return $word in plural form.
	 *
	 * @param string $word Word in singular
	 * @return string Word in plural
	 */
	public static function pluralize($word) {
		$corePluralRules = array('/(s)tatus$/i' => '\1\2tatuses',
									'/(quiz)$/i' => '\1zes',
									'/^(ox)$/i' => '\1\2en',
									'/([m|l])ouse$/i' => '\1ice',
									'/(matr|vert|ind)(ix|ex)$/i'  => '\1ices',
									'/(x|ch|ss|sh)$/i' => '\1es',
									'/([^aeiouy]|qu)y$/i' => '\1ies',
									'/(hive)$/i' => '\1s',
									'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
									'/sis$/i' => 'ses',
									'/([ti])um$/i' => '\1a',
									'/(p)erson$/i' => '\1eople',
									'/(m)an$/i' => '\1en',
									'/(c)hild$/i' => '\1hildren',
									'/(buffal|tomat)o$/i' => '\1\2oes',
									'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
									'/us$/' => 'uses',
									'/(alias)$/i' => '\1es',
									'/(ax|cri|test)is$/i' => '\1es',
									'/s$/' => 's',
									'/$/' => 's',);

		$coreUninflectedPlural = array('.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', 'Amoyese',
											'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus', 'carp', 'chassis', 'clippers',
											'cod', 'coitus', 'Congoese', 'contretemps', 'corps', 'debris', 'diabetes', 'djinn', 'eland', 'elk',
											'equipment', 'Faroese', 'flounder', 'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
											'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings', 'jackanapes', 'Kiplingese',
											'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media', 'mews', 'moose', 'mumps', 'Nankingese', 'news',
											'nexus', 'Niasese', 'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese', 'proceedings',
											'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors', 'sea[- ]bass', 'series', 'Shavese', 'shears',
											'siemens', 'species', 'swine', 'testes', 'trousers', 'trout', 'tuna', 'Vermontese', 'Wenchowese',
											'whiting', 'wildebeest', 'Yengeese',);

		$coreIrregularPlural = array('atlas' => 'atlases',
										'beef' => 'beefs',
										'brother' => 'brothers',
										'child' => 'children',
										'corpus' => 'corpuses',
										'cow' => 'cows',
										'ganglion' => 'ganglions',
										'genie' => 'genies',
										'genus' => 'genera',
										'graffito' => 'graffiti',
										'hoof' => 'hoofs',
										'loaf' => 'loaves',
										'man' => 'men',
										'money' => 'monies',
										'mongoose' => 'mongooses',
										'move' => 'moves',
										'mythos' => 'mythoi',
										'numen' => 'numina',
										'occiput' => 'occiputs',
										'octopus' => 'octopuses',
										'opus' => 'opuses',
										'ox' => 'oxen',
										'penis' => 'penises',
										'person' => 'people',
										'sex' => 'sexes',
										'soliloquy' => 'soliloquies',
										'testis' => 'testes',
										'trilby' => 'trilbys',
										'turf' => 'turfs',);

		$pluralRules = $corePluralRules;
		$uninflected = $coreUninflectedPlural;
		$irregular = $coreIrregularPlural;

		if (file_exists(FRAMEWORK.'/includes/inflections.inc')) {
			include(FRAMEWORK.'/includes/inflections.inc');
			$pluralRules = array_merge($pluralRules, $corePluralRules);
			$uninflected = array_merge($uninflectedPlural, $coreUninflectedPlural);
			$irregular = array_merge($irregularPlural, $coreIrregularPlural);
		}
		$regexUninflected = self::enclose(join( '|', $uninflected));
		$regexIrregular = self::enclose(join( '|', array_keys($irregular)));

		if (preg_match('/^(' . $regexUninflected . ')$/i', $word, $regs)) {
			return $word;
		}

		if (preg_match('/(.*)\\b(' . $regexIrregular . ')$/i', $word, $regs)) {
			return $regs[1] . $irregular[strtolower($regs[2])];
		}

		foreach ($pluralRules as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				$replace = preg_replace($rule, $replacement, $word);
				return $replace;
			}
		}
		return $word;
	}
	/**
	 * Return $word in singular form.
	 *
	 * @param string $word Word in plural
	 * @return string Word in singular
	 */
	public static function singularize($word) {
		$coreSingularRules = array('/(s)tatuses$/i' => '\1\2tatus',
									'/^(.*)(menu)s$/i' => '\1\2',
									'/(quiz)zes$/i' => '\\1',
									'/(matr)ices$/i' => '\1ix',
									'/(vert|ind)ices$/i' => '\1ex',
									'/^(ox)en/i' => '\1',
									'/(alias)(es)*$/i' => '\1',
									'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
									'/(cris|ax|test)es$/i' => '\1is',
									'/(shoe)s$/i' => '\1',
									'/(o)es$/i' => '\1',
									'/ouses$/' => 'ouse',
									'/uses$/' => 'us',
									'/([m|l])ice$/i' => '\1ouse',
									'/(x|ch|ss|sh)es$/i' => '\1',
									'/(m)ovies$/i' => '\1\2ovie',
									'/(s)eries$/i' => '\1\2eries',
									'/([^aeiouy]|qu)ies$/i' => '\1y',
									'/([lr])ves$/i' => '\1f',
									'/(tive)s$/i' => '\1',
									'/(hive)s$/i' => '\1',
									'/(drive)s$/i' => '\1',
									'/([^f])ves$/i' => '\1fe',
									'/(^analy)ses$/i' => '\1sis',
									'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
									'/([ti])a$/i' => '\1um',
									'/(p)eople$/i' => '\1\2erson',
									'/(m)en$/i' => '\1an',
									'/(c)hildren$/i' => '\1\2hild',
									'/(n)ews$/i' => '\1\2ews',
									'/^(.*us)$/' => '\\1',
									'/s$/i' => '');

		$coreUninflectedSingular = array('.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', '.*ss', 'Amoyese',
											'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus', 'carp', 'chassis', 'clippers',
											'cod', 'coitus', 'Congoese', 'contretemps', 'corps', 'debris', 'diabetes', 'djinn', 'eland', 'elk',
											'equipment', 'Faroese', 'flounder', 'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
											'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings', 'jackanapes', 'Kiplingese',
											'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media', 'mews', 'moose', 'mumps', 'Nankingese', 'news',
											'nexus', 'Niasese', 'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese', 'proceedings',
											'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors', 'sea[- ]bass', 'series', 'Shavese', 'shears',
											'siemens', 'species', 'swine', 'testes', 'trousers', 'trout', 'tuna', 'Vermontese', 'Wenchowese',
											'whiting', 'wildebeest', 'Yengeese',);

		$coreIrregularSingular = array('atlases' => 'atlas',
										'beefs' => 'beef',
										'brothers' => 'brother',
										'children' => 'child',
										'corpuses' => 'corpus',
										'cows' => 'cow',
										'ganglions' => 'ganglion',
										'genies' => 'genie',
										'genera' => 'genus',
										'graffiti' => 'graffito',
										'hoofs' => 'hoof',
										'loaves' => 'loaf',
										'men' => 'man',
										'monies' => 'money',
										'mongooses' => 'mongoose',
										'moves' => 'move',
										'mythoi' => 'mythos',
										'numina' => 'numen',
										'occiputs' => 'occiput',
										'octopuses' => 'octopus',
										'opuses' => 'opus',
										'oxen' => 'ox',
										'penises' => 'penis',
										'people' => 'person',
										'sexes' => 'sex',
										'soliloquies' => 'soliloquy',
										'testes' => 'testis',
										'trilbys' => 'trilby',
										'turfs' => 'turf',);

		$singularRules = $coreSingularRules;
		$uninflected = $coreUninflectedSingular;
		$irregular = $coreIrregularSingular;

		if (file_exists(FRAMEWORK.'/includes/inflections.inc')) {
			include(FRAMEWORK.'/includes/inflections.inc');
			$singularRules = array_merge($singularRules, $coreSingularRules);
			$uninflected = array_merge($uninflectedSingular, $coreUninflectedSingular);
			$irregular = array_merge($irregularSingular, $coreIrregularSingular);
		}
		$regexUninflected = self::enclose(join( '|', $uninflected));
		$regexIrregular = self::enclose(join( '|', array_keys($irregular)));

		if (preg_match('/^(' . $regexUninflected . ')$/i', $word, $regs)) {
			return $word;
		}

		if (preg_match('/(.*)\\b(' . $regexIrregular . ')$/i', $word, $regs)) {
			return $regs[1] . $irregular[strtolower($regs[2])];
		}

		foreach ($singularRules as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				$replace = preg_replace($rule, $replacement, $word);
				return $replace;
			}
		}
		return $word;
	}
/**
 * Returns given $lower_case_and_underscored_word as a camelCased word.
 *
 * @param string $lower_case_and_underscored_word Word to camelize
 * @return string Camelized word. likeThis.
 */
	public static function camelize($lowerCaseAndUnderscoredWord) {
		$replace = str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
		return $replace;
	}
/**
 * Returns an underscore-syntaxed ($like_this_dear_reader) version of the $camel_cased_word.
 *
 * @param string $camel_cased_word Camel-cased word to be "underscorized"
 * @return string Underscore-syntaxed version of the $camel_cased_word
 */
	public static function underscore($camelCasedWord) {
		$replace = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
		return $replace;
	}
/**
 * Returns a human-readable string from $lower_case_and_underscored_word,
 * by replacing underscores with a space, and by upper-casing the initial characters.
 *
 * @param string $lower_case_and_underscored_word String to be made more readable
 * @return string Human-readable string
 */
	public static function humanize($lowerCaseAndUnderscoredWord) {
		$replace = ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord));
		return $replace;
	}
/**
 * Returns corresponding table name for given $class_name. ("posts" for the model class "Post").
 *
 * @param string $class_name Name of class to get database table name for
 * @return string Name of the database table for given class
 */
	public static function tableize($className) {
		$replace = self::pluralize(self::underscore($className));
		return $replace;
	}
/**
 * Returns Cake model class name ("Post" for the database table "posts".) for given database table.
 *
 * @param string $tableName Name of database table to get class name for
 * @return string
 */
	public static function classify($tableName) {
		$replace = self::camelize(self::singularize($tableName));
		return $replace;
	}

/**
 * Returns camelBacked version of a string.
 *
 * @param string $string
 * @return string
 * @access public
 * @static
 */
	public static function variable($string) {
		$string = self::camelize(self::underscore($string));
		$replace = strtolower(substr($string, 0, 1));
		$variable = preg_replace('/\\w/', $replace, $string, 1);
		return $variable;
	}
/**
 * Returns a string with all spaces converted to $replacement and non word characters removed.
 *
 * @param string $string
 * @param string $replacement
 * @return string
 * @access public
 * @static
 */
	public static function slug($string, $replacement = '_') {
		$string = preg_replace(array('/[^\w\s]/', '/\\s+/') , array(' ', $replacement), $string);
		return $string;
	}

/**
 * Enclose a string for preg matching.
 *
 * @param string $string String to enclose
 * @return string Enclosed string
 */
	public static function enclose($string) {
		return '(?:' . $string . ')';
	}
}
?>
