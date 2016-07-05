<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->location
 */
use Application\Models\AddressService;

if (defined('ADDRESS_SERVICE')) {
	$data = AddressService::getLocationData($this->location);
	if (count($data)) {
		foreach ($data as $key=>$value) {
			$value = self::escape(trim($value));
			if (array_key_exists($key, AddressService::$customFieldDescriptions)
				&& $value) {
				$label = AddressService::$customFieldDescriptions[$key]['description'];
				echo "<dl><dt>$label</dt><dd>$value</dd></dl>";
			}
		}
	}
}
