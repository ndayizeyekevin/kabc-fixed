<?php
// Try to use shell_exec() to see if it's allowed
//$output = exec('echo "shell_exec is enabled"');

if ($output) {
  //  echo 'shell_exec is enabled: ' . $output;
} else {
   // echo 'shell_exec is disabled.';
}


$file = 'https://nstcr.gope.rw/codexworld.pdf';  // Specify the file to be printed
$printer = 'printer_name';         // Specify the printer name (use `lpstat -p` to find the printer name)

$command = "lp -d  $file"; // Create the command to print the file
$output = shell_exec($command);    // Execute the command

echo "Output: " . $output;         // Optionally, display the command output


?>
