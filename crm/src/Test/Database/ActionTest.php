<?php
/**
 * @copyright 2014-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Database;

use PHPUnit\Framework\TestCase;

use Application\Models\Action;
use Application\Database;

class ActionTest extends TestCase
{
    private static $testAction = [
        'name'        => 'Test',
        'description' => 'Test Description',
        'template'    => 'Test Template',
        'replyEmail'  => 'Test Email'
    ];

    public function testNameIsRequired()
    {
        $action = new Action();
        $action->setDescription(self::$testAction['description']);

        $this->expectException('\Exception');
        $this->expectExceptionMessage('missingRequiredFields');
        $action->save();
    }

    public function testDescriptionIsRequired()
    {
        $action = new Action();
        $action->setName(self::$testAction['name']);

        $this->expectException('\Exception');
        $this->expectExceptionMessage('missingRequiredFields');
        $action->save();
    }

	public function testSaveAndLoad()
	{
		$action = new Action();
		$action->handleUpdate(self::$testAction);
		$action->save();

		$id = $action->getId();
		$this->assertNotEmpty($id);

		$action = new Action($id);
		foreach (self::$testAction as $k=>$v) {
            $get = 'get'.ucfirst($k);
            $this->assertEquals($v, $action->$get());
		}

		if ($id) {
            $db = Database::getConnection();
            $db->query('delete from actions where id=?')
                    ->execute([$id]);
        }
	}
}
