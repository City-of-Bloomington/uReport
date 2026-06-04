<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Ticket;
use Application\Models\Media;
use Application\Models\Action;
use Application\Models\TicketHistory;

use Application\Block;
use Application\Controller;

class MediaController extends Controller
{
    /**
     * @param string $id
     * @return Ticket
     */
    private function loadTicket($id)
    {
        try { return new Ticket($id); }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
            header('Location: '.BASE_URL);
            exit();
        }
    }

    public function index()
    {
    }

    public function delete()
    {
        $media = new Media($_GET['media_id']);
        $ticket = $media->getTicket();
        $media->delete();

        header('Location: '.$ticket->getURL());
        exit();
    }

    public function upload()
    {
        $ticket = $this->loadTicket($_REQUEST['ticket_id']);

        if (isset($_FILES['attachment'])) {
            $media = new Media();
            $media->setTicket($ticket);

            $history = new TicketHistory();
            $history->setTicket($media->getTicket());
            $history->setAction(new Action(Action::UPLOADED_MEDIA));
            $history->setData(['media_id'=>$media->getId()]);

            try {
                $media->setFile($_FILES['attachment']);
                $media->save();

                $history->save();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
                // Clean out any file that might have been saved
                try {
                    $media->delete();
                }
                catch (\Exception $e) {
                }
            }

            header('Location: '.$ticket->getURL());
            exit();
        }

        $this->template->setFilename('tickets');
        $this->template->title = $this->template->_('upload_media');
        $this->template->blocks[] = new Block('tickets/ticketInfo.inc', ['ticket'  => $ticket, 'disableButtons'=>1]);
        $this->template->blocks[] = new Block('media/uploadForm.inc',   ['ticket'  => $ticket]);
        $this->template->blocks[] = new Block('ticketHistory/info.inc', ['history' => $ticket->getHistory()]);
    }

    /**
     * Create and cache a resized image file
     */
    public function resize()
    {
        $this->template->setFilename('media');
        try {
            $media = new Media($_REQUEST['media_id']);
            $size = !empty($_REQUEST['size']) ? (int)$_REQUEST['size'] : null;
            $this->template->blocks[] = new Block('media/image.inc', ['media'=>$media, 'size'=>$size]);
        }
        catch (\Exception $e) {
            header('HTTP/1.1 404 Not Found', true, 404);
        }
    }
}
