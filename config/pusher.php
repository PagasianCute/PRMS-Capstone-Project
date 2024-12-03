<?php 
require __DIR__ . '../../vendor/autoload.php';

$options = array(
    'cluster' => 'ap1',
    'useTLS' => true
  );
  $pusher = new Pusher\Pusher(
    '4c140a667948d3f0c3b4',
    'eff020b05ab295524329',
    '1654996',
    $options
  );
?>