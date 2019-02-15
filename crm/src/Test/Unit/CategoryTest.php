<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Application\Models\Category;

class CategoryTest extends TestCase
{
    public function testAutoResponseFields()
    {
        $category = new Category();
        $this->assertFalse($category->autoCloseIsActive());

        $category->setAutoCloseIsActive(false);
        $this->assertFalse($category->autoCloseIsActive());

        $category->setAutoCloseIsActive(true);
        $this->assertEquals(1, $category->getAutoCloseIsActive());
        $this->assertTrue($category->autoCloseIsActive());

        $category->setNotificationReplyEmail('test@somewhere');
        $this->assertEquals('test@somewhere', $category->getNotificationReplyEmail());
    }

    public function testHandleUpdate()
    {
        $data = [
            'name'=>'Name', 'description'=>'Description',
            'postingPermissionLevel'=>'test permission', 'displayPermissionLevel'=>'testing display',
            'slaDays'=>2, 'notificationReplyEmail'=>'test@somewhere',
            'autoCloseIsActive'=>1,
            // The rest of these fields would cause hits to the database if we set values for them
            // We have left them empty so we can do clean unit tests.
            // These fields would need to be tested in the database tests.
            'department_id'=>'', 'categoryGroup_id'=>'','customFields'=>'',
            'autoCloseSubstatus_id'=>'', 'defaultPerson_id'=>''
        ];

        $category = new Category();
        $category->handleUpdate($data);
        foreach ($data as $f=>$value) {
            $get = 'get'.ucfirst($f);
            $this->assertEquals($value, $category->$get());
        }
    }
}
