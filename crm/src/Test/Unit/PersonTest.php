<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Application\Models\Person;

class PersonTest extends TestCase
{
    public function testEmptyId()
    {
        $person = new Person();
        $this->assertEmpty($person->getId());
    }
}
