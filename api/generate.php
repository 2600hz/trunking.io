<?php 

/**
 * Api class that will generate a random username and a random password
 *
 * @author Francis Genet
 * @package Trunking.io
 * @version 1.0
 */

class Generate {
    private $_db = null;

    function options() {
        return;
    }

    /**
     * will return an object with the generated information 
     *
     * @url GET /
     */
    function get() {
        // Loading settings
        $objSettings = new Settings;
        $settings = $objSettings->get_settings();

        // Set the DSN (the string that determines what driver to user and how)
        $dsn = "mysql:host=" . $settings->database->host . ";dbname=" . $settings->database->dbname . ";charset=" . $settings->database->charset;
        // Set the driver parameters
        $drvr_params = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        // Creating a connexion
        $this->_db = new PDO($dsn, $settings->database->username, $settings->database->password, $drvr_params);

        // First, let's try to get a match for the current client IP
        try {
            $stmt = $this->_db->query("SELECT * FROM clients WHERE ip = ?");
            $stmt->execute(array($_SERVER['REMOTE_ADDR']));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }

        // If there is a result then it means that the user is returning
        if (count($rows) > 0) {
            $username = $rows[0]['username'];
            $password = $rows[0]['password'];
        } else {
            $username = Utils::get_random('user_', 8);
            $password = Utils::get_random('', 10);
            $client_ip = $_SERVER['REMOTE_ADDR'];
            try {
                $stmt = $this->_db->prepare("INSERT INTO clients(ip, username, password) VALUES(?, ?, ?)");
                $stmt->execute(array($client_ip, $username, $password));
            } catch (Exception $e) {
                throw new RestException(500, $e->getMessage());
            }
        }

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