<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->title (optional)
 */
$title = $this->title ? self::escape($this->title) : $this->_('find_person');
?>
<section class="findPeople">
    <header>
        <h1><?= $title; ?></h1>
    </header>
	<form method="get" action="<?= BASE_URI; ?>/people">
		<fieldset>
			<?php
                $fields = ['firstname', 'lastname', 'email', 'organization'];
				$helper = $this->template->getHelper('field');

				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='setOfPeople' && !in_array($key, $fields)) {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}

				foreach ($fields as $f) {
					echo $helper->field([
                        'name'  => $f,
                        'id'    => $f,
                        'label' => $this->_($f),
                        'value' => isset($_GET[$f]) ? self::escape($_GET[$f]) : ''
					]);
				}

				$roles   = ['all', 'staff', 'public'];
				$options = [];
                foreach ($roles as $v) { $options[] = ['value'=>$v, 'label'=>$this->_($v)]; }
                echo $helper->field([
                    'name'    => 'setOfPeople',
                    'id'      => 'setOfPeople',
                    'label'   => $this->_(['role', 'roles', count($roles)]),
                    'type'    => 'select',
                    'value'   => !empty($_GET['setOfPeople']) ? $_GET['setOfPeople'] : 'all',
                    'options' => $options
                ]);
			?>
			<button type="submit" class="search"><?= $this->_('search'); ?></button>
			<?php
				if (isset($_REQUEST['callback'])) {
					echo "
					<button type=\"button\" class=\"cancel\" onclick=\"self.close();\">
						{$this->_('cancel')}
					</button>
					";
				}
			?>
		</fieldset>
	</form>
</section>
<?php
	$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
	$this->template->addToAsset('scripts', JQUERY_UI.'/jquery-ui.min.js');
	$this->template->addToAsset('scripts', BASE_URI.'/js/people/search.js');
?>
