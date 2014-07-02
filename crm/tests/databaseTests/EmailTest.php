<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Email;

require_once './DatabaseTestCase.php';

class EmailTest extends DatabaseTestCase
{
    private $testPerson_id = 1;

    public function getDataSet()
    {
        return $this->createMySQLXMLDataSet(__DIR__.'/testData/emails.xml');
    }

    public function testSaveAndLoad()
    {
        $email = new Email();
        $email->setPerson_id($this->testPerson_id);
        $email->setEmail('something@localhost');
        $email->save();

        $id = $email->getId();
        $this->assertNotEmpty($id);

        $email = new Email($id);
        $this->assertEquals('something@localhost', $email->getEmail());
    }

    public function testDelete()
    {
        $test    = new Email(1);
        $another = new Email(2);

        $this->assertTrue ($test   ->isUsedForNotifications());
        $this->assertFalse($another->isUsedForNotifications());

        $test->delete();
        $another = new Email(2);
        $this->assertTrue($another->isUsedForNotifications());
    }
}
