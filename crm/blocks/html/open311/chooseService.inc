<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET group
 */
?>
<div class="serviceGroup">
	<h1><?php echo View::escape($_GET['group']); ?></h1>
	<ul>
	<?php
		$url = new URL(BASE_URL.'/open311');
		$services = new CategoryList(array('group'=>$_GET['group']));
		foreach ($services as $service) {
			$url->service_code = "{$service->getId()}";
			$service_name = View::escape($service->getName());
			echo "<li><a href=\"$url\">$service_name</a></li>";
		}
	?>
	</ul>
</div>