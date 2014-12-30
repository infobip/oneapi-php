<?php

// Define username/password for testing or start this script from commandline with username/password cmd line args:
define('USERNAME', sizeof($argv) >= 3 ? $argv[1] : 'INSERT USERNAME HERE');
define('PASSWORD', sizeof($argv) >= 3 ? $argv[2] : 'INSERT PASSWORD HERE');

// Fill your data:
define('SENDER_ADDRESS', '');
define('DESTINATION_ADDRESS', "");
define('MO_NUMBER', '');
define('NOTIFY_URL', '');
define('MO_NOTIFY_URL', '');
define('SOCIAL_INVITES_MESSAGE_KEY', '');
define('API_KEY', '');
define('MESSAGE_TEXT', 'hello');
define('LANGUAGE_CODE', '');
define('USE_LOCKING_SHIFT', true);
define('USE_SINGLE_SHIFT', false);

define('TFA_APPLICATION_ID', '');
define('TFA_MESSAGE_ID', '');



// Uncomment the example you want to test:
//require_once 'examples/send_message_with_custom_charset.php';
//require_once 'examples/send_message.php';
//require_once 'examples/subscribe_send_message_and_wait_delivery_push.php';
//require_once 'examples/subscribe_and_unsubscribe_to_mo_events.php';
//require_once 'examples/subscribe_trigger_mo_and_unsubscribe_to_mo_events.php';
//require_once 'examples/subscribe_trigger_mo_and_retrieve_mo_message.php';
//require_once 'examples/send_hlr_and_wait_for_push.php';
//require_once 'examples/send_hlr.php';
//require_once 'examples/send_invite.php';
//require_once 'examples/get_customer_profile.php';
//require_once 'examples/check_client_valid.php';
//require_once 'examples/check_account_balance.php';
//require_once 'examples/get_inbound_messages.php';
//require_once 'examples/query_outbound_messages.php';
//require_once 'examples/send_message_with_nli_and_wait_for_delivery_push.php';
//require_once 'examples/send_message_and_wait_to_delivery_push.php';

//define('ONEAPI_BASE_URL', 'http://oneapi-test.infobip.com/');
//require_once 'examples/tfa_auth_and_verify.php';
