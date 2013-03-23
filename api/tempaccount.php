<?php 

/**
 * Api class that will generate a random username and a random password
 *
 * @author Francis Genet
 * @package Trunking.io
 * @version 1.0
 */

class Tempaccount {
    private $_db = null;
    private $_settings = null;

    function __construct() {
        // Loading settings
        $objSettings = new Settings;
        $this->_settings = $objSettings->get_settings();

        // Set the DSN (the string that determines what driver to user and how)
        $dsn = "mysql:host=" . $this->_settings->database->host . ";dbname=" . $this->_settings->database->dbname . ";charset=" . $this->_settings->database->charset;
        // Set the driver parameters
        $drvr_params = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        // Creating a connexion
        $this->_db = new PDO($dsn, $this->_settings->database->username, $this->_settings->database->password, $drvr_params);
    }

    /**
     * will return an object with the generated information 
     *
     * @url OPTIONS /credentials/
     */
    function options_credential() {
        return;
    }

    /**
     * will return an object with the generated information 
     *
     * @url OPTIONS /registered/
     */
    function options_registered() {
        return;
    }

    /**
     * will return an object with the generated information 
     *
     * @url GET /credentials/
     */
    function credentials() {
        // First, let's try to get a match for the current client IP
        try {
            $stmt = $this->_db->query("SELECT * FROM clients WHERE ip = ?");
            $stmt->execute(array($_SERVER['REMOTE_ADDR']));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
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
            } catch (PDOException $e) {
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

    /**
     * will return an object with the info on the phone registration
     *
     * @url GET /registered/
     */
    function get_registration() {
        try {
            $stmt = $this->_db->query("SELECT * FROM clients WHERE ip = ?");
            $stmt->execute(array($_SERVER['REMOTE_ADDR']));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RestException(500, $e->getMessage());
        }

        if (count($rows) > 0) {
            $return_value = array(
                "data" => array(
                    "registered" => $rows[0]['registered']
                )
            );
            
            return $return_value;
        } else 
            throw new RestException(404, "No user corresponding to you");
    }

    /**
     * will return an object with the remainig seconds for the call
     *
     * @url GET /remaining/
     */
    function get_remaining() {
        try {
            $stmt = $this->_db->query("SELECT * FROM clients WHERE ip = ?");
            $stmt->execute(array($_SERVER['REMOTE_ADDR']));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RestException(500, $e->getMessage());
        }

        if (count($rows) > 0) {
            $return_value = array(
                "data" => array(
                    "remaining_seconds" => $rows[0]['remaining']
                )
            );

            return $return_value;
        } else 
            throw new RestException(404, "No user corresponding to you");
    }
}

 ?>