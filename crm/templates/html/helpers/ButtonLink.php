<?php
/**
 * Provides markup for button links
 *
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;

class ButtonLink extends Helper
{
	const SIZE_BUTTON = 'button';
	const SIZE_ICON   = 'icon';

	public function buttonLink($url, $label, $type, $size=self::SIZE_BUTTON, array $additionalAttributes=[])
	{
        if (!$size) { $size = self::SIZE_BUTTON; }

        $attrs = '';
        foreach ($additionalAttributes as $key=>$value) {
            $attrs.= " $key=\"$value\"";
        }
		$a = $size == self::SIZE_BUTTON
			? "<a href=\"$url\" class=\"btn  $type\" $attrs>$label</a>"
			: "<a href=\"$url\" class=\"icon $type\" $attrs>$label</a>";
		return $a;
	}
}
