<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Assignment 10</title>
        <meta charset="utf-8">
        <meta name="author" content="Matthew Martin">
        <meta name="description" content="Assignment 10">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" href="style.css" type="text/css" media="screen">

        <?php
        $debug = false;

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// PATH SETUP
//
//  $domain = "https://www.uvm.edu" or http://www.uvm.edu;

        $domain = "http://";
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS']) {
                $domain = "https://";
            }
        }

        $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, "UTF-8");

        $domain .= $server;

        $phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

        $path_parts = pathinfo($phpSelf);

        if ($debug) {
            print "<p>Domain" . $domain;
            print "<p>php Self" . $phpSelf;
            print "<p>Path Parts<pre>";
            print_r($path_parts);
            print "</pre>";
        }

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// inlcude all libraries
//

        require_once('lib/security.php');

        if ($path_parts['filename'] == "newaccount" ||$path_parts['filename'] == "viewcontact" ||$path_parts['filename'] == "updatecontact" ||$path_parts['filename'] == "removecontact" ||$path_parts['filename'] == "addcontact" ||$path_parts['filename'] == "viewadmin" ||$path_parts['filename'] == "updateadmin" ||$path_parts['filename'] == "updateemployee" ||$path_parts['filename'] == "removeadmin" ||$path_parts['filename'] == "removeemployee" ||$path_parts['filename'] == "addemployee" || $path_parts['filename'] == "confirmation" || $path_parts['filename'] == "login" || $path_parts['filename'] == "approve" ) {
            include "lib/validation-functions.php";
            include "lib/mail-message.php";
        }
     
        require_once('../bin/myDatabase.php');
        
        $dbUserName = get_current_user() . '_writer';
        $whichPass = "w"; //flag for which one to use.
        $dbName = strtoupper(get_current_user()) . '_assignment10';
        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
        
        ?>	
      
    </head>
    <!-- ################ body section ######################### -->

    <?php
    print '<body id="' . $path_parts['filename'] . '">';
    ?>