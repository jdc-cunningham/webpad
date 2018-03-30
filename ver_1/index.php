<?php

    // error checking
    ini_set('display_errors', 1);

    // require db connection
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'db-connect.php');

    // grab current link
    $cur_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $look_up = explode('webpad/', $cur_link)[1];

    $output_editable = true;

    // main functions
    function check_exist($pad_name) {

        global $dbh;

        $stmt = $dbh->prepare('SELECT * FROM entries WHERE pad_name=:pad_name');
        $stmt->bindParam(':pad_name', $pad_name, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $result = $stmt->fetchAll();
            $result_count = count($result);
            if ($result_count > 0) {
                foreach($result as $row) {
                    return true;
                }
            }
            else {
                return false;
            }
        }
    }

    function create_pad($pad_name) {

        if ($pad_name == 'css' || $pad_name == 'js' || $pad_name == 'php') {
            return 'Failed: ' . $pad_name . ' is a reserved directory';
        }

        global $dbh;

        $date_time = date('Y-m-d H:i A');

        // insert values
        $id = null;
        // $pad_name from parameter
        $entry =  $date_time . "\n\n" . 'Start writing...';// initiate with date stamp
        $last_modified = $date_time;

        $stmt = $dbh->prepare('INSERT INTO entries VALUES (:id, :pad_name, :entry, :last_modified)');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':pad_name', $pad_name, PDO::PARAM_STR);
        $stmt->bindParam(':entry', $entry, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified', $last_modified, PDO::PARAM_STR);
        if ($stmt->execute()) {
            // redirect to new pad
            header('refresh:3 url=/webpad/' . $pad_name);
            return 'Pad ' . $pad_name . ' created, redirecting...';
        }
        else {
            return false;
        }

    }

    function return_all_pads() {

        global $dbh, $output_editable;

        // set non-editable output for links
        $output_editable = false;

        // dumps all saved pad_name in alphabetical order
        $stmt = $dbh->prepare('SELECT pad_name FROM entries WHERE id > 0 ORDER BY pad_name ASC');
        if ($stmt->execute()) {
            $result = $stmt->fetchAll();
            $result_count = count($result);
            if ($result_count > 0) {
                $entry = '';
                foreach($result as $row) {
                    $entry .= '<a href="' . '/webpad/' . $row['pad_name'] . '">' . $row['pad_name'] . '</a>';
                }
            }
            else {
                $entry = 'No entries';
            }
        }
        return $entry;
    }

    // set title
    $title = 'Webpad';
    
    // check for commands eg. /delete/pad_name
    if (strpos($look_up, '/')) {
        // sub directory
        // check for methods eg. /save or /delete
        $query_parts = explode('/', $look_up);
        $command = $query_parts[0];
        $pad_name = $query_parts[1];
        $title .= ' - ' . $pad_name;

        if ($command == 'save') {
            // save preceding pad_name assuming doesn't exist
            if (strpos($pad_name, '/js/') || strpos($pad_name, '/css/') || strpos($pad_name, '/php/')) {
                echo 'this fired';
                exit;
            }
            $pad_exists = check_exist($pad_name);
            if ($pad_exists) {
                $entry = 'Failed to save: ' . $pad_name . ' entry exists';
            }
            else {
                // insert
                $save_pad = create_pad($pad_name);
                if (!$save_pad) {
                    $entry = 'Failed to save pad: ' . $pad_name;
                }
                else {
                    $entry = $save_pad;
                }
            }
        }
        else if ($command == 'delete') {
            // delete preceding pad_name assuming it exists
            $entry = 'Are you sure you want to delete ' . $pad_name . '? To delete use \really-delete\pad_name';
        }
        else if ($command == 'really-delete') {
            $stmt = $dbh->prepare('DELETE FROM entries WHERE pad_name=:pad_name');
            $stmt->bindParam(':pad_name', $pad_name, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $entry = 'Deleted ' . $pad_name;
            }
            else {
                $entry = 'Failed to delete ' . $pad_name;
            }
        }
    }
    else if ($look_up == 'view') {
        $entry = return_all_pads();
    }
    else if ($look_up == '') {
        $entry = 'Basic instructions:' . "\n\n";
        $entry .= 'Create: use /webpad/save/padname to create a new pad called padname' . "\n";
        $entry .= 'Read: use /webpad/view/padname to read a pad' . "\n";
        $entry .= "Update: if you're using a pad, your changes are saved automatically" . "\n";
        $entry .= 'Delete: use /webpad/delete/padname to delete a pad' . "\n";
        $entry .= 'The whole page is editable, simply click and start writing.' . "\n";
    }
    else {

        $pad_name = $look_up;
        $title .= ' - ' . $pad_name;

        // try to find in db
        $stmt = $dbh->prepare('SELECT entry, last_modified FROM entries WHERE pad_name=:pad_name');
        $stmt->bindParam(':pad_name', $pad_name, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $result = $stmt->fetchAll();
            $result_count = count($result);
            if ($result_count > 0) {
                foreach($result as $row) {
                    $entry = $row['entry'];
                }
            }
            else {
                // entry doesn't exist
                $entry = "entry doesn't exist";
            }
        }
    }

    if ($output_editable) {
        $pad_output = '<textarea id="editable-container" class="flex flt fdc">' .
            $entry .
        '</textarea>';
    }
    else {
        $pad_output = '<div id="editable-container" class="flex flt fdc">' .
            $entry .
        '</div>';
    }
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="/webpad/css/css-reset.css">
        <link rel="stylesheet" type="text/css" href="/webpad/css/flex-set.css">
        <link rel="stylesheet" type="text/css" href="/webpad/css/index.css">
    </head>
    <body>
        <div id="notifications" class="flex fcc"></div>
        <?php echo $pad_output; ?>
        <script src="/webpad/js/index.js"></script>
    </body>
</html>
