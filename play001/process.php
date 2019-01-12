<?php
ini_set('display_errors', 1);
error_reporting(E_PARSE | E_ERROR);
mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Kiev');

require_once $_SERVER['DOCUMENT_ROOT'].'sbdb.php';

//echo 'Jeronimo!';

$arr = array(1, 2, 3, 'a'=>4);
echo json_encode($arr);

?>
