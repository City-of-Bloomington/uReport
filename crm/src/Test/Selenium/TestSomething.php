<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Selenium;

class TestSomething extends SeleniumTest
{
    public function testOne()
    {
        $this->driver->get('https://drifter.bloomington.in.gov/crm');
        $this->assertEquals($this->driver->getTitle(), "uReport:");
    }
}
