<?php
use Application\Models\Person;
?>
<div id="banner">
	<ul id="utilityBar">
		<?php
			$li = '<li><a href="%s">%s</a></li>';

			if (Person::isAllowed('users')) {
				echo sprintf($li, BASE_URI.'/admin', $this->_('admin'));
			}
			if (isset($_SESSION['USER'])) {
				echo sprintf($li, BASE_URI.'/account', $this->_('my_account'));
				echo sprintf($li, BASE_URI.'/login/logout', $this->_('logout'));

				$name = self::escape($_SESSION['USER']->getFullname());
				echo sprintf('<li>'.sprintf($this->_('signed_in_as', 'messages'), $name).'</li>');
				echo sprintf($li,
					'https://github.com/City-of-Bloomington/uReport/wiki/User-Guide',
					$this->_('help'));
			}
			else {
				$return_url = self::escape($_SERVER['REQUEST_URI']);
				echo sprintf($li, BASE_URI.'/login?return_url='.$return_url, $this->_('login'));
			}
		?>
	</ul>
	<div id="application_name">
		<a href="<?= BASE_URI; ?>/"><?= APPLICATION_NAME; ?></a>
	</div>
	<div id="location_name"><?= LOCATION_NAME; ?></div>
</div>
