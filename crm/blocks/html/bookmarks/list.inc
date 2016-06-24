<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param BookmarkList $this->bookmarks
 */
use Blossom\Classes\View;
use Application\Templates\Helpers\ButtonLink;

$title = !empty($this->title)
	? self::escape($this->title)
	: $this->_(array('bookmark', 'bookmarks', 2));
if (count($this->bookmarks)) {
	echo "
	<div class=\"bookmarkList\">
		<h3>$title</h3>
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
	</div>
	";
}
