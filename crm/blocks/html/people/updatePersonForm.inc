<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$title = $this->person->getId() ? View::escape('Edit '.$this->person->getFirstname()) : 'Add Someone';
// Let the template override the title, if it wants to
$title = $this->title ? View::escape($this->title) : $title;
?>
<div class="updatePersonForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">

		<?php
			include APPLICATION_HOME.'/blocks/html/people/personInfoFields.inc';
		?>

		<fieldset><legend>Submit</legend>
			<button type="submit"><span class="icon-save"></span> Save</button>
			<?php
				if (isset($_REQUEST['return_url'])) {
					$return_url = urlencode($_REQUEST['return_url']);
				}
				elseif ($this->person->getId()) {
					$return_url = $this->person->getURL();
				}
				else {
					$return_url = BASE_URL.'/people';
				}

				$onclick = isset($_REQUEST['callback'])
					? 'onclick="self.close();"'
					: '';
				echo "
				<a class=\"btn\" href=\"$return_url\" $onclick>
					<span class=\"icon-remove\"></span>
					Cancel
				</a>
				";
			?>
		</fieldset>
	</form>
</div>
