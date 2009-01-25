<?

require_once("log.php");
require_once("authentication.php");
require_once("db.php");

function greet()
{
    slog('----------------------------------------------------------------------');
    slog(config()->app_name. ' session starting.. '.$_SERVER["REQUEST_URI"]);
    slog("POST: ".print_r($_POST, true));
}

function remove_quotes()
{
    if(  ( function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc() ) || ini_get('magic_quotes_sybase')  ){
        foreach($_GET as $k => $v) $_GET[$k] = stripslashes($v);
        foreach($_POST as $k => $v) $_POST[$k] = stripslashes($v);
        foreach($_COOKIE as $k => $v) $_COOKIE[$k] = stripslashes($v);
        foreach($_REQUEST as $k => $v) $_REQUEST[$k] = stripslashes($v);
    }
}

function sendSMS($users, $subject, $body)
{
    $recipient = "";
    foreach($users as $user) {
        if(isset(config()->sms_address[$user])) {
            if($recipient != "") {
                $recipient .= ", ";
            }
            $recipient .= config()->sms_address[$user];
        } else {
            elog("couldn't find user $user in sms_address configuration");
        }
    }
    $Name = config()->app_name;
    $email = "hayashis@indiana.edu"; //senders e-mail adress
    $header = "From: ". $Name . " <" . $email . ">\r\n";
    mail($recipient, $subject, $body, $header);

    dlog("Sent SMS notification to $recipient user:".print_r($users, true));
}
