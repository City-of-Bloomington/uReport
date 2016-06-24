<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
use Blossom\Classes\Block;
use Blossom\Classes\View;

$issueHeader = new Block('tickets/partials/issueHeader.inc', array('disableButtons'=>true));
$thumbnail   = new Block('media/thumbnail.inc');
echo "
<div class=\"thumbnails\">
	<p><a href=\"{$this->ticket->getURL()}\">{$this->_('labels.back_to_ticket')}</a></p>
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
			<p>{$this->_('messages.click_full_size')}</p>
			$header
			$html
		</div>
		";
	}
}
echo "
</div>
";
