<?php
include_once('DonorPerfect.php');

set_time_limit(0);
error_reporting(E_ALL);

$donors = DonorPerfect::listDonors();
print_r($donors);
exit;
?>