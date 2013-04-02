<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div><h2>My Account</h2>
	<ul><li><a href="<?php echo BASE_URL; ?>/account">Personal Info</a></li>
		<?php
			if ($_SESSION['USER']->getDepartment_id()) {
				echo "
				<li><a href=\"".BASE_URL."/account/updateMyDepartment\">Department</a></li>
				";
			}
		?>
	</ul>
</div>
