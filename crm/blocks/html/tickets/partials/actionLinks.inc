<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Application\Models\Person;
use Application\Models\Action;
?>
<div class="tools">
<?php
    $helper    = $this->template->getHelper('dropdown');
    $ticket_id = $this->ticket->getId();
    $links     = [];

	if (Person::isAllowed('tickets', 'changeStatus')) {
        if ($this->ticket->getStatus() === 'open') {
            $status = 'closed';
            $label  = $this->_('ticket_close');
        }
        else {
            $status = 'open';
            $label = $this->_('ticket_open');
        }
        $links[] = [
            'url'   => BASE_URI."/tickets/changeStatus?ticket_id=$ticket_id;status=$status",
            'label' => $label,
            'attrs' => ['data-callback'=>'changeStatus']
        ];
    }
	if (Person::isAllowed('tickets', 'delete')) {
        $links[] = [
            'url'   => BASE_URI.'/tickets/delete?ticket_id='.$ticket_id,
            'label' => $this->_('delete_ticket')
        ];
	}
	if (Person::isAllowed('tickets', 'update')) {
        $links[] = [
            'url'   => BASE_URI.'/tickets/update?ticket_id='.$ticket_id,
            'label' => $this->_('update_ticket')
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
		if (Person::isAllowed('tickets', 'respond')) {
            $links[] = [
                'url'   => BASE_URI.'/tickets/respond?ticket_id='.$ticket_id,
                'label' => $this->_('add_response')
            ];
		}
		if (Person::isAllowed('tickets', 'message')) {
            $links[] = [
                'url'   => BASE_URI.'/tickets/message?ticket_id='.$ticket_id,
                'label' => $this->_('message_send')
            ];
		}
		if (Person::isAllowed('tickets', 'recordAction')) {
            $action  = new Action(Action::COMMENTED);
            $links[] = [
                'url'   => BASE_URI."/tickets/recordAction?ticket_id=$ticket_id;action_id={$action->getId()}",
                'label' => $this->_('comment')
            ];
		}
	}

	if (isset($_SESSION['USER'])
        && $this->ticket->getStatus() != 'closed'
        && Person::isAllowed('tickets', 'recordAction')) {

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