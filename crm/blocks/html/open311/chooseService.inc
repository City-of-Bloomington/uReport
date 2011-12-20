<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET group
 */
$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url->group = $_GET['group'];
?>
<div class="serviceChooser">
	<h1><a href="<?php echo $url; ?>">
			<?php echo View::escape($_GET['group']); ?>
		</a>
	</h1>
	<ul>
	<?php
		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$services = new CategoryList(array('group'=>$_GET['group']));
		foreach ($services as $service) {
			$url->service_code = "{$service->getId()}";
			$service_name = View::escape($service->getName());
			echo "<li><a href=\"$url\">$service_name</a></li>";
		}
	?>
	</ul>
</div>