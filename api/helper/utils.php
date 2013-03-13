<?php 

class Utils {
    public static function get_random($prefix, $length) {
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