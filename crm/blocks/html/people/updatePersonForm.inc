<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Person $this->person
 */
// Let the template override the title, if it wants to
$title = $this->title ? self::escape($this->title) : $this->template->title;
?>
<section class="updatePersonForm">
    <header>
        <h1><?= $title; ?></h1>
    </header>
	<form method="post" action="<?= $_SERVER['REDIRECT_URL']; ?>">

		<?php
			$this->_include('people/personInfoFields.inc');
		?>

		<fieldset><legend><?= $this->_('submit'); ?></legend>
			<button type="submit" class="save"><?= $this->_('save'); ?></button>
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
				<a class=\"cancel button\" href=\"$return_url\" $onclick>
					{$this->_('cancel')}
				</a>
				";
			?>
		</fieldset>
	</form>
</section>
