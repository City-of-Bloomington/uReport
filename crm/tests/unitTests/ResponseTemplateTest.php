<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\ResponseTemplate;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../bootstrap.inc';

class CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testAutoRespondIsActive()
    {
        $template = new ResponseTemplate();
        $this->assertFalse($template->autoRespondIsActive());

        $template->setAutoRespond(1);
        $this->assertTrue($template->autoRespondIsActive());
    }
}