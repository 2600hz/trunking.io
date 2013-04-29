<?php

$auth_ip = array('74.82.47.147');

if (!in_array($_SERVER['REMOTE_ADDR'], $auth_ip))
    die();

if ($_REQUEST['username']) {
    // Set the DSN (the string that determines what driver to user and how)
    $dsn = "mysql:host=localhost;dbname=trunkingdotio;charset=utf8";
    // Set the driver parameters
    $drvr_params = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    // Creating a connexion
    $db = new PDO($dsn, "root", "root", $drvr_params);

    try {
        // Do SQL or file query here for the username to lookup the username and password
        $stmt = $db->prepare("SELECT * FROM `clients` WHERE `username` = ?");
        $stmt->execute(array($_REQUEST['username']));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        print_r($result);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
    
    $username = $result[0]['username'];
    $password = $result[0]['password'];
}

echo <<<XML
<include>
  <user id="$username">
    <params>
      <param name="password" value="$password"/>
    </params>
  </user>
</include>

XML;

?>