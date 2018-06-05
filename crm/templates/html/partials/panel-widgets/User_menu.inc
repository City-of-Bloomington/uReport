<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
global $AUTH0;
$helper      = $this->getHelper('dropdown');
$dropdown_id = 'user_menu';

if (isset($_SESSION['USER'])) {
    $name = self::escape($_SESSION['USER']->getFullname());
    $links = [
        ['url'=>BASE_URI.'/login/logout', 'label'=>$this->_('logout')],
        ['url'=>BASE_URI.'/account',      'label'=>$this->_('my_account')],
        ['url'=>'https://github.com/City-of-Bloomington/uReport/wiki/User-Guide', 'label'=>$this->_('help')]
    ];

    echo $helper->dropdown($links, $name, $dropdown_id);
}
else {
    $links      = [];
    $return_url = parent::escape($_SERVER['REQUEST_URI']);

    if (defined('CAS_SERVER') || isset($AUTH0)) {
        if (defined('CAS_SERVER')) {
            $links[] = ['url'=>BASE_URI."/login/cas?return_url=$return_url", 'label'=>$this->_('login_cas')];
        }
        if (isset($AUTH0)) {
            $links[] = ['url'=>BASE_URI."/login/auth0?return_url=$return_url", 'label'=>$this->_('login_auth0')];
        }

    }
    else {
        $links[] = ['url'=>BASE_URI."/login?return_url=$return_url", 'label'=>$this->_('login')];
    }
    echo $helper->dropdown($links, $this->_('login'), $dropdown_id);
}
