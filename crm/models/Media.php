<?php
/**
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Media extends MongoRecord
{
	public static $extensions = array(
		'jpg' =>array('mime_type'=>'image/jpeg','media_type'=>'image'),
		'gif' =>array('mime_type'=>'image/gif','media_type'=>'image'),
		'png' =>array('mime_type'=>'image/png','media_type'=>'image'),
		'tiff'=>array('mime_type'=>'image/tiff','media_type'=>'image'),
		'pdf' =>array('mime_type'=>'application/pdf','media_type'=>'attachment'),
		'rtf' =>array('mime_type'=>'application/rtf','media_type'=>'attachment'),
		'doc' =>array('mime_type'=>'application/msword','media_type'=>'attachment'),
		'xls' =>array('mime_type'=>'application/msexcel','media_type'=>'attachment'),
		'gz'  =>array('mime_type'=>'application/x-gzip','media_type'=>'attachment'),
		'zip' =>array('mime_type'=>'application/zip','media_type'=>'attachment'),
		'txt' =>array('mime_type'=>'text/plain','media_type'=>'attachment'),
		'wmv' =>array('mime_type'=>'video/x-ms-wmv','media_type'=>'video'),
		'mov' =>array('mime_type'=>'video/quicktime','media_type'=>'video'),
		'rm'  =>array('mime_type'=>'application/vnd.rn-realmedia','media_type'=>'video'),
		'ram' =>array('mime_type'=>'audio/vnd.rn-realaudio','media_type'=>'audio'),
		'mp3' =>array('mime_type'=>'audio/mpeg','media_type'=>'audio'),
		'mp4' =>array('mime_type'=>'video/mp4','media_type'=>'video'),
		'flv' =>array('mime_type'=>'video/x-flv','media_type'=>'video'),
		'wma' =>array('mime_type'=>'audio/x-ms-wma','media_type'=>'audio'),
		'kml' =>array('mime_type'=>'application/vnd.google-earth.kml+xml','media_type'=>'attachment'),
		'swf' =>array('mime_type'=>'application/x-shockwave-flash','media_type'=>'attachment'),
		'eps' =>array('mime_type'=>'application/postscript','media_type'=>'attachment')
	);

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * @param array $data
	 */
	public function __construct($data=null)
	{
		if (is_array($data)) {
			$this->data = $data;
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['uploaded'] = new MongoDate();
			if (isset($_SESSION['USER'])) {
				$this->setPerson($_SESSION['USER']);
			}
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->data['filename'] || !$this->data['mime_type'] || !$this->data['media_type']) {
			throw new Exception('missingRequiredFields');
		}
	}

	/**
	 * Deletes the file from the hard drive
	 */
	public function delete()
	{
		unlink(APPLICATION_HOME."/data/media/{$this->data['directory']}/{$this->data['filename']}");
	}
	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getFilename()   { return parent::get('filename');   }
	public function getMime_type()  { return parent::get('mime_type');  }
	public function getMedia_type() { return parent::get('media_type'); }
	public function getUploaded($f=null, DateTimeZone $tz=null) { return parent::getDateData('enteredDate', $f, $tz); }
	public function getPerson() { return parent::getPersonObject('person'); }

	public function setPerson($person) { parent::setPersonData('person',$person); }

	public function getType() { return $this->getMedia_type(); }
	public function getModified($f=null, DateTimeZone $tz) { return $this->getUploaded($f, $tz); }

	/**
	 * Returns the path of the file, relative to /data/media
	 *
	 * Media is stored in the data directory, outside of the web directory
	 * This variable only contains the partial path.
	 * This partial path can be concat with APPLICATION_HOME or BASE_URL
	 *
	 * @return string
	 */
	public function getDirectory() { return parent::get('directory'); }


	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Populates this object by reading information on a file
	 *
	 * This function does the bulk of the work for setting all the required information.
	 * It tries to read as much meta-data about the file as possible
	 *
	 * @param array|string Either a $_FILES array or a path to a file
	 */
	public function setFile($file,$directory)
	{
		# Handle passing in either a $_FILES array or just a path to a file
		$tempFile = is_array($file) ? $file['tmp_name'] : $file;
		$filename = is_array($file) ? basename($file['name']) : basename($file);
		if (!$tempFile) {
			throw new Exception('media/uploadFailed');
		}

		// Clean all bad characters from the filename
		$filename = $this->createValidFilename($filename);
		$this->data['filename'] = $filename;
		$extension = $this->getExtension();

		// Find out the mime type for this file
		if (!array_key_exists(strtolower($extension),Media::$extensions)) {
			throw new Exception('media/unknownFileType');
		}

		// Move the file where it's supposed to go
		if (!is_dir(APPLICATION_HOME."/data/media/$directory")) {
			mkdir(APPLICATION_HOME."/data/media/$directory",0777,true);
		}
		$newFile = APPLICATION_HOME."/data/media/$directory/$filename";
		rename($tempFile,$newFile);
		chmod($newFile,0666);

		if (is_file($newFile)) {
			$this->data['directory'] = $directory;
			$this->data['mime_type'] = Media::$extensions[$extension]['mime_type'];
			$this->data['media_type'] = Media::$extensions[$extension]['media_type'];
		}
		else {
			throw new Exception('media/badServerPermissions');
		}
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
		preg_match("/[^.]+$/",$this->data['filename'],$matches);
		return strtolower($matches[0]);
	}

	/**
	 * Returns the URL to this media
	 *
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL."/media/{$this->data['directory']}/{$this->data['filename']}";
	}

	/**
	 * @return int
	 */
	public function getFilesize()
	{
		return filesize(APPLICATION_HOME."/data/media/{$this->data['directory']}/{$this->data['filename']}");
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
