<?php
/**
 * @copyright 2011-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param CategoryList $this->categoryList
 */
echo "<services>";
foreach ($this->categoryList as $category) {
	$service_code = "{$category->getId()}";
	$service_name = self::escape($category->getName());
	$group        = self::escape($category->getCategoryGroup());
	$description  = self::escape($category->getDescription());
	$metadata     = $category->getCustomFields() ? 'true' : 'false';
	$featured     = $category->isFeatured() ? 'featured="true"' : '';


	echo "
	<service $featured>
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
