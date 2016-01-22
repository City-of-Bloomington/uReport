<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div><h2><?php echo $this->translate('labels.my_account', 'labels.my_account'); ?></h2>
	<ul><li><a href="<?php echo BASE_URL; ?>/account"><?php echo $this->translate('labels.my_info', 'labels.my_info'); ?></a></li>
		<li><a href="<?php echo $_SESSION['USER']->getURL(); ?>"><?php echo $this->translate('labels.my_profile', 'labels.my_profile'); ?></a></li>
		<?php
			if ($_SESSION['USER']->getDepartment_id()) {
				echo "
				<li><a href=\"".BASE_URL."/account/updateMyDepartment\"><?php echo $this->translate(array('labels.department', 'labels.departments', 1)); ?></a></li>
				";
			}
		?>
	</ul>
</div>
