 _ __   _____  _____ _   _ ___ 
| '_ \ / _ \ \/ / __| | | / __|
| | | | (_) >  <\__ \ |_| \__ \
|_| |_|\___/_/\_\___/\__, |___/
                      __/ |    
                     |___/     

## 0x01 About
PHP web shell that **does not** make use of the program execution functions aka the following:
```
escapeshellarg — Escape a string to be used as a shell argument
escapeshellcmd — Escape shell metacharacters
exec — Execute an external program
passthru — Execute an external program and display raw output
proc_close — Close a process opened by proc_open and return the exit code of that process
proc_get_status — Get information about a process opened by proc_open
proc_nice — Change the priority of the current process
proc_open — Execute a command and open file pointers for input/output
proc_terminate — Kills a process opened by proc_open
shell_exec — Execute command via shell and return the complete output as a string
system — Execute an external program and display the output
```

## 0x02 Why ?
For the corner cases when you can upload a php file, but cannot get a shell using `system`, `shell`, `shell_exec`, etc. because the functions are disabled for whatever reasons. 

## 0x03 Supported functions

## 0x04 Install/Usage
1. Upload `noxsys.php` to the server
2. Access the url
3. Enjoy

