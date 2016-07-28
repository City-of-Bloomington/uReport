<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket   $this->ticket
 * @param boolean $this->disableButtons (optional)
 */
use Application\Models\Person;
use Blossom\Classes\Block;

$helper = $this->template->getHelper('buttonLink');

$uploadButton = '';
if (!$this->disableButtons && Person::isAllowed('media','upload')) {
    $uploadButton = $helper->buttonLink(
        BASE_URI.'/media/upload?ticket_id='.$this->ticket->getId(),
        $this->_('upload_media'),
        'upload'
    );
}

$this->mediaList = $this->ticket->getMedia();

if ($uploadButton || count($this->mediaList)) {
	echo "
	<div class=\"issueAttachments\">
        <header>
            <h2>{$this->_(['media', 'medias', 2])}</h2>
            <div class=\"tools\">$uploadButton</div>
        </header>
	";
		$profileImage = $this->ticket->getProfileImage();
		if ($profileImage) {
			$thumbnail = new Block('media/thumbnail.inc', ['media'=>$profileImage]);
			echo "
			<div>
				<a href=\"".BASE_URI."/tickets/thumbnails?ticket_id={$this->ticket->getId()}\">
					{$this->_('see_all_thumbnails')}
				</a>
			</div>
			";
			echo $thumbnail->render('html', $this->template);
		}
		$this->_include('media/mediaList.inc');
	echo "</div>";
}
