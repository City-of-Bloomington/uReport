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
			if (userIsAllowed('departments','update')) {
				echo '<a class="btn" href="'.BASE_URL.'/departments/update"><span class="icon-plus"></span> Add</a>';
			}
		?>
		Departments
	</h2>
	<?php
		foreach ($this->departmentList as $department) {
			$block = new Block('departments/departmentInfo.inc',array('department'=>$department));
			echo $block->render('html',$this->template);
		}
	?>
</div>
