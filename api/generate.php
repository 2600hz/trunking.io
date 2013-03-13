<?php 

/**
 * Api class that will generate a random username and a random password
 *
 * @author Francis Genet
 * @package Trunking.io
 * @version 1.0
 */

class Generate {
    /**
     * will return an object with the generated information 
     *
     * @url GET /
     */
    function get() {
        $objSettings = new Settings;
        $settings = $objSettings->get_settings();

        $username = Utils::get_random('user_', 8);
        $password = Utils::get_random('', 10);
        $ip = $_SERVER['SERVER_ADDR'];

        return array(
            'data' => array(
                'username' => $username,
                'password' => $password,
                'ip' => $ip
            )
        );
    }
}

 ?>