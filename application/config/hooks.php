<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/


// Register hook to start timer
$hook['post_controller_constructor'][] = array(
    'class'    => 'RequestTimerHook', 
    'function' => 'start_request_timer', 
    'filename' => 'RequestTimerHook.php', 
    'filepath' => 'hooks'
);

// Register hook to end the timer
$hook['post_controller'][] = array(
    'class'    => 'RequestTimerHook', 
    'function' => 'end_request_timer', 
    'filename' => 'RequestTimerHook.php', 
    'filepath' => 'hooks'
);
