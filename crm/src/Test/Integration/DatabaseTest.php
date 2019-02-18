<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Database;

class DatabaseTest extends TestCase
{
    public function foreignKeysProvider()
    {
        return [
            ['bookmarks',                     'person_id', 'people',         'id'],
            ['categories',             'categoryGroup_id', 'categoryGroups', 'id'],
            ['categories',             'defaultPerson_id', 'people',         'id'],
            ['categories',                'department_id', 'departments',    'id'],
            ['category_action_responses',     'action_id', 'actions',        'id'],
            ['category_action_responses',   'category_id', 'categories',     'id'],
            ['clients',                'contactMethod_id', 'contactMethods', 'id'],
            ['clients',                'contactPerson_id', 'people',         'id'],
            ['departments',            'defaultPerson_id', 'people',         'id'],
            ['department_actions',            'action_id', 'actions',        'id'],
            ['department_actions',        'department_id', 'departments',    'id'],
            ['department_categories',       'category_id', 'categories',     'id'],
            ['department_categories',     'department_id', 'departments',    'id'],
            ['media',                         'person_id', 'people',         'id'],
            ['media',                         'ticket_id', 'tickets',        'id'],
            ['people',                    'department_id', 'departments',    'id'],
            ['peopleAddresses',               'person_id', 'people',         'id'],
            ['peopleEmails',                  'person_id', 'people',         'id'],
            ['peoplePhones',                  'person_id', 'people',         'id'],
            ['ticketHistory',           'actionPerson_id', 'people',         'id'],
            ['ticketHistory',                 'action_id', 'actions',        'id'],
            ['ticketHistory',        'enteredByPerson_id', 'people',         'id'],
            ['ticketHistory',                 'ticket_id', 'tickets',        'id'],
            ['tickets',               'assignedPerson_id', 'people',         'id'],
            ['tickets',                     'category_id', 'categories',     'id'],
            ['tickets',                       'client_id', 'clients',        'id'],
            ['tickets',                'contactMethod_id', 'contactMethods', 'id'],
            ['tickets',              'enteredByPerson_id', 'people',         'id'],
            ['tickets',                    'issueType_id', 'issueTypes',     'id'],
            ['tickets',                       'parent_id', 'tickets',        'id'],
            ['tickets',             'reportedByPerson_id', 'people',         'id'],
            ['tickets',               'responseMethod_id', 'contactMethods', 'id'],
            ['tickets',                    'substatus_id', 'substatus',      'id']
        ];
    }

    /**
     * @dataProvider foreignKeysProvider
     */
    public function testForeignKeys(string $table,
                                    string $column,
                                    string $referenced_table,
                                    string $referenced_column)
    {
        $sql = "select  TABLE_NAME,
                        COLUMN_NAME,
                        REFERENCED_TABLE_NAME,
                        REFERENCED_COLUMN_NAME
                from INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                where TABLE_SCHEMA=?
                  and TABLE_NAME=?
                  and COLUMN_NAME=?
                  and REFERENCED_TABLE_NAME=?
                  and REFERENCED_COLUMN_NAME=?";


        $zend_db = Database::getConnection();
        $schema  = $zend_db->getDriver()->getConnection()->getCurrentSchema();
        $params  = [$schema, $table, $column, $referenced_table, $referenced_column];
        $query   = $zend_db->query($sql)->execute($params);
        $c       = count($query);

        $this->assertEquals(1, $c, 'Foreign key is missing');
    }
}
