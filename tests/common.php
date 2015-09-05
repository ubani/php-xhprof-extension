<?php

/**
 * Print xhprof raw data (essentially a callgraph) in a canonical style,
 * so that even if the ordering of things within the raw_data (which is
 * an associative array) changes in future implementations the output
 * remains stable.
 *
 * Also, suppress output of variable quantities (such as time, memory)
 * so that reference output of tests doesn't need any additional masking.
 *
 * @author Kannan
 */
function print_canonical($xhprof_data)
{
    // some functions are not part of the trace
    // due to being an opcode in php7, these
    // need to be ignored for a common test-output
    $ignoreFunctions = array('strlen', 'is_array');

    ksort($xhprof_data);
    foreach($xhprof_data as $func => $metrics) {
        foreach ($ignoreFunctions as $ignoreFunction) {
            if (strpos($func, "==>" . $ignoreFunction) !== false) {
                continue 2;
            }
        }

        echo str_pad($func, 40) . ":";
        ksort($metrics);
        foreach ($metrics as $name => $value) {

            // Only call counts are stable.
            // Wild card everything else. We still print
            // the metric name to ensure it was collected.
            if ($name != "ct") {
                $value = "*";
            } else {
                $value = str_pad($value, 8, " ", STR_PAD_LEFT);
            }

            echo " {$name}={$value};";
        }
        echo "\n";
    }
}


function print_spans($spans)
{
    foreach ($spans as $span) {
        ksort($span['a']);
        $annotations = '';
        foreach ($span['a'] as $k => $v) {
            $annotations .= "$k=$v ";
        }

        printf("%s: %d timers - %s\n", $span['n'], count($span['b']), rtrim($annotations));
    }
}
