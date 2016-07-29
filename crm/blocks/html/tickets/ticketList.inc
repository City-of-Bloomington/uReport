<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param TicketList $this->ticketList
 *
 * @param string $this->title
 * @param bool   $this->disableButtons
 * @param Ticket $this->filterTicket     A ticket to leave out of the display
 * @param string $this->moreLink
 */
use Application\Models\Person;
use Blossom\Classes\Url;

$title = $this->title ? self::escape($this->title) : $this->_(['ticket', 'tickets', count($this->ticketList)]);
?>
<section class="ticketList">
    <header>
        <h1><?= $title; ?></h1>
    </header>

	<table>
		<thead>
            <tr>
            <?php
                foreach ($this->fields as $f) {
                    echo "<th>{$this->_($f)}</th>";
                }
                echo '<th></th>';
            ?>
            </tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->ticketList as $ticket) {
				if ($this->filterTicket
					&& "{$this->filterTicket->getId()}"=="{$ticket->getId()}") {
					continue;
				}

				echo "<tr draggable=\"true\" ondragstart=\"event.dataTransfer.setData('text/plain', '{$ticket->getId()}');\">";
				foreach ($this->fields as $f) {
                    switch ($f) {
                        case 'enteredDate':
                            $value = $ticket->getEnteredDate(DATE_FORMAT);
                        break;

                        case 'slaPercentage':
                            $value = $ticket->getSlaPercentage();
                            if ($value) {
                                $class = $value<=100 ? 'onTrack' : 'pastDue';
                                $value = "<span class=\"$class\">$value%</span>";
                            }
                        break;

                        default:
                            $get   = 'get'.ucfirst($f);
                            $value = self::escape($ticket->$get());
                    }

                    if (!$this->disableLinks) {
                        $value = "<a href=\"{$ticket->getURL()}\">$value</a>";
                    }

                    echo "<td>$value</td>";
				}
				echo "<td>";
					if (!$this->disableButtons
                        #&& $ticket->getStatus() !== 'closed'
                        && $this->filterTicket
                        && !$ticket->getParent_id()
                        && Person::isAllowed('tickets', 'merge')) {

                        $uri = BASE_URI."/tickets/merge?parent_ticket_id={$this->filterTicket->getId()};child_ticket_id={$ticket->getId()}";

						$helper = $this->template->getHelper('buttonLink');
						echo $helper->buttonLink($uri, $this->_('merge'), '');
					}
				echo "</td>";
				echo "</tr>";
			}
		?>
		</tbody>
	</table>
	<?php
		if ($this->moreLink) {
			echo "
			<div>
				<a href=\"{$this->moreLink}\" class=\"more\">{$this->_('more')}</a>
			</div>
			";
		}
	?>
</section>
