<?php
require_once "../src/telegram/Helper.php";
use rein\telegram\Helper;

/**
 * This is an example of creating a group.
 * The channel name and description will be requested through the command line if you run the script from the console.
 * If you open the script through the browser, then the data (channel & desc) must be sended via POST-request.
 */

$channel = $desc = null;
if(php_sapi_name() == "cli")
{
    $channel = readline("Enter channel name: ");
    $desc = readline("Enter channel desc: ");
}
else
{
    $channel = $_POST['channel'] ?? null;
    $desc = $_POST['desc'] ?? null;
}

if(empty($channel) || empty($desc))
    die("You must specify the name and description of the channel");

$url = Helper::createGroup($channel, $desc);
echo "Well done. Invite URL: $url\n";
?>

<?php if(php_sapi_name() != "cli"): ?>
    <p><a href="#" onclick="history.back()">Go back</a></p>
<?php endif ?>