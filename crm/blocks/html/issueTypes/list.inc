<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\IssueTypeTable;
use Application\Models\Person;

$t = new IssueTypeTable();
$list = $t->find();
$count = count($list);
?>
<section>
    <header>
        <h1><?= $this->_(['issueType', 'issueTypes', $count]); ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('issueTypes','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/issueTypes/update',
					$this->_('add'),
					'add'
				);
			}
		?>
		</div>
    </header>
    <?php
        if ($count) {
            echo '<ul>';
            $editButton = '';
            foreach ($list as $type) {
                if (Person::isAllowed('issueTypes','update')) {
                    $editButton = $helper->buttonLink(
                        BASE_URI.'/issueTypes/update?issueType_id='.$type->getId(),
                        $this->_('edit'),
                        'edit'
                    );
                }
                $name = self::escape($type->getName());
                echo "<li>$editButton $name</li>";
            }
            echo '</ul>';
        }
    ?>
</section>
