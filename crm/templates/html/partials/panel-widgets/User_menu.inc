<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
if (isset($_SESSION['USER'])) {
    $name = self::escape($_SESSION['USER']->getFullname());
    $links = [
        ['url'=>BASE_URI.'/login/logout', 'label'=>$this->_('logout')],
        ['url'=>BASE_URI.'/account',      'label'=>$this->_('my_account')],
        ['url'=>'https://github.com/City-of-Bloomington/uReport/wiki/User-Guide', 'label'=>$this->_('help')]
    ];

    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $name, 'User_menu');
}
else {
    $return_url = self::escape($_SERVER['REQUEST_URI']);
    $url = BASE_URI.'/login';
    echo "<nav><a href=\"$url?return_url=$return_url\">{$this->_('login')}</a></nav>";
}
