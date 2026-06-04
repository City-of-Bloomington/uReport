<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class PersonTable extends PdoRepository
{
    public const TABLENAME = 'people';
    public const CLASSNAME = __namespace__.'\Person';

    private static $fields = [
        'firstname','middlename','lastname',
        'email','organization',
        'address','city','state','zip',
        'department_id','username','authenticationMethod','role'
    ];

    private static function prepareJoins(array $fields): array
    {
        $joins = [];

        if (!empty($fields['email'])) {
            $joins[] = 'left join peopleEmails e on p.id=e.person_id';
        }
        if (!empty($fields['phone'])) {
            $joins[] = 'left join peoplePhones h on p.id=h.person_id';
        }
        if (   !empty($fields['address'])
            || !empty($fields['city'   ])
            || !empty($fields['state'  ])
            || !empty($fields['zip'    ])) {
            $joins[] = 'left join peopleAddresses a on p.id=a.person_id';
        }
        return $joins;
    }

    public function find(array $fields=[], ?string $order='p.lastname, p.firstname', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select p.* from people p';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            $joins = self::prepareJoins($fields);

            foreach ($fields as $k=>$v) {
                if ($v) {
                    switch ($k) {
                        case 'user_account':
                            $where[] = 'p.username is not null';

                            break;
                        case 'email':
                            $where[] = 'e.email=:email';
                            $params['email'] = $v;
                            break;

                        case 'phone':
                            $where[] = 'h.number=:number';
                            $params['number'] = $v;
                            break;

                        case 'address':
                        case 'city':
                        case 'state':
                        case 'zip':
                            $where[] = "a.$k=:$k";
                            $params[$k] = $v;
                            break;

                        default:
                            if (in_array($k, self::$fields)) {
                                $where[] = "p.$k=:$k";
                                $params[$k] = $v;
                            }
                    }
                }
            }
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function search(array $fields=[], ?string $order='p.lastname, p.firstname', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select p.* from people p';
        $joins  = [];
        $where  = [];
        $params = [];

        if (isset($fields['query'])) {
            $v       = trim($fields['query']).'%';
            $joins[] = 'left join peopleEmails e on p.id=e.person_id';
            $where[] = '(p.firstname=:firstname or p.lastname=:lastname or p.email=:email or p.username=:username)';
            $params  = ['firstname'=>$v, 'lastname'=>$v, 'email'=>$v, 'username'=>$v];
        }
        elseif ($fields) {
            $joins = self::prepareJoins($fields);

            foreach ($fields as $k=>$v) {
                if ($v) {
                    switch ($k) {
                        case 'user_account':
                            $where[] = 'p.username is not null';
                            break;

                        case 'email':
                            $where[] = 'e.email like :email';
                            $params['email'] = "$v%";
                            break;

                        case 'phone':
                            $where[] = 'h.number like :number';
                            $params['number'] = "$v%";
                            break;

                        case 'department_id':
                            $where[] = "p.$k=:$k";
                            $params[$k] = $v;
                            break;

                        case 'address':
                        case 'city':
                        case 'state':
                        case 'zip':
                            $where[] = "a.$k=:$k";
                            $params[$k] = "$v%";
                            break;

                        default:
                            if (in_array($k, self::$fields)) {
                                $where[] = "p.$k like :$k";
                                $params[$k] = "$v%";
                            }
                    }
                }
            }
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
