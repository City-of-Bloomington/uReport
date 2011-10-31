<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
echo "<services>";
foreach ($this->categoryList as $category) {
	$service_code = "{$category->getId()}";
	$service_name = View::escape($category->getName());
	$group = View::escape($category->getGroup());
	$description = View::escape($category->getDescription());
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
