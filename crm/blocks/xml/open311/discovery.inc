<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<discovery>
	<changeset>2011-11-29T09:48Z-05</changeset>
	<contact>You can email for assistance <?php echo ADMINISTRATOR_EMAIL; ?></contact>
	<key_service>You can request a key by emailing <?php echo ADMINISTRATOR_EMAIL; ?></key_service>
	<endpoints>
		<endpoint>
			<specification>http://wiki.open311.org/GeoReport_v2</specification>
			<url><?php echo BASE_URL; ?>/open311/v2</url>
			<changeset></changeset>
			<type>development</type>
			<formats>
				<format>text/xml</format>
				<format>application/json</format>
			</formats>
		</endpoint>
	</endpoints>
</discovery>
