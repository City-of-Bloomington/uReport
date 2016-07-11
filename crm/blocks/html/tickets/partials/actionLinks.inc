<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Application\Models\Person;
?>
<div class="tools">
<?php
    $helper    = $this->template->getHelper('dropdown');
    $ticket_id = $this->ticket->getId();
    $links     = [];

	if (Person::isAllowed('tickets', 'changeStatus')) {
        $links[] = [
            'url'   => BASE_URI.'/tickets/changeStatus?ticket_id='.$ticket_id,
            'label' => $this->_('change_status'),
            'attrs' => ['data-callback'=>'changeStatus']
        ];
    }
	if (Person::isAllowed('tickets', 'delete')) {
        $links[] = [
            'url'   => BASE_URI.'/tickets/delete?ticket_id='.$ticket_id,
            'label' => $this->_('delete_ticket')
        ];
	}
	if ($this->ticket->getStatus() != 'closed') {
        if (Person::isAllowed('tickets', 'changeCategory')) {
            $links[] = [
                'url'   => BASE_URI.'/tickets/changeCategory?ticket_id='.$ticket_id,
                'label' => $this->_('change_category'),
                'attrs' => ['data-callback'=>'changeCategory']
            ];
        }
        if (Person::isAllowed('tickets', 'changeLocation')) {
            $links[] = [
                'url'   => BASE_URI.'/tickets/changeLocation?ticket_id='.$ticket_id,
                'label' => $this->_('change_location'),
                'attrs' => ['data-callback'=>'changeLocation']
            ];
        }
		if (Person::isAllowed('tickets', 'assign')) {
            $links[] = [
                'url'   => BASE_URI.'/tickets/assign?ticket_id='.$ticket_id,
                'label' => $this->_('assign_ticket'),
                'attrs' => ['data-callback'=>'assign']
            ];
		}
	}

	if (isset($_SESSION['USER'])) {
        $department = $_SESSION['USER']->getDepartment_id()
            ? $_SESSION['USER']->getDepartment()
            : new Department();

        $actions = $department->getActions();
        $c       = count($actions);
        if ($c) {
            foreach ($actions as $a) {
                $links[] = [
                    'url'   => BASE_URI."/tickets/recordAction?ticket_id=$ticket_id;action_id={$a->getId()}",
                    'label' => $a->getName()
                ];
            }
        }
	}

	if (count($links)) {
        echo $helper->dropdown($links, $this->_(['action', 'actions', count($links)]), "ticketActions_$ticket_id");
    }
?>
</div>