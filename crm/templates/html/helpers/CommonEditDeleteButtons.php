<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CommonEditDeleteButtons
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	/**
	 * @param string $controller
	 * @param string $item
	 * @param mixed $object
	 */
	public function commonEditDeleteButtons($controller, $item, $object)
	{
		$class = ucfirst($item);
		$buttons = "
		<a class=\"edit button\"
			href=\"".BASE_URI."/{$controller}/update{$class}?{$item}_id={$object->getId()}\">
			Edit $class
		</a>
		<a class=\"delete button\"
			href=\"".BASE_URI."/{$controller}/delete{$class}?{$item}_id={$object->getId()}\">
			Delete $class
		</a>
		";
		return $buttons;
	}
}