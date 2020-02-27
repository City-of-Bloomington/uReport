<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket  $this->ticket
 * @param bool    $this->disableButtons (optional)
 */
use Application\Models\Person;
use Blossom\Classes\Block;

$helper = $this->template->getHelper('buttonLink');

$this->mediaList = $this->ticket->getMedia();

if (count($this->mediaList)) {
	echo "
	<div class=\"issueAttachments\">
        <header>
            <h2>{$this->_(['media', 'medias', 2])}</h2>
        </header>
	";
		$profileImage = $this->ticket->getProfileImage();
		if ($profileImage) {
			echo "
			<div>
				<a href=\"".BASE_URI."/tickets/thumbnails?ticket_id={$this->ticket->getId()}\">
					{$this->_('see_all_thumbnails')}
				</a>
			</div>
			";
			$thumbnail = new Block('media/thumbnail.inc', [
                'media'          => $profileImage,
                'disableButtons' => $this->disableButtons
            ]);
			echo $thumbnail->render('html', $this->template);
		}
		$this->_include('media/mediaList.inc');
	echo "</div>";
}
