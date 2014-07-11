<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div class="categoryList">
	<h2><?php
			echo $this->translate(array('labels.category', 'labels.categories', 2));
			if (Person::isAllowed('categories','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/categories/update',
					$this->translate('labels.add'),
					'add'
				);
			}
		?>
	</h2>
	<?php
		$toc = '<div class="tableOfContents"><ul>';
		$html = '';
		$previous = null;
		foreach ($this->categoryList as $category) {
			$group = $category->getCategoryGroup();

			if ($group && "{$group->getId()}"!=$previous) {
				$anchor = preg_replace('[^a-zA-Z]','',$group->getName());
				$name = View::escape($group->getName());

				if ($previous) {
					$html.= '</div>';
				}
				$html.= "<div class=\"group\"><h3><a id=\"$anchor\">$name</a></h3>";
				$toc.= "<li><a href=\"".BASE_URI."/categories#$anchor\">$name</a></li>";

				$previous = "{$group->getId()}";
			}

			$editButton = '';
			if (Person::isAllowed('categories','update')) {
				$editButton = $helper->buttonLink(
					BASE_URI.'/categories/update?category_id='.$category->getId(),
					$this->translate('labels.edit'),
					'edit'
				);
			}
			$name              = View::escape($category->getName());
			$postPermission    = View::escape($category->getPostingPermissionLevel());
			$displayPermission = View::escape($category->getDisplayPermissionLevel());
			$description       = View::escape($category->getDescription());

			$d = $category->getDepartment();
			$department = $d ? View::escape($d->getName()) : '';

			$html.= "
			<div class=\"category\">
				<h4><a href=\"".BASE_URI."/categories/view?category_id={$category->getId()}\">
						$name
					</a>
					$editButton
				</h4>
				<p>$description</p>
				<table>
					<thead>
						<tr><th>{$this->translate(array('labels.department', 'labels.departments', 1))}</th>
							<th>{$this->translate('labels.sla')}
								{$this->translate(array('labels.day', 'labels.days', 2))}
							</th>
							<th>{$this->translate('labels.postingPermissionLevel')}</th>
							<th>{$this->translate('labels.displayPermissionLevel')}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>$department</td>
							<td>{$category->getSlaDays()}</td>
							<td>$postPermission</td>
							<td>$displayPermission</td>
						</tr>
					</tbody>
				</table>
			</div>
			";
		}
		echo $toc;
		echo $html;
		echo '</div>'; // Close out the last Group div
	?>
</div>
