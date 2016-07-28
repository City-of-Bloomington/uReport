<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Blossom\Classes\Block;

$thumbnail   = new Block('media/thumbnail.inc');
echo "
<div class=\"thumbnails\">
    <header>
        <div class=\"tools\">
            <a href=\"{$this->ticket->getURL()}\">{$this->_('back_to_ticket')}</a>
        </div>
    </header>
";
$html = '';
foreach ($this->ticket->getMedia() as $media) {
    if ($media->getMedia_type() == 'image') {
        $thumbnail->media = $media;
        $html.= $thumbnail->render('html', $this->template);
    }
}
if ($html) {
    echo "
    <p>{$this->_('click_full_size', 'messages')}</p>
    $html
    ";
}
echo "
</div>
";
