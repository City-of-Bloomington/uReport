<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Application\Models\Person;
use Application\Models\Ticket;

class TicketTest extends TestCase
{
    public function testChangingWillUpdateClusters()
    {
        $ticket = new Ticket();
        $this->assertFalse($ticket->willUpdateClustersOnSave());
        $ticket->setLatitude(39.123);
        $this->assertTrue($ticket->willUpdateClustersOnSave());
    }

    public function testLatLngShouldNotAllowZeros()
    {
        $ticket = new Ticket();
        $ticket->setLatitude (0);
        $ticket->setLongitude(0);

        $this->assertNull($ticket->getLatitude ());
        $this->assertNull($ticket->getLongitude());
    }

    public function testDisplayPermission()
    {
        $ticket = new Ticket();
        $ticket->setDisplayPermissionLevel('private');

        $this->assertEquals('private', $ticket->getDisplayPermissionLevel());
        $this->assertFalse($ticket->allowsDisplay());

        $p = new Person();
        $p->setRole('Staff');
        $this->assertTrue($ticket->allowsDisplay($p));

        $p->setRole('Administrator');
        $this->assertTrue($ticket->allowsDisplay($p));

        $ticket->setDisplayPermissionLevel('public');
        $this->assertEquals('public', $ticket->getDisplayPermissionLevel());
        $this->assertTrue($ticket->allowsDisplay());
    }
}
