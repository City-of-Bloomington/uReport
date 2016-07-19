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
							&& Person::isAllowed('issues', 'update')
							&& $ticket->getStatus() !== 'closed') {

						$url = new Url(BASE_URL.'/issues/update');
						$url->ticket_id = $ticket->getId();
						if (isset($_GET['person_id'])) {
							try {
								$person = new Person($_GET['person_id']);
								$url->person_id = "{$person->getId()}";
							}
							catch (Exception $e) {
								// Just ignore invalid people
							}
						}
						$helper = $this->template->getHelper('buttonLink');
						echo $helper->buttonLink($url, $this->_('add_issue'), 'add');
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
