<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 */
use Application\Models\Ticket;
use Application\Models\Person;

$fields = [
    'id', 'name', 'description', 'categoryGroup', 'department',
    'displayPermissionLevel', 'postingPermissionLevel', 'notificationReplyEmail'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->category->$get());
}
$p             = $this->category->getDefaultPerson();
$defaultPerson = $p ? self::escape($p->getFullname()) : '';

$editButton = '';
if (Person::isAllowed('categories', 'update')) {
    $helper = $this->template->getHelper('buttonLink');
    $editButton = $helper->buttonLink(
        BASE_URI."/categories/update?category_id=$id",
        $this->_('edit'),
        'edit'
    );
}
?>
<section class="categoryInfo">
    <header>
        <h1><?= $name; ?></h1>
        <div class="tools"><?= $editButton; ?></div>
    </header>
	<p><?= $description; ?></p>
	<?php
        $autoCloseActive = $this->category->autoCloseIsActive() ? $this->_('active_yes') : $this->_('active_no');
        $status          = $this->category->isActive()          ? $this->_('active_yes') : $this->_('active_no');

        $fields = [
            'status', 'categoryGroup', 'department', 'defaultPerson',
            'displayPermissionLevel', 'postingPermissionLevel',
            'notificationReplyEmail', 'autoCloseActive'
        ];
        foreach ($fields as $f) {
            echo "
            <dl><dt>{$this->_($f)}</dt>
                <dd>{$$f}</dd>
            </dl>
            ";
        }

        $this->_include('responseTemplates/list.inc');
    ?>

	<form method="get">
		<fieldset id="customFields">
            <legend><?= $this->_('custom_fields'); ?></legend>
            <?php
                $this->ticket = new Ticket();
                $this->_include('tickets/customFieldsForm.inc');
            ?>
		</fieldset>
	</form>
</section>
