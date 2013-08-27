<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class Controller
{
	protected $template;

	abstract public function index();

	public function __construct(Template &$template)
	{
		$this->template = $template;
		$this->template->controller = get_class($this);
	}
}
