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
    private $_realm = "";
    private $_trunkstore_account_id = "";

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
            CURLOPT_URL => $this->_settings->api_url . "api_auth",
            CURLOPT_POSTFIELDS => json_encode($data)
        ));

        $response = json_decode(curl_exec($this->_curl));
        $this->_auth_token = $response->auth_token;
    }

    private function _get_realm_random() {
        $random = Utils::get_random("", 8);
        $this->_realm = $random . ".sip.2600hz.com";
    }

    function __construct() {
        // Loading settings
        $objSettings = new Settings;
        $this->_settings = $objSettings->get_settings();

        $this->_init_curl();
        $this->_get_auth_token();
    }

    // Create main account
    private function _create_account($request_data) {
        $this->_get_realm_random();

        $account_data = array(
            "data" => array(
                "name" => "Test account",
                "realm" => $this->_realm,
                "timezone" => "America/Los_Angeles"
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_settings->master_account_id . "/",
            CURLOPT_POSTFIELDS => json_encode($account_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json', 
                'X-Auth-Token: ' . $this->_auth_token, 
                'Accept: application/json'
            )
        ));

        $response = json_decode(curl_exec($this->_curl));
        return $response->data->id;
    }

    // Create trunkstore account
    private function _create_trunkstore_account($request_data) {
        $trunkstore_account_data = array(
            "data" => array(
                "name" => $request_data['data']['account']['name'],
                "available_apps" => $request_data['data']['account']['available_apps'],
                "account" => array(
                    "credits" => array(
                        "prepay" => '0.00'
                    ),
                    "trunks" => '0',
                    "inbound_trunks" => '0',
                    "auth_realm" => $this->_realm
                ),
                "billing_account_id" => $this->_account_id,
                "DIDs_Unassigned" => array(),
                "servers" => array(
                    array(
                        "DIDs" => $request_data['data']['pbx']['list_dids'],
                        "options" => array(
                            "enabled" => true,
                            "inbound_format" => "e.164",
                            "international" => false,
                            "caller_id" => array(),
                            "e911_info" => array(),
                            "failover" => array(),
                            "media_handling" => "bypass"
                        ),
                        "monitor" => array(
                            "monitor_enabled" => false
                        ),
                        "auth" => array(
                            "auth_user" => $request_data['data']['pbx']['auth']['auth_user'],
                            "auth_password" => $request_data['data']['pbx']['auth']['auth_password'],
                            "auth_method" => "Password"
                        ),
                        "server_name" => "My PBX",
                        "server_type" => "other"
                    )
                )
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_account_id . "/connectivity",
            CURLOPT_POSTFIELDS => json_encode($trunkstore_account_data)
        ));

        $response = json_decode(curl_exec($this->_curl));
        return $response->data->id;
    }

    private function _create_user($request_data) {
        $user_data = array(
            "data" => array(
                "username" => $request_data['data']['user']['username'],
                "password" => $request_data['data']['user']['password'],
                "email" => $request_data['data']['user']['email'],
                "first_name" => $request_data['data']['user']['first_name'],
                "last_name" => $request_data['data']['user']['last_name'],
                "timezone" => "America/Los_Angeles",
                "priv_level" => $request_data['data']['user']['priv_level'],
                "verified" => false,
                "record_call" => false,
                "apps" => $request_data['data']['user']['apps']
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_account_id . "/users",
            CURLOPT_POSTFIELDS => json_encode($user_data)
        ));

        $response = json_decode(curl_exec($this->_curl));
        if ($response->status == "success")
            return true;
        else
            return false;
    }

    private function _set_credits($request_data) {
        $credits_data = array(
            "data" => array(
                "amount" => $request_data['data']['trunks']['money']
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_account_id . "/braintree/credits",
            CURLOPT_POSTFIELDS => json_encode($credits_data)
        ));

        $response = json_decode(curl_exec($this->_curl));
        if ($response->status == "success")
            return true;
        else
            return false;
    }

    private function _set_limits($request_data) {
        $limits_data = array(
            "data" => array(
                "twoway_trunks" => 1,
                "inbound_trunks" => 1
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_account_id . "/limits",
            CURLOPT_POSTFIELDS => json_encode($limits_data)
        ));

        $response = json_decode(curl_exec($this->_curl));

        print_r($response);

        if ($response->status == "success")
            return true;
        else
            return false;
    }

    // Adding a credit card
    private function _add_credit_card($request_data) {
        $credit_card_data = array(
            "data" => array(
                "credit_card" => array(
                    "number" => $request_data['data']['braintree']['credit_card']['number'],
                    "expiration_date" => $request_data['data']['braintree']['credit_card']['expiration_date'],
                    "cvv" => $request_data['data']['braintree']['credit_card']['cvv'],
                    "billing_address" => array(
                        "postal_code" => $request_data['data']['braintree']['credit_card']['billing_address']['postal_code'],
                        "first_name" => $request_data['data']['braintree']['credit_card']['billing_address']['first_name'],
                        "last_name" => $request_data['data']['braintree']['credit_card']['billing_address']['last_name'],
                        "country" => $request_data['data']['braintree']['credit_card']['billing_address']['country'],
                        "locality" => $request_data['data']['braintree']['credit_card']['billing_address']['locality'],
                        "region" => $request_data['data']['braintree']['credit_card']['billing_address']['region'],
                        "street_address" => $request_data['data']['braintree']['credit_card']['billing_address']['street_address'],
                    ),
                    "first_name" => $request_data['data']['braintree']['first_name'],
                    "last_name" => $request_data['data']['braintree']['last_name'],
                    "company" => $request_data['data']['braintree']['company']
                )
            )
        );

        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_account_id . "/braintree/customer",
            CURLOPT_POSTFIELDS => json_encode($credit_card_data)
        ));

        $response = json_decode(curl_exec($this->_curl));
        if ($response->status == "success")
            return true;
        else 
            return false;
    }

    // We might need to delete the account if there is a fail somewhere
    private function _delete_account() {
        curl_setopt_array($this->_curl, array(
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_URL => $this->_settings->api_url . "accounts/" . $this->_account_id,
            CURLOPT_POSTFIELDS => null        ));

        $response = json_decode(curl_exec($this->_curl));
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

        $trunkstore_account_id = $this->_create_trunkstore_account($request_data);
        if ($trunkstore_account_id)
            $this->_trunkstore_account_id = $trunkstore_account_id;
        else {
            $this->_delete_account();
            throw new RestException(500, "Could not create the trunkstore account");
        }

        if (!$this->_create_user($request_data)) {
                $this->_delete_account();
                throw new RestException(500, "Could not save the credits");
            }

        if (isset($request_data['trunks']['money'])) {
            if (!$this->_set_credits($request_data)) {
                $this->_delete_account();
                throw new RestException(500, "Could not save the credits");
            }
        } else {
            if (!$this->_set_limits($request_data)) {
                $this->_delete_account();
                throw new RestException(500, "Could not save the limits");
            }
        }

        if (!$this->_add_credit_card($request_data)) {
            $this->_delete_account();
            throw new RestException(500, "Error while saving the credit card");
            
        }

        return array(
            "data" => array(
                "status" => "success"
            )
        );
    }
}

 ?>