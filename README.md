#### Summary

Written using the `symfony/process` package, which provides nicer APIs (than PHP natives) to the system-execution processes within PHP. Script executes a standalone, asynchronous PHP process for every record within the input file, and lets it run for the length of time specified in the `wait` property. There is an artificial limit of 110 concurrent processes. 

The total sum of `wait` values (across 620 records) in the provided input file is just over 17 minutes of cumulative runtime. Script execution on my machine takes roughly 63 seconds.

#### Instructions

Run the index.php script. 

Script was only tested in-browser, on a PHP 7 environment within Laravel Valet.

#### Room for improvement...

Handling:

- bad file paths
- bad input/output files
- unexpected format of input records
- unexpected `wait` values
- script interruption; incremental processes
- process exceptions


