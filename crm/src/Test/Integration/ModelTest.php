<?php
/**
 * @copyright 2019-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Database;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public static function models(): array
    {
        return [
            ['\Application\Models\Action'          ],
            ['\Application\Models\Address'         ],
            ['\Application\Models\Bookmark'        ],
            ['\Application\Models\Category'        ],
            ['\Application\Models\CategoryGroup'   ],
            ['\Application\Models\Client'          ],
            ['\Application\Models\ContactMethod'   ],
            ['\Application\Models\Department'      ],
            ['\Application\Models\Email'           ],
            ['\Application\Models\IssueType'       ],
            ['\Application\Models\Media'           ],
            ['\Application\Models\Person'          ],
            ['\Application\Models\Phone'           ],
            ['\Application\Models\ResponseTemplate'],
            ['\Application\Models\Substatus'       ],
            ['\Application\Models\Ticket'          ],
            ['\Application\Models\TicketHistory'   ]
        ];
    }

    #[DataProvider('models')]
    public function testConstructors(string $class)
    {
        $this->assertFalse(empty($class::TABLENAME), 'TABLENAME not set');
        $pdo = Database::getConnection();
        $tab = $class::TABLENAME;

        $q   = $pdo->query("select * from $tab limit 1");
        $res = $q->fetchAll(\PDO::FETCH_ASSOC);

        $o   = new $class($res[0]['id']);
        $this->assertTrue(self::compare($res[0], $o->data));
    }

    /**
     * Make sure all the database fields are present in the loaded Model
     *
     * Model data may contain additional fields, this only compares the fields
     * from the database.
     */
    private static function compare(array $db, array $model): bool
    {
        foreach ($db as $k=>$v) {
            if ($model[$k] != $v) { return false; }
        }
        return true;
    }
}
