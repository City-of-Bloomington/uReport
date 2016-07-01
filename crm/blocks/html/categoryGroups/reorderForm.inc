<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\CategoryGroupTable;
?>
<section class="categoryGroupReportForm">
    <header><h1><?= $this->_('reorder'); ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/categoryGroups/reorder">
        <?php
            $helper = $this->template->getHelper('field');

            $t = new CategoryGroupTable();
            $groups = $t->find();
            foreach ($groups as $group) {
                $id    = $group->getId();
                $name  = self::escape($group->getName());
                $order = (int)$group->getOrdering();

                echo $helper->field([
                    'name'  => "categoryGroups[$id]",
                    'id'    => "categoryGroups_$id",
                    'label' => $name,
                    'value' => $order,
                    'type'  => 'number'
                ]);
            }
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/categoryGroups');
		?>
	</form>
</section>
