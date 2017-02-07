<?php

$link = mysqli_init();
mysqli_real_connect($link, $argv[1], getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'), null, null, null, 0);

?>
