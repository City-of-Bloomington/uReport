<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
use Application\Models\Person;
use Blossom\Classes\Block;
?>
<div class="departmentList">
	<h2><?php
			echo $this->translate(array('labels.department', 'labels.departments', 2));
			if (Person::isAllowed('departments','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/departments/update',
					$this->translate('labels.add'),
					'add'
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
