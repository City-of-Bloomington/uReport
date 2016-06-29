<?php
/**
 * Provides markup for button links
 *
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Template;

class ButtonLink
{
	private $template;

	const SIZE_BUTTON = 'button';
	const SIZE_ICON   = 'icon';

	public static $types = [
		'add'    => 'fa fa-plus',
		'edit'   => 'fa fa-pencil',
		'delete' => 'fa fa-times',
		'cancel' => 'fa fa-times',
		'save'   => 'fa fa-floppy-o',
		'submit' => 'fa fa-check',
		'reorder'=> 'fa fa-bars'
	];

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	public function buttonLink($url, $label, $type, $size=self::SIZE_BUTTON, array $additionalAttributes=[])
	{
        $class = array_key_exists($type, self::$types) ? self::$types[$type] : $type;
        $attrs = '';
        foreach ($additionalAttributes as $key=>$value) {
            $attrs.= " $key=\"$value\"";
        }
		$a = $size == self::SIZE_BUTTON
			? "<a href=\"$url\" class=\"btn\"    $attrs><i class=\"$class\"></i> $label</a>"
			: "<a href=\"$url\" class=\"$class\" $attrs><i class=\"hidden-label\">$label</i></a>";
		return $a;
	}
}
