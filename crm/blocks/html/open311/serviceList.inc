<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
use Blossom\Classes\View;

echo "
<div class=\"serviceList\">
	<h1>Services</h1>
	<table>
		<thead>
			<tr><th>service_code</th>
				<th>service_name</th>
				<th>type</th>
				<th>metadata</th>
				<th>keywords</th>
				<th>group</th>
				<th>description</th>
			</tr>
		</thead>
		<tbody>
";
		foreach ($this->categoryList as $category) {
			$service_code = "{$category->getId()}";
			$service_name = self::escape($category->getName());
			$group        = self::escape($category->getCategoryGroup());
			$description  = self::escape($category->getDescription());
			$metadata     = count($category->getCustomFields()) ? 'true' : 'false';

			$url = BASE_URL."/open311/v2/services/$service_code";
			$service_code = $metadata=='true' ? "<a href=\"$url\">$service_code</a>" : $service_code;
			echo "
			<tr><td>$service_code</td>
				<td>$service_name</td>
				<td>realtime</td>
				<td>$metadata</td>
				<td></td>
				<td>$group</td>
				<td>$description</td>
			</tr>
			";
		}
echo "
		</tbody>
	</table>
</div>
";
