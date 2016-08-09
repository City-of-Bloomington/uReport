<?php
/**
 * Media is attached to Tickets
 *
 * Files will be stored as /data/media/YYYY/MM/DD/$unique_id
 * User provided filenames will be stored in the database
 *
 * @copyright 2006-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Media extends ActiveRecord
{
	protected $tablename = 'media';

	protected $ticket;
	protected $person;

	/**
	 * Whitelist of accepted file types
	 */
	public static $mime_types = [
		 'image/jpeg'                          => 'jpg',
		 'image/gif'                           => 'gif',
		 'image/png'                           => 'png',
		 'image/tiff'                          => 'tiff',
		 'application/pdf'                     => 'pdf',
		 'application/rtf'                     => 'rtf',
		 'application/msword'                  => 'doc',
		 'application/msexcel'                 => 'xls',
		 #'application/x-gzip'                  => 'gz',
		 #'application/zip'                     => 'zip',
		 #'text/plain'                          => 'txt',
		 #'video/x-ms-wmv'                      => 'wmv',
		 'video/quicktime'                     => 'mov',
		 #'application/vnd.rn-realmedia'        => 'rm',
		 #'audio/vnd.rn-realaudio'              => 'ram',
		 #'audio/mpeg'                          => 'mp3',
		 #'video/mp4'                           => 'mp4',
		 #'video/x-flv'                         => 'flv',
		 #'audio/x-ms-wma'                      => 'wma',
		 #'application/vnd.google-earth.kml+xml'=> 'kml',
		 #'application/x-shockwave-flash'       => 'swf',
		 #'application/postscript'              => 'eps'
	];

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
				$zend_db = Database::getConnection();
				if (ActiveRecord::isId($id)) {
					$sql = 'select * from media where id=?';
				}
				// Internal filename without extension
				elseif (ctype_xdigit($id)) {
					$sql = 'select * from media where internalFilename=?';
				}

				$result = null;
				if (isset($sql)) {
                    $result = $zend_db->createStatement($sql)->execute([$id]);
				}

				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('media/unknownMedia');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setUploaded('now');
			if (isset($_SESSION['USER'])) {
				$this->setPerson($_SESSION['USER']);
			}
		}
	}

    /**
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     *
     * @Override
     * @param array $data
     */
    public function exchangeArray($data)
    {
        parent::exchangeArray($data);

        $this->ticket = null;
        $this->person = null;
    }

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->data['filename'  ]) { throw new \Exception('media/missingFilename' ); }
		if (!$this->data['mime_type' ]) { throw new \Exception('media/missingMimeType' ); }
		if (!$this->data['ticket_id' ]) { throw new \Exception('media/missingTicket_id'); }
	}

	public function save() { parent::save(); }

	/**
	 * Deletes the file from the hard drive
	 */
	public function delete()
	{
		unlink(SITE_HOME."/media/{$this->getDirectory()}/{$this->getInternalFilename()}");
		parent::delete();
	}
	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()         { return parent::get('id');         }
	public function getTicket_id()  { return parent::get('ticket_id');  }
	public function getFilename()   { return parent::get('filename');   }
	public function getMime_type()  { return parent::get('mime_type');  }
	public function getPerson_id()  { return parent::get('person_id');  }
	public function getUploaded($f=null, \DateTimeZone $tz=null) { return parent::getDateData('uploaded', $f, $tz); }

	public function getTicket() { return   parent::getForeignKeyObject(__namespace__.'\Ticket', 'ticket_id'); }
	public function getPerson() { return   parent::getForeignKeyObject(__namespace__.'\Person', 'person_id'); }

	public function setTicket_id($id)    { parent::setForeignKeyField (__namespace__.'\Ticket', 'ticket_id', $id); }
	public function setPerson_id($id)    { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $id); }
	public function setTicket(Ticket $o) { parent::setForeignKeyObject(__namespace__.'\Ticket', 'ticket_id', $o);  }
	public function setPerson(Person $o) { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $o);  }
	public function setUploaded($d)      { parent::setDateData('uploaded', $d); }

	public function getModified($f=null, \DateTimeZone $tz=null) { return $this->getUploaded($f, $tz); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns the root portion of the mime_type
	 *
	 * @return string
	 */
	public function getMedia_type() { return dirname($this->getMime_type()); }
	public function getType()       { return dirname($this->getMime_type()); }

	/**
	 * Populates this object by reading information on a file
	 *
	 * This function does the bulk of the work for setting all the required information.
	 * It tries to read as much meta-data about the file as possible
	 *
	 * @param array|string Either a $_FILES array or a path to a file
	 */
	public function setFile($file)
	{
		// Handle passing in either a $_FILES array or just a path to a file
		$tempFile = is_array($file) ? $file['tmp_name'] : $file;
		$filename = is_array($file) ? basename($file['name']) : basename($file);
		if (!$tempFile) {
            if (!empty($file['error'])) {
                throw new \Exception('media/uploadFailed', $file['error']);
            }
            else {
                throw new \Exception('media/uploadFailed');
            }
		}

		// Find out the mime type for this file
		$this->data['mime_type'] = mime_content_type($tempFile);
		if (array_key_exists($this->data['mime_type'], self::$mime_types)) {
            $extension = self::$mime_types[$this->data['mime_type']];
		}
		else {
			throw new \Exception('media/unknownFileType');
		}

		// Clean all bad characters from the filename
		$filename = $this->createValidFilename($filename, $extension);
		$this->data['filename'] = $filename;

		// Move the file where it's supposed to go
		$newFile   = $this->getFullPath();
		$directory = dirname($newFile);
		if (!is_dir($directory)) {
			mkdir  ($directory, 0777, true);
		}
		rename($tempFile, $newFile);
		chmod($newFile, 0666);

		// Check and make sure the file was saved
		if (!is_file($newFile)) {
			throw new \Exception('media/badServerPermissions');
		}
	}

	/**
	 * Returns the full path to the file or derivative
	 *
	 * @return string
	 */
	public function getFullPath()
	{
        return SITE_HOME."/media/{$this->getDirectory()}/{$this->getInternalFilename()}";
	}

	/**
	 * Returns the path of the file, relative to SITE_HOME/media
	 *
	 * Media is stored in the SITE_HOME directory, outside of the web directory
	 * This variable only contains the partial path.
	 * This partial path can be concat with APPLICATION_HOME or BASE_URL
	 *
	 * @return string
	 */
	public function getDirectory()
	{
        return $this->getUploaded('Y/n/j');
	}

	/**
	 * Returns the file name used on the server
	 *
	 * We do not use the filename the user chose when saving the files.
	 * We generate a unique filename the first time the filename is needed.
	 * This filename will be saved in the database whenever this media is
	 * finally saved.
	 *
	 * @return string
	 */
	public function getInternalFilename()
	{
		$filename = parent::get('internalFilename');
		if (!$filename) {
			$filename = uniqid();
			parent::set('internalFilename', $filename);
		}
		return $filename;
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
        return self::$mime_types[$this->data['mime_type']];
	}

	/**
	 * Returns the URL to this media
	 *
	 * @return string
	 */
	public function getURL($size=null)
	{
		$url = BASE_URL."/media/{$this->getDirectory()}";
		if (!empty($size)) { $url.= "/$size"; }
		$url.= "/{$this->getInternalFilename()}";
		return $url;
	}

	/**
	 * @return int
	 */
	public function getFilesize()
	{
        return filesize($this->getFullPath());
	}

	/**
	 * Cleans a filename of any characters that might cause problems on filesystems
	 *
	 * @return string
	 */
	public static function createValidFilename($string)
	{
		// No bad characters
		$string = preg_replace('/[^A-Za-z0-9_\.\s]/','',$string);

		// Convert spaces to underscores
		$string = preg_replace('/\s+/','_',$string);

		// Lower case any file extension
		if (preg_match('/(^.*\.)([^\.]+)$/',$string,$matches)) {
			$string = $matches[1].strtolower($matches[2]);
		}

		return $string;
	}
}
