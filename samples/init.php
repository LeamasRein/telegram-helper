<?php
require_once "../src/telegram/Helper.php";
use rein\telegram\Helper;

/**
 * This is an example of script initialization. It must be completed at least once.
 * In the process, the script will request a phone number and an access code from your telegram account.
 * 
 * The first time the library is installed, it may take time. After authorization, you will have a quick response.
 */

set_time_limit(0);

Helper::createGroup('test', 'test');
?>

<?php if(php_sapi_name() != "cli"): ?>
    <p>Well done. Test channel was created.</p>
    <p><a href="../index.php"">Go back</a></p>
<?php endif ?>