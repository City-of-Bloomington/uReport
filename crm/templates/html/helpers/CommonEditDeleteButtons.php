<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Template;

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

		$h = $this->template->getHelper('buttonLink');
		$buttons = $h->buttonLink(
			BASE_URI."/{$controller}/update{$class}?{$item}_id={$object->getId()}",
			$this->template->translate('edit'),
			'edit',
			ButtonLink::SIZE_ICON
		);
		$buttons.= $h->buttonLink(
			BASE_URI."/{$controller}/delete{$class}?{$item}_id={$object->getId()}",
			$this->template->translate('delete'),
			'delete',
			ButtonLink::SIZE_ICON
		);
		return $buttons;
	}
}
