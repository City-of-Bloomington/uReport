<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Blossom\Classes\Block;

$issueHeader = new Block('tickets/partials/issueHeader.inc', ['disableButtons'=>true]);
$thumbnail   = new Block('media/thumbnail.inc');
echo "
<div class=\"thumbnails\">
	<p><a href=\"{$this->ticket->getURL()}\">{$this->_('back_to_ticket')}</a></p>
";
foreach ($this->ticket->getIssues() as $issue) {
	$html = '';
	foreach ($issue->getMedia() as $media) {
		if ($media->getMedia_type() == 'image') {
			$thumbnail->media = $media;
			$html.= $thumbnail->render('html', $this->template);
		}
	}
	if ($html) {
		$issueHeader->issue = $issue;
		$header = $issueHeader->render('html', $this->template);
		echo "
		<div class=\"issueInfo\">
			<p>{$this->_('click_full_size', 'messages')}</p>
			$header
			$html
		</div>
		";
	}
}
echo "
</div>
";
