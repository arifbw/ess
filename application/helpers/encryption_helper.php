<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Encrypt string using CI Encrypt library
 * 
 * @param string $data Data yang akan dienkripsi
 * @return string Data terenkripsi
 */
if (!function_exists('encrypt_data_ess')) {
    function encrypt_data_ess($data) {
        $CI =& get_instance(); // Mendapatkan instance CodeIgniter
        $CI->load->library('encryption'); // Memastikan library encryption sudah dimuat
        return $CI->encryption->encrypt($data); // Mengenkripsi data
    }
}

/**
 * Decrypt string using CI Encrypt library
 * 
 * @param string $data Data terenkripsi
 * @return string Data asli setelah didekripsi
 */
if (!function_exists('decrypt_data_ess')) {
    function decrypt_data_ess($data) {
        $CI =& get_instance(); // Mendapatkan instance CodeIgniter
        $CI->load->library('encryption'); // Memastikan library encryption sudah dimuat
        return $CI->encryption->decrypt($data); // Mendekripsi data
    }
}