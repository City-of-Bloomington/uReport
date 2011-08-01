<?php
/**
 * Logs a user out of the system
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
session_destroy();
header('Location: '.BASE_URL);
