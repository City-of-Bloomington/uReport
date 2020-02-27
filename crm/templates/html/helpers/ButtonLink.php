<?php
/**
 * Provides markup for button links
 *
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;

class ButtonLink extends Helper
{
	const SIZE_BUTTON = 'button';
	const SIZE_ICON   = 'icon';

	public function buttonLink($url, $label, $type=null, $size=self::SIZE_BUTTON, array $additionalAttributes=[])
	{
        if (!$size) { $size = self::SIZE_BUTTON; }

        $attrs = '';
        foreach ($additionalAttributes as $key=>$value) {
            $attrs.= " $key=\"$value\"";
        }
        return "<a href=\"$url\" class=\"$size $type\" $attrs>$label</a>";
	}
}
