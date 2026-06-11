<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Unit;

use PHPUnit\Framework\TestCase;
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

    public function testValidateRequiredFields()
    {
        $this->expectNotToPerformAssertions();
        $ticket = new Ticket([
            'category_id' => 1,
            'location'    => 'Rev. Ernest D. Butler Park',
            'latitude'    => 39.17112475449202,
            'longitude'   => -86.54195584130858
        ]);
        $ticket->validate();

        $ticket = new Ticket([
            'category_id' => 1,
            'description' => 'Testing'
        ]);
        $ticket->validate();

    }
}
