<?php
/**
 * @copyright 2011-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\TableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;

class PersonTable extends TableGateway
{
	private $select;
	private static $defaultSort = array('p.lastname', 'p.firstname');
	private static $fields = array(
		'firstname','middlename','lastname',
		'email','organization',
		'address','city','state','zip',
		'department_id','username','authenticationMethod','role'
	);

	public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

	private function prepareJoins(array $fields)
	{
		if (!empty($fields['email'])) {
			$this->select->join(['email'=>'peopleEmails'], 'people.id=email.person_id', [], Select::JOIN_LEFT);
		}
		if (!empty($fields['phone'])) {
			$this->select->join(['phone'=>'peoplePhones'], 'people.id=phone.person_id', [], Select::JOIN_LEFT);
		}
		if (   !empty($fields['address'])
			|| !empty($fields['city'   ])
			|| !empty($fields['state'  ])
			|| !empty($fields['zip'    ])) {
			$this->select->join(['address'=>'peopleAddresses'], 'people.id=address.person_id', [], Select::JOIN_LEFT);
		}
		if (!empty($fields['reportedTicket_id'])) {
			$this->select->join(['t'=>'tickets'], 'people.id=t.reportedByPerson_id', [], Select::JOIN_LEFT);
		}
	}

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null, $order="people.lastname, people.firstname", $paginated=false, $limit=null)
	{
		$this->select = new Select('people');
		if ($fields) {
			$this->prepareJoins($fields);

			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'user_account':
							$value
								? $this->select->where('people.username is not null')
								: $this->select->where('people.username is null');
							break;
						case 'email':
							$this->select->where(['email.email' => $value]);
							break;

						case 'phone':
							$this->select->where(['phone.number' => $value]);
							break;

						case 'address':
						case 'city':
						case 'state':
						case 'zip':
							$this->select->where(["address.$key" => $value]);
							break;

						case 'reportedTicket_id':
							$this->select->where(['t.id' => $value]);
							break;

						default:
							if (in_array($key, self::$fields)) {
								$this->select->where(["people.$key" => $value]);
							}
					}
				}
			}
		}

		return parent::performSelect($this->select, $order, $paginated, $limit);
	}

	/**
	 * Populates the collection, using regular expressions for matching
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function search($fields=null, $order="people.lastname, people.firstname", $paginated=false, $limit=null)
	{
		$this->select = new Select('people');
		$search = [];
		if (isset($fields['query'])) {
			$value = trim($fields['query']).'%';
			$this->select->join(['email'=>'peopleEmails'], 'people.id=email.person_id', [], Select::JOIN_LEFT);
			$this->select->where(function (Where $w) use ($value) { $w->like('people.firstname', $value); })
					   ->orWhere(function (Where $w) use ($value) { $w->like('people.lastname' , $value); })
					   ->orWhere(function (Where $w) use ($value) { $w->like('email.email'     , $value); })
					   ->orWhere(function (Where $w) use ($value) { $w->like('people.username' , $value); });
		}
		elseif ($fields) {
			$this->prepareJoins($fields);

			foreach ($fields as $key=>$value) {
                if ($value) {
                    switch ($key) {
                        case 'user_account':
                            $value
                                ? $this->select->where('username is not null')
                                : $this->select->where('username is null');
                            break;

                        case 'email':
                            $this->select->where(function (Where $w) use ($value) { $w->like('email.email', "$value%"); });
                            break;

                        case 'phone':
                            $this->select->where(function (Where $w) use ($value) { $w->like('phone.number', "$value%"); });
                            break;

                        case 'department_id':
                            $this->select->where([$key=>$value]);
                            break;

                        case 'address':
                        case 'city':
                        case 'state':
                        case 'zip':
                            $this->select->where(function (Where $w) use ($key, $value) { $w->like("address.$key", "$value%"); });
                            break;

                        case 'reportedTicket_id':
                            $this->select->where(['t.reportedByPerson_id' => $value]);
                            break;

                        default:
                            if (in_array($key, self::$fields)) {
                                $this->select->where(function (Where $w) use ($key, $value) { $w->like("people.$key", "$value%"); });
                            }
                    }
                }
			}
		}

		return parent::performSelect($this->select, $order, $paginated, $limit);
	}
}
