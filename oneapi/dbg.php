<?php

/**
 * PHP command line "debugger": start with:
 * require_once 'oneapi/dbg.php';
 * __dbg(get_defined_vars());
 */

function __dbg($vars) {
    $run = true;
    while($run) {
        $line = readline('? ');
        $line = @trim($line);
        $startOfCommand = trim(@substr($line, 0, 2));
        $args = trim(@substr($line, 2));
        if(!$startOfCommand) {
            echo "h for help\n";
        } else if($startOfCommand == 'h') {
            echo "h -> help\n";
            echo "l -> list code\n";
            echo "p <expression> -> print expression value\n";
            echo "s -> show stack trace\n";
            echo "c -> continue\n";
        } else if($startOfCommand == 'l') {
            $backTrace = debug_backtrace();
            $backTraceElement = $backTrace[0];
            $file = $backTraceElement['file'];
            $lineNumber = $backTraceElement['line'];
            echo "File:", $file;
            echo "Line:", $lineNumber;
            $lines = file($file);
            for($i = 0; $i < sizeof($lines); $i++) {
                if($lineNumber - 6 <= $i && $i < $lineNumber + 5) {
                    echo trim($i . ':' . $lines[$i]);
                    if($i == $lineNumber - 1)
                        echo " <----- HERE YOU ARE !\n";
                    else
                        echo "\n";
                }
            }
        } else if($startOfCommand == 'p') {
            try {
                $evalResult = null;
                $evalExpression = preg_replace('/(\$)(\w+)/', '\$vars[\'$2\']', $args);
                //echo "Evaluating expression:", $evalExpression, "\n";
                eval('$evalResult = ' . $evalExpression . ';');
                echo "\n = ", print_r($evalResult, true), "\n";
                /*
                if(array_key_exists($args, $vars))
                    echo "\n = ", var_dump($vars[$args]), "\n";
                else
                    echo "\n undefined variable\n";
                */
            } catch(Exception $e) {
                echo "Invalid expression:", $args, "\n";
            }
        } else if($startOfCommand == 's') {
            $backTrace = debug_backtrace();
            for($i = 1; $i < sizeof($backTrace); $i++) {
                $backTraceElement = $backTrace[$i];
                $file = @$backTraceElement['file'];
                $lineNumber = @$backTraceElement['line'];
                $function = @$backTraceElement['function'];
                $class = @$backTraceElement['class'];
                $functionArgs = @$backTraceElement['args'];
                echo $file . '(' . $lineNumber . "):\n";
                if($function)
                    echo "\tfunction: ", $function, "\n";
                if($class)
                    echo "\tclass: ", $class, "\n";
                if($functionArgs) {
                    $functionArgs = str_replace("\n", ' ', print_r($functionArgs, true));
                    $functionArgs = preg_replace('/\s+/', ' ', $functionArgs);
                    echo "\targs: ", $functionArgs, "\n";
                }
                echo $file . '(' . $lineNumber . "):\n";
            }
        } else if($startOfCommand == 'c') {
            $run = false;
        }
    }
}

