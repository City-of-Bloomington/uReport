<?php
use Application\Models\Person;
?>
<nav id="nav1">
    <div class="container">
	<?php
        $li = '<a href="%s">%s</a>';
        echo     sprintf($li, BASE_URI.'/tickets'  ,   $this->_(['ticket'  ,'tickets'  , 2]));
        echo     sprintf($li, BASE_URI.'/locations',   $this->_(['location','locations', 2]));
        if (Person::isAllowed('people', 'view')) {
            echo sprintf($li, BASE_URI.'/people',      $this->_(['person'  ,'people'   ,2]));
        }
        if (Person::isAllowed('tickets', 'add')) {
            echo sprintf($li, BASE_URI.'/tickets/add', $this->_('add_new_ticket'));
        }
        if (Person::isAllowed('reports')) {
            echo sprintf($li, BASE_URI.'/reports',     $this->_(['report', 'reports', 2]));
        }
    ?>
    </div>
</nav>