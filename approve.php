<?php
include "top.php";

print '<article id="main">';

print '<h1>Registration Approval</h1>';

$debug = false;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%

$adminEmail = "mdmartin@uvm.edu";
$message = "<p>I am sorry but an error occured, try again later.</p>";


if (isset($_GET["q"])) {
    $key1 = htmlentities($_GET["q"], ENT_QUOTES, "UTF-8");
    

    $data = array($key1);
    //##############################################################
    // get the membership record 

    $query = "SELECT fldDateJoined, fldEmail FROM tblRegister WHERE pmkRegisterId = ? ";
    $results = $thisDatabase->select($query, $data);

    $dateSubmitted = $results[0]["fldDateJoined"];
    $email = $results[0]["fldEmail"];

    $k1 = sha1($dateSubmitted);

    if ($debug) {
        print "<p>Date: " . $dateSubmitted;
        print "<p>email: " . $email;
        print "<p><pre>";
        print_r($results);
        print "</pre></p>";
        print "<p>k1: " . $k1;
        print "<p>q : " . $key1;
    }
    //##############################################################
    // update confirmed
    
        if ($debug)
            print "<h1>Confirmed</h1>";

        $query = "UPDATE tblRegister set fldApproved = 1 WHERE pmkRegisterId = ? ";
        $results = $thisDatabase->update($query, $data);

        if ($debug) {
            print "<p>Query: " . $query;
            print "<p><pre>";
            print_r($results);
            print_r($data);
            print "</pre></p>";
        }
        // notify admin
        $message = '<h2>The following Registration has been approved:</h2>';
        $message .= 'email:';
        $message .= $email;
        


        if ($debug)
            print "<p>" . $message;


        if ($debug) {
            print "<p>";
            if (!$mailed) {
                print "NOT ";
            }
            print "mailed to admin ". $to . ".</p>";
        }

        

        //print $message;
        if ($debug) {
            print "<p>";
            if (!$mailed) {
                print "NOT ";
            }
            print "mailed to member: " . $to . ".</p>";
        }
    
        print $message;
    
} // ends isset get q
?>



<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</article>
</body>
</html>