<?php
/**
 * @copyright 2019-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Unit;

use PHPUnit\Framework\TestCase;

use Application\Models\Category;
use Application\Models\Department;
use Application\Models\Person;
use Application\Models\Substatus;
use Application\Models\Ticket;

class TicketTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SESSION['USER']);
    }

    public function testChangingWillUpdateClusters() : void
    {
        $ticket = new Ticket();
        $this->assertFalse($ticket->willUpdateClustersOnSave());
        $ticket->setLatitude(39.123);
        $this->assertTrue($ticket->willUpdateClustersOnSave());
    }

    public function testLatLngShouldNotAllowZeros() : void
    {
        $ticket = new Ticket();
        $ticket->setLatitude (0);
        $ticket->setLongitude(0);

        $this->assertNull($ticket->getLatitude ());
        $this->assertNull($ticket->getLongitude());
    }

    public function testValidateFailsWithoutCategory() : void
    {
        $this->expectExceptionMessage('tickets/missingCategory');
        $ticket = new Ticket();
        $ticket->validate();
    }

    public function testValidateFailsWithoutDescriptionOrLocation() : void
    {
        $this->expectExceptionMessage('missingRequiredFields');

        $ticket = new Ticket([
            'category_id' => 1
        ]);

        $ticket->validate();
    }

    public function testValidateFailsWithoutBothCoordinates() : void
    {
        $this->expectExceptionMessage('missingRequiredFields');

        $ticket = new Ticket([
            'category_id' => 1,
            'latitude'    => 39.170085621693396
        ]);

        $ticket->validate();
    }

    public function testValidateFailsForUnresolvedTicket() : void
    {
        $this->expectExceptionMessage('tickets/missingResolution');

        $ticket = new Ticket([
            'category_id' => 1,
            'department_id' => 99,
            'location'    => '401 N Morton St',
            'latitude'    => 39.170085621693396,
            'longitude'   => -86.53678539714889,
            'status'      => 'closed'
        ]);

        $ticket->validate();
    }

    public function testValidateSucceedsWithLocationStringOnly() : void
    {
        $ticket = new Ticket([
            'category_id' => 1,
            'location'    => 'Rev. Ernest D. Butler Park'
        ]);

        $ticket->validate();

        $this->addToAssertionCount(1);
    }

    public function testValidateSucceedsWithLatLongOnly() : void
    {
        $ticket = new Ticket([
            'category_id' => 1,
            'latitude'    => 39.17112475449202,
            'longitude'   => -86.54195584130858
        ]);

        $ticket->validate();

        $this->addToAssertionCount(1);
    }

    public function testValidateSucceedsWithDescriptionOnly() : void
    {
        $ticket = new Ticket([
            'category_id' => 1,
            'description' => 'Testing'
        ]);

        $ticket->validate();

        $this->addToAssertionCount(1);
    }

    public function testValidateSetsDefaultStatus() : void
    {
        $ticket = $this->createMinimumValidTicket();

        $ticket->validate();

        $this->assertSame(
            $ticket->getStatus(), 
            'open'
        );
    }

    public function testValidateFailsForStatusMismatch() : void
    {
        $this->expectExceptionMessage('tickets/statusMismatch');

        $subStatus = new Substatus([
            'id' => 1
        ]);

        $ticket = new Ticket([
            'category_id' => 1,
            'department_id' => 99,
            'location'    => '401 N Morton St',
            'latitude'    => 39.170085621693396,
            'longitude'   => -86.53678539714889,
            'status'      => 'open'
        ]);
        $ticket->setSubstatus($subStatus);

        $ticket->validate();
    }

    public function testValidateSetsDefaultEnteredDate() : void
    {
        $ticket = $this->createMinimumValidTicket();

        $ticket->validate();

        $expectedDate = new \DateTimeImmutable('now');
        $this->assertEqualsWithDelta(
            $ticket->getEnteredDate('U'), 
            $expectedDate->getTimestamp(), 
            5
        );
    }

    public function testValidateSetsEnteredByPerson(): void
    {
        $user = new Person([
            'id' => 123
        ]);

        $_SESSION['USER'] = $user;

        $ticket = $this->createMinimumValidTicket();

        $ticket->validate();

        $this->assertSame(
            $user->getId(),
            $ticket->getEnteredByPerson_id()
        );
    }

    public function testValidateDoesNotSetEnteredByPersonWhenNoUserInSession(): void
    {
        unset($_SESSION['USER']);

        $ticket = $this->createMinimumValidTicket();

        $ticket->validate();

        $this->assertNull($ticket->getEnteredByPerson_id());
    }

    public function testValidateDoesNotOverwriteExistingEnteredByPerson(): void
    {
        $originalUser = new Person([
            'id' => 123
        ]);

        $_SESSION['USER'] = new Person([
            'id' => 456
        ]);

        $ticket = new Ticket([
            'category_id' => 1,
            'department_id' => 99,
            'enteredByPerson_id' => $originalUser->getId(),
            'location'    => '401 N Morton St',
            'latitude'    => 39.170085621693396,
            'longitude'   => -86.53678539714889
        ]);

        $ticket->validate();

        $this->assertSame(
            $originalUser->getId(),
            $ticket->getEnteredByPerson_id()
        );
    }

    public function testValidateDoesNotOverwriteExistingAssignedPerson(): void
    {
        $user = new Person([
            'id' => 123
        ]);

        $ticket = new Ticket([
            'category_id' => 1,
            'department_id' => 99,
            'assignedPerson_id' => $user->getId(),
            'location'    => '401 N Morton St',
            'latitude'    => 39.170085621693396,
            'longitude'   => -86.53678539714889
        ]);

        $ticket->validate();

        $this->assertSame(
            $user->getId(), 
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsCategoryDefaultPerson(): void
    {
        $defaultPerson = new Person([
            'id' => 123
        ]);

        $category = new Category([
            'id' => 77
        ]);

        $category->setDefaultPerson($defaultPerson);

        $ticket = $this->createMinimumValidTicket();
        $ticket->setCategory($category);

        $ticket->validate();

        $this->assertSame(
            $defaultPerson->getId(), 
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsDepartmentDefaultPerson(): void
    {
        $defaultPerson = new Person([
            'id' => 123
        ]);

        $department = new Department([
            'id'               => 99, 
            'defaultPerson_id' => 123
        ]);

        $category = new Category([
            'id' => 77
        ]);

        $category->setDepartment($department);
        $department->setDefaultPerson($defaultPerson);

        $ticket = $this->createMinimumValidTicket();
        $ticket->setCategory($category);

        $ticket->validate();

        $this->assertSame(
            $defaultPerson->getId(), 
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsPersonFromSession(): void
    {
        $sessionUser = new Person([
            'id' => 123
        ]);

        $_SESSION['USER'] = $sessionUser;

        $category = new Category([
            'id' => 77
        ]);

        $ticket = $this->createMinimumValidTicket();
        $ticket->setCategory($category);

        $ticket->validate();

        $this->assertSame(
            $sessionUser->getId(),
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsPersonFallback(): void
    {
        $category = new Category([
            'id' => 77
        ]);

        $ticket = $this->createMinimumValidTicket();
        $ticket->setCategory($category);

        $ticket->validate();

        $this->assertSame(
            1,
            $ticket->getAssignedPerson_id()
        );
    }

    private function createMinimumValidTicket(): Ticket
    {
        return new Ticket([
            'category_id' => 1,
            'department_id' => 99,
            'location'    => '401 N Morton St',
            'latitude'    => 39.170085621693396,
            'longitude'   => -86.53678539714889
        ]);
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
