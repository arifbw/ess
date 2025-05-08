<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_table_monitoring_socnet extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->api_url = config_item('api_sosnet');
        $this->api_base_url = $this->api_url . "/api/ess/postingan/";
    }

    public function get_posts($params) {
        $url = $this->api_base_url . "get_posts/";

        // Initialize cURL
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Add query parameters
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        }

        // Execute the request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the JSON response
        return json_decode($response, true);
    }

    public function get_reports($post_id) {
        $url = $this->api_base_url . "get_reports/" . $post_id;

        // Initialize cURL
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the JSON response
        return json_decode($response, true);
    }

    public function get_comments($post_id) {
        $url = $this->api_base_url . 'get_comments/' . $post_id;
        return $this->send_request($url);
    }

    public function toggle_post($post_id, $no_pokok) {
        $url = $this->api_base_url . 'toggle_post/' . $post_id;
        $data = ['no_pokok' => $no_pokok];
        return $this->send_request($url, $data);
    }

    public function hide_comment($comment_id, $no_pokok) {
        $url = $this->api_base_url . 'hide_comment/' . $comment_id;
        $data = ['no_pokok' => $no_pokok];
        return $this->send_request($url, $data);
    }

    public function unhide_comment($comment_id) {
        $url = $this->api_base_url . 'unhide_comment/' . $comment_id;
        return $this->send_request($url);
    }

    private function send_request($url, $data = []) {
        // Assuming you're using cURL or similar method to send the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}
?>
