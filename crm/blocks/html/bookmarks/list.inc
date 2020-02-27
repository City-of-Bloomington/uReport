<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param BookmarkList $this->bookmarks
 */
use Application\Templates\Helpers\ButtonLink;

$c = count($this->bookmarks);

if ($c) {
	echo "
	<section class=\"bookmarkList\">
        <header><h1>{$this->template->title}</h1></header>
		<ul>
 	";
		foreach ($this->bookmarks as $bookmark) {
			$name = self::escape($bookmark->getName());

			$h = $this->template->getHelper('buttonLink');
			$button = $h->buttonLink(
				BASE_URI."/bookmarks/delete?bookmark_id={$bookmark->getId()};return_url=$_SERVER[REQUEST_URI]",
				$this->_('delete'),
				'delete',
				ButtonLink::SIZE_ICON
			);

			echo "<li><a href=\"{$bookmark->getRequestUri()}\">$name</a> $button</li>";
		}
	echo "
		</ul>
	</section>
	";
}
