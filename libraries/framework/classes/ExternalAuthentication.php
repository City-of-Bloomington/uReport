<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

interface ExternalAuthentication
{
	public static function authenticate($username,$password);
	public static function savePassword($username,$password);
}