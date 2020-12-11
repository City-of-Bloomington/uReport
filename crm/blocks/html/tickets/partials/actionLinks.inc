<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->ticket
 */
use Application\Models\Person;
use Application\Models\Action;
use Application\Models\Department;

    $helper    = $this->template->getHelper('dropdown');
    $ticket_id = $this->ticket->getId();
    $links     = [];
    $status    = $this->ticket->getStatus();



    if ($status === 'open') {
        $subgroup = [];
		if (Person::isAllowed('tickets', 'recordAction')) {
            $action  = new Action(Action::COMMENTED);
            $subgroup[] = [
                'url'   => BASE_URI."/tickets/recordAction?ticket_id=$ticket_id;action_id={$action->getId()}",
                'label' => $this->_('comment')
            ];
		}
		if (Person::isAllowed('tickets', 'assign')) {
            $subgroup[] = [
                'url'   => BASE_URI.'/tickets/assign?ticket_id='.$ticket_id,
                'label' => $this->_('assign_ticket'),
                'attrs' => ['data-callback'=>'assign']
            ];
		}
		if (count($subgroup)) { $links[] = ['subgroup'=>$subgroup]; }
    }

    #---
    $subgroup = [];
    if (Person::isAllowed('tickets', 'message')) {
        $subgroup[] = [
            'url'   => BASE_URI.'/tickets/message?ticket_id='.$ticket_id,
            'label' => $this->_('message_send')
        ];
    }
    if (Person::isAllowed('tickets', 'respond')) {
        $subgroup[] = [
            'url'   => BASE_URI.'/tickets/respond?ticket_id='.$ticket_id,
            'label' => $this->_('add_response')
        ];
    }
    if ($status === 'open' && Person::isAllowed('tickets', 'close')) {
        $subgroup[] = [
            'url'   => BASE_URI."/tickets/close?ticket_id=$ticket_id;status=closed",
            'label' => $this->_('ticket_close')
        ];
    }
    if (count($subgroup)) { $links[] = ['subgroup'=>$subgroup]; }

    if ($status === 'open') {
		#---
		$subgroup = [];
        if (Person::isAllowed('tickets', 'update')) {
            $subgroup[] = [
                'url'   => BASE_URI.'/tickets/update?ticket_id='.$ticket_id,
                'label' => $this->_('update_ticket')
            ];
        }
        if (Person::isAllowed('tickets', 'changeCategory')) {
            $subgroup[] = [
                'url'   => BASE_URI.'/tickets/changeCategory?ticket_id='.$ticket_id,
                'label' => $this->_('change_category'),
                'attrs' => ['data-callback'=>'changeCategory']
            ];
        }
        if (Person::isAllowed('tickets', 'changeLocation')) {
            $subgroup[] = [
                'url'   => BASE_URI.'/tickets/changeLocation?ticket_id='.$ticket_id,
                'label' => $this->_('change_location'),
                'attrs' => ['data-callback'=>'changeLocation']
            ];
        }
        if (Person::isAllowed('media', 'upload')) {
            $subgroup[] = [
                'url'   => BASE_URI.'/media/upload?ticket_id='.$ticket_id,
                'label' => $this->_('upload_media')
            ];
        }
        if (count($subgroup)) { $links[] = ['subgroup'=>$subgroup]; }

        if (isset($_SESSION['USER']) && Person::isAllowed('tickets', 'recordAction')) {
            $department = $_SESSION['USER']->getDepartment_id()
                ? $_SESSION['USER']->getDepartment()
                : new Department();

            $actions = $department->getActions();
            $c       = count($actions);
            if ($c) {
                $subgroup = [];
                foreach ($actions as $a) {
                    $links[] = [
                        'url'   => BASE_URI."/tickets/recordAction?ticket_id=$ticket_id;action_id={$a->getId()}",
                        'label' => $a->getName()
                    ];
                }
                if (count($subgroup)) { $links[] = ['subgroup'=>$subgroup]; }
            }
        }
    }

    $subgroup = [];
    if ($status != 'open' && Person::isAllowed('tickets', 'open')) {
        $subgroup[] = [
            'url'   => BASE_URI."/tickets/open?ticket_id=$ticket_id;status=open",
            'label' => $this->_('ticket_open')
        ];
    }
	if (Person::isAllowed('tickets', 'delete')) {
        $subgroup[] = [
            'url'   => BASE_URI.'/tickets/delete?ticket_id='.$ticket_id,
            'label' => $this->_('delete_ticket')
        ];
	}
	if (count($subgroup)) { $links[] = ['subgroup'=>$subgroup]; }

	if (count($links)) {
        echo $helper->dropdown($links, $this->_(['action', 'actions', count($links)]), "ticketActions_$ticket_id");
    }
