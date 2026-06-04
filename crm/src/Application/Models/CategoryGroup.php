<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;

class CategoryGroup extends ActiveRecord
{
    public const TABLENAME = 'categoryGroups';

    /**
     * Populates the object with data
     *
     * Passing in an associative array of data will populate this object without
     * hitting the database.
     *
     * Passing in a scalar will load the data from the database.
     * This will load all fields in the table as properties of this class.
     * You may want to replace this with, or add your own extra, custom loading
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $sql = ActiveRecord::isId($id)
                    ? 'select * from categoryGroups where id=?'
                    : 'select * from categoryGroups where name=?';
                $result = Database::query($sql, [$id]);
                if (count($result)) {
                    $this->exchangeArray($result[0]);
                }
                else {
                    throw new \Exception('categoryGroups/unknown');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
        }
    }

    /**
     * Throws an exception if anything's wrong
     *
     * @throws \Exception
     */
    public function validate()
    {
        // Check for required fields here.  Throw an exception if anything is missing.
        if(!$this->data['name']) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function save()   { parent::save();   }
    public function delete() { parent::delete(); }

    //----------------------------------------------------------------
    // Getters and Setters
    //----------------------------------------------------------------
    public function getName()        { return parent::get('name');     }
    public function getOrdering()   { return parent::get('ordering'); }
    public function __toString()    { return parent::get('name');     }

    public function setName    ($s) { parent::set('name',     $s); }
    public function setOrdering($s)    { parent::set('ordering', $s); }

    public function handleUpdate(array $post)
    {
        $this->setName    ($post['name']);
        $this->setOrdering($post['ordering']);
    }

    public function getCategories(): array
    {
        $out = [];
        $sql = 'select * from categories where categoryGroup_id=?';
        $res = Database::query($sql, [$this->getId()]);
        foreach ($res as $r) { $out[] = new Category($r); }
        return $out;
    }
}
