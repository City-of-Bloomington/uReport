<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;

class Action extends ActiveRecord
{
    // Pre-defined system level actions
    const OPENED     = 'open';
    const CLOSED     = 'closed';
    const ASSIGNED   = 'assignment';
    const UPDATED    = 'update';
    const RESPONDED  = 'response';
    const DUPLICATED = 'duplicate';
    const COMMENTED  = 'comment';
    const CHANGED_CATEGORY = 'changeCategory';
    const CHANGED_LOCATION = 'changeLocation';
    const UPLOADED_MEDIA   = 'upload_media';

	protected $tablename = 'actions';
	public static $types = ['system', 'department'];

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$sql = ActiveRecord::isId($id)
					? 'select * from actions where id=?'
					: 'select * from actions where name=?';

				$zend_db = Database::getConnection();
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('actions/unknown');
				}
			}

		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setType('department');
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getName() || !$this->getDescription()) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->getType()) {
			$this->setType('department');
		}
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function __toString()     { return parent::get('name');        }
	public function getId()          { return parent::get('id');          }
	public function getName()        { return parent::get('name');        }
	public function getDescription() { return parent::get('description'); }
	public function getType()        { return parent::get('type');        }
	public function getTemplate   () { return parent::get('template'   ); }
	public function getReplyEmail () { return parent::get('replyEmail' ); }

	public function setName($s)        { parent::set('name',        $s); }
	public function setDescription($s) { parent::set('description', $s); }
	public function setTemplate   ($s) { parent::set('template',    $s); }
	public function setReplyEmail ($s) { parent::set('replyEmail',  $s); }

	/**
	 * @param string $string
	 */
	public function setType($string)
	{
		$string = trim($string);
		if (in_array($string, self::$types)) { $this->data['type'] = $string; }
	}

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
        if ($this->getType() !== 'system') {
            $this->setName($post['name']);
        }
		$this->setDescription($post['description']);
		$this->setTemplate   ($post['template'   ]);
		$this->setReplyEmail ($post['replyEmail' ]);
	}
}
