<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 */
if (defined('ADDRESS_SERVICE')) {
	$data = AddressService::getLocationData($this->location);
	if (count($data)) {
		echo '<table class="masterAddressData">';
		foreach ($data as $key=>$value) {
			$value = View::escape(trim($value));
			if (array_key_exists($key,AddressService::$customFieldDescriptions)
				&& $value) {
				$label = AddressService::$customFieldDescriptions[$key]['description'];
				echo "<tr><th>$label</th><td>$value</td></tr>";
			}
		}
		echo '</table>';
	}
}
