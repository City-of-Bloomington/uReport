<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
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
        $ticket = $this->createValidTicket();

        $ticket->validate();

        $this->assertSame(
            $ticket->getStatus(), 
            'open'
        );
    }

    public function testValidateFailsForStatusMismatch() : void
    {
        $this->expectExceptionMessage('tickets/statusMismatch');

        $subStatus = $this->createStub(Substatus::class);

        $subStatus->method('getId')
                  ->willReturn(1);

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
        $ticket = $this->createValidTicket();

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
        $user = $this->createStub(Person::class);

        $user->method('getId')
             ->willReturn(123);

        $_SESSION['USER'] = $user;

        $ticket = $this->createValidTicket();

        $ticket->validate();

        $this->assertSame(
            $user->getId(),
            $ticket->getEnteredByPerson_id()
        );
    }

    public function testValidateDoesNotSetEnteredByPersonWhenNoUserInSession(): void
    {
        unset($_SESSION['USER']);

        $ticket = $this->createValidTicket();

        $ticket->validate();

        $this->assertNull($ticket->getEnteredByPerson_id());
    }

    public function testValidateDoesNotOverwriteExistingEnteredByPerson(): void
    {
        $originalUser = $this->createStub(Person::class);
        $sessionUser  = $this->createStub(Person::class);

        $originalUser->method('getId')
                     ->willReturn(123);
        $sessionUser->method('getId')
                     ->willReturn(456);

        $_SESSION['USER'] = $sessionUser;

        $ticket = $this->createValidTicket();
        $ticket->setEnteredByPerson($originalUser);

        $ticket->validate();

        $this->assertSame(
            $originalUser->getId(),
            $ticket->getEnteredByPerson_id()
        );
    }

    public function testValidateDoesNotOverwriteExistingAssignedPerson(): void
    {
        $user = $this->createStub(Person::class);

        $user->method('getId')
             ->willReturn(123);

        $ticket = $this->createValidTicket();
        $ticket->setCategory_id(1);
        $ticket->setAssignedPerson($user);

        $ticket->validate();

        $this->assertSame(
            $user->getId(), 
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsCategoryDefaultPerson(): void
    {
        $defaultPerson = $this->createStub(Person::class);
        $category = $this->createStub(Category::class);

        $defaultPerson->method('getId')
                      ->willReturn(123);

        $category->method('getDefaultPerson_id')
                 ->willReturn(123);

        $category->method('getDefaultPerson')
                 ->willReturn($defaultPerson);

        $ticket = $this->createValidTicket();
        $ticket->setCategory($category);

        $ticket->validate();

        $this->assertSame(
            $defaultPerson->getId(), 
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsDepartmentDefaultPerson(): void
    {
        $defaultPerson = $this->createStub(Person::class);
        $department = $this->createStub(Department::class);
        $category = $this->createStub(Category::class);

        $defaultPerson->method('getId')
                      ->willReturn(123);

        $department->method('getId')
                      ->willReturn(99);

        $department->method('getDefaultPerson_id')
                 ->willReturn(123);

        $department->method('getDefaultPerson')
                 ->willReturn($defaultPerson);

        $category->method('getDepartment_id')
                 ->willReturn(99);

        $category->method('getDepartment')
                 ->willReturn($department);

        $ticket = $this->createValidTicket();
        $ticket->setCategory($category);

        $ticket->validate();

        $this->assertSame(
            $defaultPerson->getId(), 
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsPersonFromSession(): void
    {
        $user = $this->createStub(Person::class);

        $user->method('getId')
             ->willReturn(123);

        $_SESSION['USER'] = $user;

        $ticket = $this->createValidTicket();

        $ticket->validate();

        $this->assertSame(
            $user->getId(),
            $ticket->getAssignedPerson_id()
        );
    }

    public function testValidateAssignsPersonFallback(): void
    {
        $ticket = $this->createValidTicket();

        $ticket->validate();

        $this->assertSame(
            1,
            $ticket->getAssignedPerson_id()
        );
    }

    private function createValidTicket(): Ticket
    {
        return new Ticket([
            'category_id' => 1,
            'department_id' => 99,
            'location'    => '401 N Morton St',
            'latitude'    => 39.170085621693396,
            'longitude'   => -86.53678539714889
        ]);
    }
}
