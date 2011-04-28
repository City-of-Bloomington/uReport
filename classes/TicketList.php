<?php
/**
 * A collection class for Ticket objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
	public function find($fields=null,$order=null)
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					if (false !== strpos($key,'_id')) {
						$value = new MongoId($value);
					}
					$search[$key] = $value;
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
	 * This is a callback function, called from ZendDbResultIterator.  It is
	 * called once per row of the result.
	 *
	 * @param int $key The index of the result row to load
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
			'enteredDate'=>'Ticket Date',
			'enteredByPerson'=>'Ticket Entered By',
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
			'categories'=>'Categories'
		);
	}
}
