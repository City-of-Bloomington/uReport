<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
use Blossom\Classes\View;

echo "<services>";
foreach ($this->categoryList as $category) {
	$service_code = "{$category->getId()}";
	$service_name = self::escape($category->getName());
	$group = self::escape($category->getCategoryGroup());
	$description = self::escape($category->getDescription());
	$metadata = count($category->getCustomFields()) ? 'true' : 'false';

	echo "
	<service>
		<service_code>$service_code</service_code>
		<service_name>$service_name</service_name>
		<type>realtime</type>
		<metadata>$metadata</metadata>
		<keywords></keywords>
		<group>$group</group>
		<description>$description</description>
	</service>
	";
}
echo "</services>";
