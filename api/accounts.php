<?php 

/**
 * Api class that will generate a random username and a random password
 *
 * @author Francis Genet
 * @package Trunking.io
 * @version 1.0
 */

class Accounts {
    private $_curl = null;
    private $_settings = null;
    private $_auth_token = "";
    private $_account_id = "";

    function options() {
        return;
    }

    private function _init_curl() {
        $this->_curl = curl_init();

        curl_setopt_array($this->_curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));
    }

    private function _get_auth_token() {
        $data = array(
            "data" => array(
                "api_key" => $this->_settings->api_key
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => "http://apps001-qa-fmt.2600hz.com:8000/v1/api_auth",
            CURLOPT_POSTFIELDS => json_encode($data)
        ));

        $response = json_decode(curl_exec($this->_curl));
        $this->_auth_token = $response->auth_token;
    }

    private function _get_realm_random() {
        $random = Utils::get_random("", 8);

        return $random . ".sip.2600hz.com";
    }

    function __construct() {
        // Loading settings
        $objSettings = new Settings;
        $this->_settings = $objSettings->get_settings();

        $this->_init_curl();
        $this->_get_auth_token();
    }

    private function _create_account($request_data) {
        $account_data = array(
            "data" => array(
                "name" => "Test account",
                "realm" => $this->_get_realm_random(),
                "timezone" => "America/Los_Angeles"
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => "http://apps001-qa-fmt.2600hz.com:8000/v1/accounts/" . $this->_settings->master_account_id . "/",
            CURLOPT_POSTFIELDS => json_encode($account_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json', 
                'X-Auth-Token: ' . $this->_auth_token, 
                'Accept: application/json'
            )
        ));

        $response = json_decode(curl_exec($this->_curl));
        return $response['data']['id'];
    }

    /**
     * will create an account and everything related to it 
     *
     * @url POST /
     */
    function create($request_data) {
        $account_id = $this->_create_account($request_data);
        if ($account_id)
            $this->_account_id = $account_id;
        else
            throw new RestException(500, "Could not create the account");
            
    }
}

 ?>