<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Selenium;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class SeleniumTest extends TestCase
{
    protected $driver;

    public function setUp(): void
    {
        $host = 'http://localhost:4444/wd/hub';
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome(), 5000);
    }
}
