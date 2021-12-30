<?php
require_once('sess.php');

sessLogout();
header('location: index.php');
die();
