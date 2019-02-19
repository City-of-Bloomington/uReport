<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function classProvider()
    {
        $classes = [];
        foreach (glob(APPLICATION_HOME.'/src/Application/Models/*Table.php') as $f) {
            preg_match('/(^.*)\.([^\.]+)$/', $f, $matches);
            $file      = basename($matches[1]);
            $classes[] = ["Application\Models\\".basename($matches[1])];
        }
        return $classes;
    }

    /**
     * @dataProvider classProvider
     */
    public function testTableClasses(string $class)
    {
        $o = new $class();
        $this->assertEquals($class, get_class($o));
    }
}
