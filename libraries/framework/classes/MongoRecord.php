<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class MongoRecord
{
	protected $data = array();

	/**
	 * Returns any field stored in $data
	 */
	public function get($fieldname)
	{
		if (isset($this->data[$fieldname])) {
			return $this->data[$fieldname];
		}
	}

	/**
	 * Returns raw data from the Mongo record.
	 *
	 * You can specify a particular element to return, using the dot syntax
	 * If no element is asked for, the entire data record is returned
	 *
	 * @param string $path
	 * @return array
	 */
	public function getData($path=null)
	{
		$data = $this->data;
		if ($path) {
			foreach (explode('.',$path) as $field) {
				if (isset($data[$field])) {
					$data = $data[$field];
				}
				else {
					return null;
				}
			}
		}
		return $data;
	}

	/**
	 * Returns data from person structures in the Mongo record
	 *
	 * If the data doesn't exist an empty string will be returned
	 * Examples:
	 * 	getPersonData('enteredByPerson','id')
	 *  getPersonData('referredPerson','fullname')
	 *
	 * @param string $personField
	 * @param string $dataField
	 * @return string
	 */
	public function getPersonData($personField,$dataField)
	{
		return isset($this->data[$personField][$dataField])
			? $this->data[$personField][$dataField]
			: '';
	}

	/**
	 * Loads the Person from Mongo and returns the Person object
	 *
	 * @param string $personField
	 * @return Person
	 */
	public function getPersonObject($personField)
	{
		if (isset($this->data[$personField]['_id'])) {
			return new Person((string)$this->data[$personField]['_id']);
		}
	}

	/**
	 * @param string|array|Person $person
	 */
	public function setPersonData($fieldname,$person)
	{
		if (is_string($person)) {
			$person = new Person($person);
		}
		if (is_array($person)) {
			if (isset($person['_id'])) {
				$person = new Person("$person[_id]");
			}
		}
		if ($person instanceof Person) {
			$data = array(
				'_id'=>$person->getId(),
			);
			$fields = array(
				'fullname',
				'department'
				// Available fields are:
				//'fullname','firstname','middlename','lastname',
				//'username','department','organization',
				//'email','phone','address','city','state','zip'
			);
			foreach ($fields as $field) {
				$get = 'get'.ucfirst($field);
				if ($person->$get()) {
					$data[$field] = $person->$get();
				}
			}
			$this->data[$fieldname] = $data;
		}
		else {
			throw new Exception('invalidPerson');
		}
	}
}
