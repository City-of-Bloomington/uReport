<nav class="dropdown">
<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
if (isset($_SESSION['USER'])) {
    $name = self::escape($_SESSION['USER']->getFullname());
    echo "
    <button class=\"launcher\" id=\"userMenu\" aria-haspopup=\"true\" aria-expanded=\"false\">$name</button>
    <div class=\"links\" aria-labeledby=\"userMenu\">
    ";

    $routes = [
        BASE_URI.'/login/logout' => $this->_('logout'),
        BASE_URI.'/account'      => $this->_('my_account'),
        'https://github.com/City-of-Bloomington/uReport/wiki/User-Guide' => $this->_('help')
    ];
    foreach ($routes as $url => $label) {
        echo "<a href=\"$url\">$label</a>";
    }
    echo "
    </div>
    ";
}
else {
    $return_url = self::escape($_SERVER['REQUEST_URI']);
    $url = BASE_URI.'/login';
    echo "<a href=\"$url?return_url=$return_url\">{$this->_('login')}</a>";
}
?>
</nav>
