<?php

# whitelisted tables/fields

# guests accessible tables/fields
$freeAccess =
  array('news' => array('title', 'post', 'dt_create', 'dt_modify'));

# any user accessible tables/fields
$userAccess = array('users' => array('login', 'dt_create'));

# privately accessible tables/fields
$privAccess = array('users' => array('id', 'confidence', 'dt_modify'),
                    'sessions' => array('dt_create', 'dt_modify'));


?>
