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

        $username = $this->_get_random('user_', 8);
        $password = $this->_get_random('', 10);
        $ip = $_SERVER['SERVER_ADDR'];

        return array(
            'data' => array(
                'username' => $username,
                'password' => $password,
                'ip' => $ip,
                'version' => $settings->version
            )
        );
    }

    private function _get_random($prefix, $length) {
        $lowercase = "qwertyuiopasdfghjklzxcvbnm";
        $uppercase = "ASDFGHJKLZXCVBNMQWERTYUIOP";
        $numbers = "1234567890";
        $randomCode = "";
        mt_srand(crc32(microtime()));
        
        $max = strlen($lowercase) - 1;
        for ($x = 0; $x < abs($length/3); $x++) {
            $randomCode .= $lowercase{mt_rand(0, $max)};
        }

        $max = strlen($uppercase) - 1;
        for ($x = 0; $x < abs($length/3); $x++) {
            $randomCode .= $uppercase{mt_rand(0, $max)};
        }

        $max = strlen($numbers) - 1;
        for ($x = 0; $x < abs($length/3); $x++) {
            $randomCode .= $numbers{mt_rand(0, $max)};
        }
        return $prefix . str_shuffle($randomCode);
    }
}

 ?>