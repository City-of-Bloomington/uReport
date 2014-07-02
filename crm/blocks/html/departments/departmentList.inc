<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
?>
<div class="departmentList">
	<h2><?php
			echo $this->translate(array('labels.department', 'labels.departments', 2));
			if (Person::isAllowed('departments','update')) {
				echo sprintf('<a class="btn" href="%s"><span class="icon-plus"></span> %s</a>',
					BASE_URI.'/departments/update',
					$this->translate('labels.add')
				);
			}
		?>
	</h2>
	<?php
		foreach ($this->departmentList as $department) {
			$block = new Block('departments/departmentInfo.inc',array('department'=>$department));
			echo $block->render('html',$this->template);
		}
	?>
</div>
