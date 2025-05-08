<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RequestTimerHook {

    public function __construct() {
        // Constructor will load only once, no need for CI instance yet
    }

    public function start_request_timer () {
        // Check if debug_request_time is set in the URL
        if (isset($_GET['debug_request_time']) && $_GET['debug_request_time'] == 'true') {
            // Start the timer at the beginning of each request
            debug_start_timer();
        }
    }

    // End the timer after the request has been processed
    public function end_request_timer() {
        // Check if debug_request_time is set in the URL
        if (isset($_GET['debug_request_time']) && $_GET['debug_request_time'] == 'true') {
            // End the timer after processing the request
            debug_end_timer();
        }
    }
}
