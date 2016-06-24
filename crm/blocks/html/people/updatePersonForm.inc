<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$title = $this->person->getId() ? self::escape('Edit '.$this->person->getFirstname()) : 'Add Someone';
// Let the template override the title, if it wants to
$title = $this->title ? self::escape($this->title) : $title;
?>
<div class="updatePersonForm">
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= $_SERVER['REDIRECT_URL']; ?>">

		<?php
			include APPLICATION_HOME.'/blocks/html/people/personInfoFields.inc';
		?>

		<fieldset><legend><?= $this->_('submit'); ?></legend>
			<button type="submit">
				<span class="fa fa-check"></span>
				<?= $this->_('save'); ?>
			</button>
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
					<span class=\"fa fa-times\"></span>
					{$this->_('cancel')}
				</a>
				";
			?>
		</fieldset>
	</form>
</div>
