<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$service = new Category($_GET['service_code']);
$service_code = "{$service->getId()}";
?>
<div class="clientRequestForm">
	<form method="post" action="<?php echo BASE_URL; ?>/open311/requests.html">
		<fieldset>
			<input name="jurisdiction_id" type="hidden" value="bloomington.in.gov" />
			<input name="service_code" type="hidden" value="<?php echo $service_code; ?>" />

			<div>
				<div>
					<label for="description">Description</label>
				</div>
				<textarea name="description" id="description"></textarea>
			</div>
			<table>
				<tr><td><label for="address_id">Location</label></td>
					<td><div id="location"></div>
						<input name="address_id" id="address_id" type="hidden" />
						<input name="address_string" id="address_string" type="hidden" />
						<input name="lat" id="lat" type="hidden" />
						<input name="long" id="long" type="hidden" />
					</td>
				</tr>
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
		<?php
			$customFields = $service->getCustomFields();
			if ($customFields) {
				echo "
				<fieldset>
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
		<fieldset>
			<?php
				echo $this->formButton('Submit','submit','submit');
			?>
		</fieldset>
	</form>
</div>