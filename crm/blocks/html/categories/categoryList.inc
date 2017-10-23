<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryList $this->categoryList
 */
use Application\Models\Person;
?>
<section class="categoryList">
    <header>
        <h1><?= $this->template->title; ?></h1>
        <div class="tools">
            <?php
                if (Person::isAllowed('categories','update')) {
                    $helper = $this->template->getHelper('buttonLink');
                    echo $helper->buttonLink(
                        BASE_URI.'/categories/update',
                        $this->_('add'),
                        'add'
                    );
                }
            ?>
        </div>
    </header>
	<?php
		$toc = '<nav class="tableOfContents"><ul>';
		$html = '';
		$previous = null;
		foreach ($this->categoryList as $category) {
			$group = $category->getCategoryGroup();

			if ($group && "{$group->getId()}"!=$previous) {
				$anchor = preg_replace('[^a-zA-Z]','',$group->getName());
				$name = self::escape($group->getName());

				if ($previous) {
					$html.= '</div>';
				}
				$html.= "<div class=\"group\"><h2><a id=\"$anchor\">$name</a></h2>";
				$toc .= "<li><a href=\"".BASE_URI."/categories#$anchor\">$name</a></li>";

				$previous = "{$group->getId()}";
			}

			$name              = self::escape($category->getName());
			$postPermission    = self::escape($category->getPostingPermissionLevel());
			$displayPermission = self::escape($category->getDisplayPermissionLevel());
			$description       = self::escape($category->getDescription());

			$d = $category->getDepartment();
			$p = $category->getDefaultPerson_id() ? $category->getDefaultPerson() : $d->getDefaultPerson();
			$department    = $d ? self::escape($d->getName())     : '';
			$defaultPerson = $p ? self::escape($p->getFullname()) : '';
			if ($defaultPerson) { $department = "$department ($defaultPerson)"; }

            $autoCloseActive    = $category->autoCloseIsActive() ? $this->_('active_yes') : $this->_('active_no');
            $active             = $category->isActive()          ? $this->_('active_yes') : $this->_('active_no');

			$html.= "
			<div class=\"category\">
				<h3><a href=\"".BASE_URI."/categories/view?category_id={$category->getId()}\">
						$name
					</a>
				</h3>
				<p>$description</p>
				<table>
					<thead>
						<tr><th>{$this->_(['department', 'departments', 1])}</th>
							<th>{$this->_('sla')}
								{$this->_(['day', 'days', 2])}
							</th>
							<th>{$this->_('postingPermissionLevel')}</th>
							<th>{$this->_('displayPermissionLevel')}</th>
							<th>{$this->_('status')}</th>
							<th>{$this->_('autoCloseIsActive')}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>$department</td>
							<td>{$category->getSlaDays()}</td>
							<td>$postPermission</td>
							<td>$displayPermission</td>
							<td>$active</td>
							<td>$autoCloseActive</td>
						</tr>
					</tbody>
				</table>
			</div>
			";
		}
		$toc.= '</ul></nav>';

		echo $toc;
		echo $html;
		echo '</div>'; // Close out the last Group div
	?>
</div>
