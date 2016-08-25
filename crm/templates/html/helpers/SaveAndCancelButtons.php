<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Template;

class SaveAndCancelButtons
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	public function saveAndCancelButtons($cancelURL)
	{
		$helper = $this->template->getHelper('buttonLink');

		$buttons = "<button type=\"submit\" class=\"save\">{$this->template->translate('save')}</button>\n";
		$buttons.=$helper->buttonLink(
			$cancelURL,
			$this->template->translate('cancel'),
			'cancel'
		);
		return $buttons;
	}
}
