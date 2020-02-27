<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket     $this->ticket
 * @param Issue      $this->issue
 * @param Department $this->currentDepartment
 */
$category    = $this->ticket->getCategory();
$category_id = $category ? $category->getId() : '';
$title       = $category ? "New {$category->getName()} Case" : 'New Case';
?>
<section id="addTicketForm">
    <header>
        <h1><?= self::escape($title); ?></h1>
    </header>
	<form method="post" action="<?= BASE_URL; ?>/tickets/add">
		<fieldset><legend><?= $this->_('category'); ?></legend>
		<?php
			$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseCategory.js');
			$this->return_url = BASE_URL.'/tickets/add';
			$this->_include('tickets/partials/chooseCategoryFields.inc');
		?>
		</fieldset>

		<fieldset class="locationFields">
			<legend><?= $this->_('info_location'); ?></legend>
			<?php
                $this->_include('tickets/chooseLocation.inc');

                $helper = $this->template->getHelper('field');
                foreach(['location', 'city', 'state', 'zip', 'latitude', 'longitude'] as $f) {
                    $get = 'get'.ucfirst($f);
                    $$f = self::escape($this->ticket->$get());

                    echo $helper->field(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f]);
                }
			?>
		</fieldset>
		<fieldset class="issueFields">
			<legend><?= $this->_('info_issue'); ?></legend>
			<?php $this->_include('tickets/issueFields.inc'); ?>

            <div id="customFields">
            <?php
                $this->category = $category;
                $this->_include('tickets/customFieldsForm.inc');
            ?>
            </div>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend><?= $this->_('info_assignment'); ?></legend>
			<?php
				$this->_include('tickets/assignTicketFields.inc');
			?>
		</fieldset>


		<fieldset>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/tickets');
			?>
		</fieldset>
	</form>
</section>
