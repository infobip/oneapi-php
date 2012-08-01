<?php

// Define username/password for testing or start this script from commandline with username/password cmd line args:
define('USERNAME', sizeof($argv) >= 3 ? $argv[1] : 'FILL_USERNAME_HERE!!!!!');
define('PASSWORD', sizeof($argv) >= 3 ? $argv[2] : 'FILL_PASSWORD_HERE!!!!!');

// Fill your data:
define('SENDER_ADDRESS', '');
define('DESTINATION_ADDRESS', '');
define('MO_NUMBER', '');
define('NOTIFY_URL', '');
define('MO_NOTIFY_URL', '');

// Uncomment the example you want to test:
//require_once 'examples/send_message.php';
//require_once 'examples/subscribe_send_message_and_wait_delivery_push.php';
//require_once 'examples/subscribe_and_unsubscribe_to_mo_events.php';
//require_once 'examples/subscribe_trigger_mo_and_unsubscribe_to_mo_events.php';
//require_once 'examples/subscribe_trigger_mo_and_retrieve_mo_message.php';
//require_once 'examples/send_hlr_and_wait_for_push.php';
//require_once 'examples/send_hlr.php';
//require_once 'examples/get_customer_profile.php';
//require_once 'examples/check_client_valid.php';
