<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$lastModified = '2011-12-19T09:02-05';
?>
<discovery>
	<changeset><?php echo $lastModified; ?></changeset>
	<contact>You can email for assistance <?php echo ADMINISTRATOR_EMAIL; ?></contact>
	<key_service>You can request a key by emailing <?php echo ADMINISTRATOR_EMAIL; ?></key_service>
	<endpoints>
		<endpoint>
			<specification>http://wiki.open311.org/GeoReport_v2</specification>
			<url>https://bloomington.in.gov/ureport/open311/v2</url>
			<changeset><?php echo $lastModified; ?></changeset>
			<type>production</type>
			<formats>
				<format>text/xml</format>
				<format>application/json</format>
			</formats>
		</endpoint>
		<endpoint>
			<specification>http://wiki.open311.org/GeoReport_v2</specification>
			<url>https://bloomington.in.gov/test/open311/v2</url>
			<changeset><?php echo $lastModified; ?></changeset>
			<type>test</type>
			<formats>
				<format>text/xml</format>
				<format>application/json</format>
			</formats>
		</endpoint>
	</endpoints>
</discovery>
