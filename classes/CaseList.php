<?php
/**
 * A collection class for case objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class caseList extends MongoResultIterator
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
			$this->cursor = $this->mongo->cases->find($search);
		}
		else {
			$this->cursor = $this->mongo->cases->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
	}

	/**
	 * Hydrates all the case objects from a database result set
	 *
	 * @param array $data A single data record returned from Mongo
	 * @return case
	 */
	public function loadResult($data)
	{
		return new case($data);
	}

	/**
	 * Returns fields that can be displayed in a single line
	 *
	 * When displaying caseLists, it is useful to try to display each case on a single line
	 * These are the fields that are possible to be joined into a single line for any single case
	 *
	 * @return array(fieldname=>human_readable_label)
	 */
	public static function getDisplayableFields()
	{
		// All possible columns to display
		return array(
			'id'=>'case #',
			'enteredDate'=>'case Date',
			'enteredByPerson'=>'case Entered By',
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
