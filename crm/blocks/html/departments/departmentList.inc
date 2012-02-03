<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
?>
<div class="departmentList">
	<h1><?php
			if (userIsAllowed('departments','update')) {
				echo '<a class="add button" href="'.BASE_URL.'/departments/update">Add</a>';
			}
		?>
		Departments
	</h1>
	<?php
		foreach ($this->departmentList as $department) {
			$block = new Block('departments/departmentInfo.inc',array('department'=>$department));
			echo $block->render('html',$this->template);
		}
	?>
</div>
