<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\Database;

class Open311Client
{
	/**
	 * Translates an Open311 POST into a CRM POST
	 *
	 * A valid Client api_key is required in the POST
	 *
	 * http://wiki.open311.org/GeoReport_v2#POST_Service_Request
	 * Open311 POST Service Request: defines what key/values should
	 * be posted when adding a service request.
	 * However, we're just going to take the Open311 POST and
	 * hand it off to Ticket::handleAdd().  So, we have to
	 * translate all the Open311 POST parameters into the
	 * POST parameters that Ticket::handleAdd() expects.
	 *
	 * Media should not need any special handling here,
	 * since both CRM and Open311 use "media" as the fieldname
	 *
	 * @param array $open311Post The raw POST from the client
	 * @return array A POST for Ticket::handleAdd()
	 */
	public static function translatePostArray($open311Post)
	{
		// Make sure we have a valid api_key
		if (!empty($open311Post['api_key'])) { $client = Client::loadByApiKey($open311Post['api_key']); }
		else { throw new \Exception('clients/unknown'); }

		$ticketPost = array(
			'client_id'       => $client->getId(),
			'contactMethod_id'=> $client->getContactMethod_id()
		);

		// The lookup table for keyname translations
		// 'open311Post_key'=>'ticketPost_key'
		$fields = array(
			// Ticket Fields
			'service_code'  =>'category_id',
			'lat'           =>'latitude',
			'long'          =>'longitude',
			'address_string'=>'location',
			// Issue Fields
			'description'   =>'description',
			'attribute'     =>'customFields'
		);
		foreach ($fields as $open311Field=>$crmField) {
			if (!empty($open311Post[$open311Field])) {
				$ticketPost[$crmField] = $open311Post[$open311Field];
			}
		}
		$person = self::findPerson($open311Post);
		if ($person) {
			$ticketPost['reportedByPerson_id'] = $person->getId();
		}

		return $ticketPost;
	}

	/**
	 * Try to find this person in the database.
	 * If we cannot find them, create a new person record.
	 *
	 * @return Person
	 */
	public static function findPerson($post)
	{
		$search = array();

		// Translates Open311 parameters into PersonList search parameters
		// open311 => personList
		$fields = array(
			'first_name'=> 'firstname',
			'last_name' => 'lastname',
			'email'     => 'email',
			'phone'     => 'phoneNumber',
			'device_id' => 'phoneDeviceId'
		);
		foreach ($fields as $open311Field=>$crmField) {
			if (!empty($post[$open311Field])) { $search[$crmField] = $post[$open311Field]; }
		}
		// If the user provided any personal info, do a person search
		if (count($search)) {
			$table = new PersonTable();
			$list = $table->find($search);
			// When we find one and only one record, use the record we found
			if (count($list) == 1) { $person = $list->current(); }
			// Otherwise, create a new person record
			else {
				$p = array();
				foreach ($fields as $key=>$field) {
					if (!empty($post[$key])) { $p[$field] = $post[$key]; }
				}
				if (count($p)) {
					$person = new Person();
					try {
						$person->handleUpdate($p);
						$person->save();

						if (!empty($post['email'])) {
							$email = new Email();
							$email->setPerson($person);
							$email->setEmail($post['email']);
							$email->save();
						}

						if (!empty($post['phone']) || !empty($post['device_id'])) {
							$phone = new Phone();
							$phone->setPerson($person);
							if (!empty($post['phone'    ])) { $phone->setNumber  ($post['phone'    ]); }
							if (!empty($post['device_id'])) { $phone->setDeviceId($post['device_id']); }
							$phone->save();
						}
					}
					catch (\Exception $e) { unset($person); }
				}
			}
		}
		return isset($person) ? $person : null;
	}

    /**
     * Exports Open311 data to a file
     *
     * The CSV file will be in the format described by the 311 bulk request data standard
     * @see: http://govex.jhu.edu/untangling-311-request-data/
     * @see: https://docs.google.com/spreadsheets/d/1N9TSt6anpSJkZQv5ZhwtH4r4UX_QegerC4IRnzhkrzI/edit#gid=2065572358
     *
     * @param string $file         Full path to the file to create
     * @param int    $category_id  (optional) uReport category to filter the output
     */
    public static function export_data($file, $category_id=null)
    {
        $category_name = 'full data';

        if ($category_id) {
            $category      = new Category($category_id);
            $category_name = $category->getName();
        }

        $fields = [
            'service_request_id',
            'requested_datetime',
            'updated_datetime',
            'closed_date',
            'status',
            'source',
            'service_name',
            'service_subtype',
            'description',
            'agency_responsible',
            'address',
            'lat',
            'long'
        ];

        $FILE = fopen($file, 'w');
        if (!$FILE) {
            die("Could not create $file\n");
        }
        fputcsv($FILE, $fields);

        $zend_db = Database::getConnection();
        $sql = "select  t.id            as ticket_id,
                        t.enteredDate,
                        t.lastModified,
                        t.closedDate,
                        t.status,
                        m.name          as contactMethod,
                        c.name          as category,
                        t.description,
                        d.name          as department,
                        t.location, t.city, t.state, t.zip,
                        t.latitude,
                        t.longitude
                from tickets             t
                left join categories     c on t.category_id       = c.id
                left join people         p on t.assignedPerson_id = p.id
                left join departments    d on p.department_id     = d.id
                left join contactMethods m on t.contactMethod_id  = m.id
                where c.displayPermissionLevel = 'anonymous'";
        if (isset($category)) {
            $id = (int)$category->getId();
            $sql.= " and t.category_id=$id";
        }

        echo "Dumping $category_name to ".basename($file)."\n";
        $result = $zend_db->query($sql)->execute();
        foreach ($result as $row) {
            $enteredDate  = new \DateTime($row['enteredDate']);
            $lastModified = new \DateTime($row['lastModified']);
            $closedDate   = new \DateTime($row['closedDate']);

            $location = '';
            if (!empty($row['location'])) {
                $l = [ $row['location'] ];
                if (!empty($row['city' ])) { $l[] = $row['city' ]; }
                if (!empty($row['state'])) { $l[] = $row['state']; }
                if (!empty($row['zip'  ])) { $l[] = $row['zip'  ]; }

                $location = implode(', ', $l);
            }

            $data = [
                'service_request_id' => $row['ticket_id'],
                'requested_datetime' => $enteredDate ->format('c'),
                'updated_datetime'   => $lastModified->format('c'),
                'closed_date'        => $closedDate  ->format('c'),
                'status'             => $row['status'],
                'source'             => $row['contactMethod'],
                'service_name'       => $row['category'],
                'service_subtype'    => '',
                'description'        => $row['description'],
                'agency_responsible' => $row['department'],
                'address'            => $location,
                'lat'                => $row['latitude'],
                'long'               => $row['longitude']
            ];
            fputcsv($FILE, $data);
        }
        fclose($FILE);
    }
}
