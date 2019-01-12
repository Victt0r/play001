<?php

# SERVER-SIDE OPTIONS OF clerk module

# where to find f.php, krumo etc
$commonsPath = '../../_Common/PHP/';

# table prefix
$tblPrefix = 'play001_';

# name of the users-table in the database
$tblUsers = $tblPrefix.'users';
//$tblUsers = 'users';

# name of the session-table in the database
$tblSessions = $tblPrefix.'sessions';
//$tblSessions = 'sessions';

# number of sessions at the same time per user
$sessNum = 3;

# days until session expires
$sessExpire = 5;

# to use or not to use browser footprint (user agent string and/or IP) in check
$checkAgent = true;
//$checkAgent = false;
$checkIP = true;
//$checkIP = false;


?>
