<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Application\Models\Media;

function return_bytes($size)
{
    switch (substr($size, -1)) {
        case 'M': return (int)$size * 1048576;
        case 'K': return (int)$size * 1024;
        case 'G': return (int)$size * 1073741824;
        default:  return (int)$size;
    }
}
$upload_max_size  = ini_get('upload_max_filesize');
  $post_max_size  = ini_get('post_max_size');
$upload_max_bytes = return_bytes($upload_max_size);
  $post_max_bytes = return_bytes(  $post_max_size);

if ($upload_max_bytes < $post_max_bytes) {
    $maxSize  = $upload_max_size;
    $maxBytes = $upload_max_bytes;
}
else {
    $maxSize  = $post_max_size;
    $maxBytes = $post_max_bytes;
}

$accept = [];
foreach (Media::$mime_types as $mime=>$ext) {
    $accept[] = ".$ext";
}
$accept = implode(',', $accept);

$maxSize = sprintf($this->_('file_max_size',    'messages'), $maxSize);
$accept  = sprintf($this->_('file_type_accept', 'messages'), $accept);
?>
<div class="uploadForm">
	<form method="post" action="<?= BASE_URL; ?>/media/upload" enctype="multipart/form-data">
		<fieldset><legend><?= $this->_('upload_media'); ?></legend>
			<input name="ticket_id"   type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<input name="attachment" type="file" accept="<?= $accept; ?>" />
			<button type="submit" class="upload"><?= $this->_('upload'); ?></button>
			<?php
				$h = $this->template->getHelper('buttonLink');
				echo $h->buttonLink($this->ticket->getURL(), $this->_('cancel'), 'cancel' );

                echo "
                <input name=\"MAX_FILE_SIZE\" type=\"hidden\" value=\"$maxBytes\" />
                <div>$maxSize</div>
                <div>$accept</div>
                ";

			?>
		</fieldset>
	</form>
</div>
