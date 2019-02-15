<?php
/**
 * @copyright 2012-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\AddressService;
use Application\Models\CategoryTable;
use Application\Models\Department;
use Application\Models\DepartmentTable;
use Application\Models\ClientTable;
use Application\Models\Search;

use Blossom\Classes\ActiveRecord;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/collapsible.js');

$helper = $this->template->getHelper('field');
?>
<section id="reportSearchForm">
    <header>
        <h1><?= $this->_('search'); ?></h1>
    </header>
	<form method="get">
		<fieldset>
			<nav class="collapsible">
				<h1><?= $this->_('category'); ?></h1>
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
			</nav>
			<nav class="collapsible">
                <h1><?= $this->_('department'); ?></h1>
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
			</nav>
			<nav class="collapsible">
                <h1><?= $this->_('enteredDate'); ?></h1>
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
            </nav>
			<nav class="collapsible">
                <h1><?= $this->_('postingPermissionLevel'); ?></h1>
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
			</nav>
            <nav class="collapsible">
                <h1><?= $this->_('client'); ?></h1>
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
            </nav>
            <?php
                $search = new Search();
                foreach (AddressService::$customFieldDescriptions as $f=>$v) {
                    echo "<nav class=\"collapsible\"><h1>$v[description]</h1>";
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
                    echo "</nav>";
                }

                $helper   = $this->template->getHelper('renderInputs');
                $ignore   = array_keys(AddressService::$customFieldDescriptions);
                $ignore[] = 'enteredDate';
                echo $helper->renderInputs($_GET, null, $ignore);
            ?>
			<button type="submit" class="submit"><?= $this->template->_('submit'); ?></button>
		</fieldset>
	</form>
</section>
