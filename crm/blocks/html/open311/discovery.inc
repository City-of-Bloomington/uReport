<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Category;

$lastModified = Category::getGlobalLastModifiedDate('c');
?>
<h1>Service Discovery</h1>
<table>
	<tr><th>changeset</th>
		<td><?= $lastModified; ?></td>
	</tr>
	<tr><th>contact</th>
		<td>You can email for assistance <?= ADMINISTRATOR_EMAIL; ?></td>
	</tr>
	<tr><th>key_service</th>
		<td>You can request a key at
		<?php
			echo '<a href="'.OPEN311_KEY_SERVICE.'">'.OPEN311_KEY_SERVICE.'</a>';
		?>
		</td>
	</tr>
</table>
<h2>Endpoints</h2>
<h3>Production</h3>
<table>
	<tr><th>specification</th>
		<td>http://wiki.open311.org/GeoReport_v2</td>
	</tr>
	<tr><th>url</th>
		<td><?= BASE_URL; ?>/open311/v2</td>
	</tr>
	<tr><th>changeset</th>
		<td><?= $lastModified; ?></td>
	</tr>
	<tr><th>type</th><td>production</td></tr>
	<tr><th>formats</th><td>text/xml,application/json</td></tr>
</table>
<h3>Test</h3>
<table>
	<tr><th>specification</th>
		<td>http://wiki.open311.org/GeoReport_v2</td>
	</tr>
	<tr><th>url</th>
		<td>https://bloomington.in.gov/test/open311/v2</td>
	</tr>
	<tr><th>changeset</th>
		<td><?= $lastModified; ?></td>
	</tr>
	<tr><th>type</th><td>test</td></tr>
	<tr><th>formats</th><td>text/xml,application/json</td></tr>
</table>
