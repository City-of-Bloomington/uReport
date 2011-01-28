<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Find Tickets</h1>
<form method="get" action="<?php echo BASE_URL; ?>/tickets/home.php">
	<fieldset><legend>Ticket Information</legend>
		<table>
			<tr><td><label for="person_id">Entered By</label></td>
				<td><select name="person_id" id="person_id">
						<option></option>
						<?php
							$people = new PersonList(array('enteredTicket'=>true));
							foreach ($people as $person) {
								$name = View::escape($person->getFullname());
								$selected = (isset($_GET['person_id']) && $_GET['person_id']==$person->getId())
									? 'selected="selected"'
									: '';
								echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr><td><label for="location">Location</label></td>
				<td><input name="location" id="location"
						value="<?php echo isset($_GET['location']) ? View::escape($_GET['location']) : ''; ?>" />
				</td>
			</tr>
			<tr><td><label for="neighborhoodAssociation">Neighborhood Assocation</label></td>
				<td><select name="neighborhoodAssociation" id="neighborhoodAssociation">
						<option></option>
						<?php
							foreach (Location::getNeighborhoodAssociations() as $neighborhoodAssociation) {
								$selected = (isset($_GET['neighborhoodAssociation']) && $_GET['neighborhoodAssociation']==$neighborhoodAssociation)
									? 'selected="selected"'
									: '';
								$neighborhoodAssociation = View::escape($neighborhoodAssociation);
								echo "<option $selected>$neighborhoodAssociation</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr><td><label for="township">Township</label></td>
				<td><select name="township" id="township">
						<option></option>
						<?php
							foreach (Location::getTownships() as $township) {
								$selected = (isset($_GET['township']) && $_GET['township']==$township)
									? 'selected="selected"'
									: '';
								$township = View::escape($township);
								echo "<option $selected>$township</option>";
							}
						?>
					</select>
				</td>
			</tr>
		</table>

		<?php
			echo $this->template->formButton(
				'Search','submit','search'
			);
		?>
	</fieldset>
</form>
