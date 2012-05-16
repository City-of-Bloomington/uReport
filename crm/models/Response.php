<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Response extends MongoRecord
{
	public function __construct($data=null)
	{
		if (isset($data)) {
			if (is_array($data)) {
				$this->data = $data;
			}
			else {
				throw new Exception('response/invalidData');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['date'] = new MongoDate();
		}
	}

	public function validate()
	{
		if (!$this->data['date']) {
			$this->data['date'] = new MongoDate();
		}

		if (!$this->getPerson()) {
			throw new Exception('response/unknownPerson');
		}
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	public function getContactMethod() { return parent::get('contactMethod'); }
	public function getNotes()         { return parent::get('notes');         }
	public function getPerson() { return parent::getPersonObject('person'); }
	public function getDate($f=null, DateTimeZone $tz=null) { return parent::getDateData('date', $f, $tz); }

	public function setContactMethod($s) { $this->data['contactMethod'] = trim($s); }
	public function setNotes        ($s) { $this->data['notes']         = trim($s); }
	public function setPerson($person) { parent::setPersonData('person', $person); }
	public function setDate($date) { parent::setDateData('date', $date); }
}