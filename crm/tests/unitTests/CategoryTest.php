<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Category;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testAutoResponseFields()
    {
        $category = new Category();
        $this->assertFalse($category->autoResponseIsActive());
        $this->assertFalse($category->autoCloseIsActive());

        $category->setAutoResponseIsActive(false);
        $this->assertFalse($category->autoResponseIsActive());

        $category->setAutoResponseIsActive(true);
        $this->assertEquals(1, $category->getAutoResponseIsActive());
        $this->assertTrue  ($category->autoResponseIsActive());

        $category->setAutoCloseIsActive(false);
        $this->assertFalse($category->autoCloseIsActive());

        $category->setAutoCloseIsActive(true);
        $this->assertEquals(1, $category->getAutoCloseIsActive());
        $this->assertTrue($category->autoCloseIsActive());

        $category->setAutoResponseText('test message');
        $this->assertEquals('test message', $category->getAutoResponseText());

        $category->setNotificationReplyEmail('test@somewhere');
        $this->assertEquals('test@somewhere', $category->getNotificationReplyEmail());
    }

    public function testHandleUpdate()
    {
        $data = [
            'name'=>'Name', 'description'=>'Description',
            'postingPermissionLevel'=>'test permission', 'displayPermissionLevel'=>'testing display',
            'slaDays'=>2, 'notificationReplyEmail'=>'test@somewhere',
            'autoResponseIsActive'=>1,'autoResponseText'=>'auto response','autoCloseIsActive'=>1,
            // The rest of these fields would cause hits to the database if we set values for them
            // We have left them empty so we can do clean unit tests.
            // These fields would need to be tested in the database tests.
            'department_id'=>'', 'categoryGroup_id'=>'','customFields'=>'', 'autoCloseSubstatus_id'=>''
        ];

        $category = new Category();
        $category->handleUpdate($data);
        foreach ($data as $f=>$value) {
            $get = 'get'.ucfirst($f);
            $this->assertEquals($value, $category->$get());
        }
    }
}