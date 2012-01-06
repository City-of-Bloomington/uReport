<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$jurisdiction_id = !empty($_REQUEST['jurisdiction_id'])
	? View::escape($_REQUEST['jurisdiction_id'])
	: OPEN311_JURISDICTION;

$api_key = !empty($_REQUEST['api_key'])
	? View::escape($_REQUEST['api_key'])
	: '';

$service = new Category($_GET['service_code']);
$service_code = "{$service->getId()}";
$service_name = View::escape($service->getName());
$service_description = View::escape($service->getDescription());

$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
if (isset($url->parameters['group']))			{ unset($url->parameters['group']); }
if (isset($url->parameters['service_code']))	{ unset($url->parameters['service_code']); }
?>
<div class="breadcrumbs">
	<ul><li><a href="<?php echo $url; ?>">All Reports</a></li>
		<li><?php echo $service_name; ?></li>
	</ul>
</div>
<div class="clientRequestForm">
	<h1><?php echo $service_name; ?></h1>
	<p class="service_description"><?php echo $service_description; ?></p>

	<?php $url->parameters = array(); ?>
	<form method="post" action="<?php echo $url; ?>">
	<fieldset><legend>Contact Information</legend>
			<table>
				<tr><td><label for="first_name">First Name</label></td>
					<td><input name="first_name" id="first_name" /></td>
				</tr>
				<tr><td><label for="last_name">Last Name</label></td>
					<td><input name="last_name" id="last_name" /></td>
				</tr>
				<tr><td><label for="phone">Phone</label></td>
					<td><input name="phone" id="phone" /></td>
				</tr>
				<tr><td><label for="email">Email</label></td>
					<td><input name="email" id="email" /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset><legend>Location</legend>
		<p>If your report involves a specific area or place, please select it using the map.</p>
		<div>
							<span id="location"></span>
							<span class="button"><span class="add"><a id="openMapButton">Open Map</a></span></span>
						</div>
						<div id="map_overlay"></div>
						<input name="address_string" id="address_string" type="hidden" />
						<input name="lat" id="lat" type="hidden" />
						<input name="long" id="long" type="hidden" />
		
		</fieldset>
		<fieldset><legend>Report Details</legend>
			<input name="jurisdiction_id" type="hidden" value="<?php echo $jurisdiction_id; ?>" />
			<input name="api_key" type="hidden" value="<?php echo $api_key; ?>" />
			<input name="service_code" type="hidden" value="<?php echo $service_code; ?>" />

			<div>
				<div>
					<label for="description">Description</label>
				</div>
				<textarea name="description" id="description"></textarea>
			</div>
		</fieldset>
	
		<?php
			$customFields = $service->getCustomFields();
			if ($customFields) {
				echo "
				<fieldset><legend>Additional Information</legend>
					<table>
				";
				foreach ($customFields as $field) {
					$name = View::escape($field['name']);
					$label = isset($field['label']) ? View::escape($field['label']) : $name;
					$type = isset($field['type']) ? $field['type'] : 'string';

					$inputHTML = '';
					switch ($type) {
						case 'singlevaluelist':
							$inputHTML = "<select name=\"attribute[$name]\" id=\"$name\"><option></option>";
							foreach ($field['values'] as $value) {
								$value = View::escape($value);
								$inputHTML.= "<option>$value</option>";
							}
							$inputHTML.= "</select>";
							break;

						case 'multivaluelist':
							$inputHTML = "<select name=\"attribute[$name][]\" id=\"$name\" multiple=\"multiple\">";
							foreach ($field['values'] as $value) {
								$value = View::escape($value);
								$inputHTML.= "<option>$value</option>";
							}
							$inputHTML.= "</select>";
							break;

						case 'text':
							$inputHTML = "<textarea name=\"attribute[$name]\" id=\"$name\"></textarea>";
							break;

						default:
							$inputHTML = "<input name=\"attribute[$name]\" id=\"$name\" />";
					}
					echo "
					<tr><td><label for=\"$name\">$label</label></td>
						<td>$inputHTML</td>
					</tr>
					";
				}
				echo "
					</table>
				</fieldset>
				";
			}
		?>
			<?php
				echo $this->formButton('Submit','submit','submit');
			?>
	</form>
</div>
<script type="text/javascript">
	var DEFAULT_LATITUDE = <?php echo DEFAULT_LATITUDE; ?>,
		DEFAULT_LONGITUDE = <?php echo DEFAULT_LONGITUDE; ?>,
		BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<?php
	$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
	$this->template->addToAsset('scripts','http://maps.googleapis.com/maps/api/js?sensor=true');
	$this->template->addToAsset('scripts',BASE_URL.'/js/open311/map_picker.js');
?>