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
	public function buttonLink($url, $label, $type, array $additionalAttributes=[])
	{
        $attrs = '';
        foreach ($additionalAttributes as $key=>$value) {
            $attrs.= "$key=\"$value\"";
        }
		return "<a  href=\"$url\" class=\"$type button\" $attrs>$label</a>";
	}
}
