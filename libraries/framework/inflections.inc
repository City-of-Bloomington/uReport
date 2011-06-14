<?php
/* SVN FILE: $Id: inflections.php 3028 2006-06-08 02:18:18Z phpnut $ */
/**
 * Custom Inflected Words.
 *
 * This file is used to hold words that are not matched in the normail Inflector::pluralize() and
 * Inflector::singularize()
 *
 * Orinally written by the CakePHP Project
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Modified to work in City of Bloomington's Framework
 * Relicensed under GPL
 * Redistributions must retain all copyright notices
 * @copyright 2006 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
/**
 * This is a key => value array of regex used to match words.
 * If key matches then the value is returned.
 *
 *  $pluralRules = array('/(s)tatus$/i' => '\1\2tatuses', '/^(ox)$/i' => '\1\2en', '/([m|l])ouse$/i' => '\1ice');
 */
	$pluralRules = array();
/**
 * This is a key only array of plural words that should not be inflected.
 * Notice the last comma
 *
 * $uninflectedPlural = array('.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox');
 */
	$uninflectedPlural = array();
/**
 * This is a key => value array of plural irregular words.
 * If key matches then the value is returned.
 *
 *  $irregularPlural = array('atlas' => 'atlases', 'beef' => 'beefs', 'brother' => 'brothers')
 */
	$irregularPlural = array();
/**
 * This is a key => value array of regex used to match words.
 * If key matches then the value is returned.
 *
 *  $singularRules = array('/(s)tatuses$/i' => '\1\2tatus', '/(matr)ices$/i' =>'\1ix','/(vert|ind)ices$/i')
 */
	$singularRules = array();
/**
 * This is a key only array of singular words that should not be inflected.
 * You should not have to change this value below if you do change it use same format
 * as the $uninflectedPlural above.
 */
	$uninflectedSingular = $uninflectedPlural;
/**
 * This is a key => value array of singular irregular words.
 * Most of the time this will be a reverse of the above $irregularPlural array
 * You should not have to change this value below if you do change it use same format
 *
 * $irregularSingular = array('atlases' => 'atlas', 'beefs' => 'beef', 'brothers' => 'brother')
 */
	$irregularSingular = array_flip($irregularPlural);
?>