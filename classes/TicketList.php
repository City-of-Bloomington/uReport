<?php
/**
 * A collection class for Ticket objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TicketList extends MongoResultIterator
{
	/**
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		parent::__construct();
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param array $order
	 */
	public function find($fields=null,$order=array('enteredDate'=>-1))
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					if (is_array($value)) {
						if (false !== strpos($key,'_id')) {
							foreach ($value as $k=>$v) {
								$value[$k] = new MongoId($v);
							}
						}
						$search[$key] = array('$in'=>$value);
					}
					else {
						if (false !== strpos($key,'_id')) {
							$value = new MongoId($value);
						}
						$search[$key] = $value;
					}
				}
			}
		}
		if (count($search)) {
			$this->cursor = $this->mongo->tickets->find($search);
		}
		else {
			$this->cursor = $this->mongo->tickets->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
	}

	/**
	 * Hydrates all the Ticket objects from a database result set
	 *
	 * @param array $data A single data record returned from Mongo
	 * @return Ticket
	 */
	public function loadResult($data)
	{
		return new Ticket($data);
	}

	/**
	 * Returns fields that can be displayed in a single line
	 *
	 * When displaying TicketLists, it is useful to try to display each ticket on a single line
	 * These are the fields that are possible to be joined into a single line for any single ticket
	 *
	 * @return array(fieldname=>human_readable_label)
	 */
	public static function getDisplayableFields()
	{
		// All possible columns to display
		return array(
			'id'=>'Ticket #',
			'enteredDate'=>'Case Date',
			'enteredByPerson'=>'Case Entered By',
			'assignedPerson'=>'Assigned To',
			'referredPerson'=>'Referred To',
			'status'=>'Status',
			'resolution'=>'Resolution',
			'location'=>'Location',
			'latitude'=>'Latitude',
			'longitude'=>'Longitude',
			'city'=>'City',
			'state'=>'State',
			'zip'=>'Zip',
			'categories'=>'Categories',
			'notes'=>'Notes'
		);
	}
}
