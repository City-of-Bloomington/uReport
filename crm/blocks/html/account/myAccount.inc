<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
?>
<nav>
    <header>
        <h1><?= $this->_('my_account'); ?></h1>
    </header>
	<ul><li><a href="<?= BASE_URL; ?>/account"><?= $this->_('my_info'); ?></a></li>
		<li><a href="<?= $_SESSION['USER']->getURL(); ?>"><?= $this->_('my_profile'); ?></a></li>
		<?php
			if ($_SESSION['USER']->getDepartment_id()) {
				echo "
				<li><a href=\"".BASE_URL."/account/updateMyDepartment\">{$this->_('department')}</a></li>
				";
			}
		?>
	</ul>
</nav>
