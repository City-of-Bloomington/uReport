<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;

class Dropdown extends Helper
{
	public function dropdown(array $links, $title, $id, $class=null)
	{
        $html = "
        <nav class=\"dropdown $class\">
            <button id=\"$id\" class=\"launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">$title</button>
            <div class=\"links\" aria-labeledby=\"$id\">
                {$this->renderLinks($links)}
            </div>
        </nav>
        ";
        return $html;
	}

	private function renderLinks(array $links)
	{
        $html = '';
        foreach ($links as $l) {

            $attrs = '';
            if (!empty($l['attrs'])) {
                $attrs = ' ';
                foreach ($l['attrs'] as $key=>$value) {
                    $attrs.= "$key=\"$value\"";
                }
            }

            $html.= empty($l['subgroup'])
                ? "<a href=\"$l[url]\"$attrs>$l[label]</a>"
                : "<div class=\"subgroup\">{$this->renderLinks($l['subgroup'])}</div>";
        }
        return $html;
	}
}