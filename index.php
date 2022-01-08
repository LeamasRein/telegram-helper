<?php
require_once "src/telegram/Helper.php";
if(php_sapi_name() == "cli")
    die("Use examples from 'samples' folder.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Telegram Helper Test</title>
</head>
<body>

    <?php if(rein\telegram\Helper::isGuest()): ?>

        <p>
            Your app has not been initiated yet.<br/>
            The first launch will take long time and you will need to enter the login and code from the telegram account.
        </p>
        <p>Click <a href="samples/init.php">here</a> to log in.</p>

    <?php else: ?>

        <p>Specify the fields for creating a group:</p>
        <form method="POST" action="samples/create-group.php">
            <p>
                <label>Channel name:</label> <br/>
                <input type="text" name="channel" />
            </p>
            <p>
                <label>Channel description:</label> <br/>
                <input type="text" name="desc" />
            </p>
            <p><button type="submit">Create</button>
        </form>

    <?php endif ?>


</body>
</html>