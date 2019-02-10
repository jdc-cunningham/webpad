<?php

    // error checking
    ini_set('display_errors', 1);

    // require db connection
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'db-connect.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // get data
        $pad_name = $_POST['pad_name'];
        $entry = $_POST['entry'];
        
        $entry = str_replace('|amp|', '&', $entry);

        // return array
        $status = [];

        // update
        $stmt = $dbh->prepare('UPDATE entries SET entry=:entry WHERE pad_name=:pad_name');
        $stmt->bindParam(':entry', $entry, PDO::PARAM_STR);
        $stmt->bindParam(':pad_name', $pad_name, PDO::PARAM_STR);
        if ($stmt->execute()) {
            echo 'ok';
        }
        else {
            echo 'fail';
        }

    }
