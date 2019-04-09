<?php
require ('vendor/autoload.php');

use Symfony\Component\Process\Process;

/**
 * Helper. Prints out formatted date/time.
 * 
 * @return none
 */
function logTime() {
    echo '<br/>';
    echo date('d-m-Y h:i:s a', time());    
    echo '<br/>';
}

/**
 * Processes the file at $inputPath and writes output to $outputPath.
 *
 * Each input record runs for the time specified by its 'wait' property.
 * 
 * @param  string $inputPath  Input file path.
 * @param  string $outputPath Output file path.
 * @return none
 */
function processFile($inputPath, $outputPath) {
    // can't json_decode the whole file as 1) it's not valid JSON,
    // and 2) can run into memory issues
    $inputHandle = fopen($inputPath, 'r');

    if ($inputHandle) {
        $tasks = [];

        // read file line by line
        while (($line = fgets($inputHandle)) !== false) {
            $event = json_decode($line);

            // run a standalone PHP process to handle the line/event
            $process = new Process(['/usr/bin/php', './processor.php', $event->wait]);

            // start() triggers an async process
            $process->start(function ($type, $buffer) use ($outputPath, $line) {
                if ($buffer == 'finished') {
                    // process for this line has finished, write it to the
                    // output file
                    $outputHandle = fopen($outputPath, 'a');
                    fwrite($outputHandle, $line);
                    fclose($outputHandle);
                }
            });

            $tasks[] = $process; 

            // limit the number of file processes that will run in parallel; without
            // this, or with a limit that's too high, PHP will error out with:
            //      Warning: proc_open(): unable to create pipe Too many open files...
            if (sizeof($tasks) == 110) {
                finishTasks($tasks);
            }
        }

        // wrap up the remaining tasks
        finishTasks($tasks);

        fclose($inputHandle);
    }
}

/**
 * Executes the (blocking) wait() method on each task, to ensure output
 * isn't sent to the browser before each task has completed.
 * 
 * @param  array &$tasks List of Process classes.
 * @return none
 */
function finishTasks(&$tasks) {
    foreach ($tasks as $task) {
        $task->wait();
    }

    $tasks = [];
}

logTime();
processFile('./sample_rc.json', './output.json');
logTime();
