<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Domain\Auth;

class ExternalIdentity
{
    public $username;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip;

    public function __construct(?array $data=null)
    {
        if ($data) {
            foreach ((array)$this as $k=>$v) {
                if (!$v && !empty($data[$k])) {
                    $this->$k = $data[$k];
                }
            }
        }
    }
}
