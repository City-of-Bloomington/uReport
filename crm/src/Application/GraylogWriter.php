<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application;

use Gelf\Publisher;
use Gelf\Message;
use Gelf\Transport\UdpTransport;

use Laminas\Log\Writer\AbstractWriter;

class GraylogWriter extends AbstractWriter
{
    private $publisher;

    public function __construct(string $url, int $port)
    {
        $this->url = $url;

        $transport = new UdpTransport($url, $port, UdpTransport::CHUNK_SIZE_LAN);
        $this->publisher = new Publisher();
        $this->publisher->addTransport($transport);
    }

    public function doWrite(array $event)
    {
        $message = new Message();
        if (!empty($event['message'])) { $message->setShortMessage($event['message']); }
        if (!empty($event['errno'  ])) { $message->setLevel       ($event['errno'  ]); }
        if (!empty($event['file'   ])) { $message->setFile        ($event['file'   ]); }
        if (!empty($event['line'   ])) { $message->setLine        ($event['line'   ]); }

        if (!empty($event['priority'     ])) { $message->setLevel($event['priority']); }
        if (!empty($event['extra']['file'])) { $message->setFile ($event['extra']['file']); }
        if (!empty($event['extra']['line'])) { $message->setLine ($event['extra']['line']); }
        $message->setAdditional('base_uri', BASE_URI);
        $message->setFullMessage(print_r($event, true));

        $this->publisher->publish($message);
    }
}
