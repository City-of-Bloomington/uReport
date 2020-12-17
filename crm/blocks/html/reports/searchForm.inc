<?php
/**
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\AddressService;
use Application\Models\CategoryTable;
use Application\Models\Department;
use Application\Models\DepartmentTable;
use Application\Models\ClientTable;
use Application\Models\Search;

use Application\ActiveRecord;

$helper = $this->template->getHelper('field');
?>
<section id="reportSearchForm">
    <header>
        <h1><?= $this->_('search'); ?></h1>
    </header>
	<form method="get">
		<fieldset>
			<details>
				<summary><?= $this->_('category'); ?></summary>
				<?php
					$table   = new CategoryTable();
					$list    = $table->find();
                    $options = [];
                    $values  = (!empty($_GET['categories']) && is_array($_GET['categories']))
                                ? array_keys($_GET['categories'])
                                : [];
					foreach ($list as $c) { $options[] = ['value'=>$c->getId(), 'label'=>self::escape($c->getName())]; }
					echo $helper->field([
                        'name'    => 'categories',
                        'type'    => 'checkbox',
                        'value'   => $values,
                        'options' => $options
					]);

				?>
			</details>
			<details>
                <summary><?= $this->_('department'); ?></summary>
				<?php
					$table   = new DepartmentTable();
					$list    = $table->find();
					$options = [];
					$values  = (!empty($_GET['departments']) && is_array($_GET['departments']))
                                ? array_keys($_GET['departments'])
                                : [];
					foreach ($list as $d) { $options[] = ['value'=>$d->getId(), 'label'=>self::escape($d->getName())]; }
					echo $helper->field([
                        'name'    => 'departments',
                        'type'    => 'checkbox',
                        'value'   => $values,
                        'options' => $options
					]);
				?>
			</details>
			<details>
                <summary><?= $this->_('enteredDate'); ?></summary>
                <?php
                    foreach (['start', 'end'] as $d) {
                        $value = '';
                        if (!empty($_GET['enteredDate'][$d])) {
                            $value = self::escape($_GET['enteredDate'][$d]);
                        }
                        echo $helper->field([
                            'name'  => "enteredDate[$d]",
                            'id'    => "enteredDate-$d",
                            'label' => $this->_($d),
                            'type'  => 'date',
                            'value' => $value
                        ]);
                    }
                ?>
            </details>
			<details>
                <summary><?= $this->_('postingPermissionLevel'); ?></summary>
                <?php
                    $value   = !empty($_GET['postingPermissionLevel'])
                                ? $_GET['postingPermissionLevel']
                                : '';
                    $options = [
                        ['value' => 'anonymous'],
                        ['value' => 'staff']
                    ];
                    echo $helper->field([
                        'name'    => 'postingPermissionLevel',
                        'type'    => 'radio',
                        'value'   => $value,
                        'options' => $options
                    ]);
                ?>
			</details>
            <details>
                <summary><?= $this->_('client'); ?></summary>
                <?php
                    $table   = new ClientTable();
                    $list    = $table->find();
                    $options = [];
                    $value   = (!empty($_GET['clients']) && is_array($_GET['clients']))
                                ? array_keys($_GET['clients'])
                                : [];
                    foreach ($list as $c) { $options[] = ['value'=>$c->getId(), 'label'=>self::escape($c->getName())]; }
                    echo $helper->field([
                        'name'    => 'clients',
                        'type'    => 'checkbox',
                        'value'   => $value,
                        'options' => $options
                    ]);
                ?>
            </details>
            <?php
                $customFields = [];
                if (defined('ADDRESS_SERVICE')) {
                    $search       = new Search();
                    $customFields = call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions');
                    foreach ($customFields as $f=>$v) {
                        echo "<details><summary>$v[description]</summary>";
                        $options = [['value'=>'']];
                        $value   = !empty($_GET[$f]) ? self::escape($_GET[$f]) : '';
                        foreach ($search->facetValues($f) as $k=>$c) { $options[] = ['value'=>$k]; }

                        echo $helper->field([
                            'name'    => $f,
                            'id'      => $f,
                            'type'    => 'select',
                            'value'   => $value,
                            'options' => $options
                        ]);
                        echo "</details>";
                    }
                }

                $helper   = $this->template->getHelper('renderInputs');
                $ignore   = array_keys($customFields);
                $ignore[] = 'enteredDate';
                echo $helper->renderInputs($_GET, null, $ignore);
            ?>
			<button type="submit" class="submit"><?= $this->template->_('submit'); ?></button>
		</fieldset>
	</form>
</section>
