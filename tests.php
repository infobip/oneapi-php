<?

/*
$fails = array();

function assertFailed($param) {
    global $fails;
    $fails[] = $param;
}
*/

assert_options(ASSERT_ACTIVE, true);
//assert_options(ASSERT_WARNING, true);
assert_options(ASSERT_CALLBACK, 'assertFailed');

// Remove firtst arg (script name):
array_shift($argv);

$files = array();

if($argv) {
    // If tests given in command line, use them:
    $files = $argv;
} else {
    // Otherwise run all found in tests/
    $path = dirname(__FILE__).'/tests';
    $handle = opendir($path);
    print_r($handle);
    while (false !== ($entry = readdir($handle))) {
        if(preg_match('/^.*\.php$/', $entry)) {
            $files[] = 'tests/' . $entry;
        }
    }
}

foreach($files as $file) {
    require_once $file;
}

/*
echo "\n";
if($fails) {
    foreach($fails as $fail)
        print 'Failed:'.$fail."\n";
}
*/
