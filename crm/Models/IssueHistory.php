<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

class IssueHistory extends History
{
	protected $tablename = 'issueHistory';
	public function __construct($id=null) { parent::__construct($id); }
}
