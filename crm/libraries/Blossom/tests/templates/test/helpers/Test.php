<?php
/**
 * Test Helper
 *
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Template;

class Test
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	public function test($string)
	{
		return $string;
	}
}
