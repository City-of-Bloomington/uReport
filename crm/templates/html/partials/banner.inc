<div id="banner">
	<ul id="utilityBar">
		<?php
			$li = '<li><a href="%s">%s</a></li>';

			if (userIsAllowed('users')) {
				echo sprintf($li, BASE_URI.'/admin', $this->translate('labels.admin'));
			}
			if (isset($_SESSION['USER'])) {
				echo sprintf($li, BASE_URI.'/account', $this->translate('labels.my_account'));
				echo sprintf($li, BASE_URI.'/login/logout', $this->translate('labels.logout'));

				$name = View::escape($_SESSION['USER']->getFullname());
				echo sprintf('<li>'.sprintf($this->translate('messages.signed_in_as'), $name).'</li>');
				echo sprintf($li,
					'https://github.com/City-of-Bloomington/uReport/wiki/User-Guide',
					$this->translate('labels.help'));
			}
			else {
				$return_url = View::escape($_SERVER['REQUEST_URI']);
				echo sprintf($li, BASE_URI.'/login?return_url='.$return_url, $this->translate('labels.login'));
			}
		?>
	</ul>
	<div id="application_name">
		<a href="<?php echo BASE_URI; ?>/"><?php echo APPLICATION_NAME; ?></a>
	</div>
	<div id="location_name"><?php echo LOCATION_NAME; ?></div>
</div>
