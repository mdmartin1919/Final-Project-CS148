<?php
include "top.php";
print '<article id="main">';
print '<h1>Registration Confirmation</h1>';

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.
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
    $key2 = htmlentities($_GET["w"], ENT_QUOTES, "UTF-8");

    $data = array($key2);
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

    if ($key1 == $k1) {
        if ($debug)
            print "<h1>Confirmed</h1>";

        $query = "UPDATE tblRegister set fldConfirmed = 1 WHERE pmkRegisterId = ? ";
        $results = $thisDatabase->update($query, $data);

        if ($debug) {
            print "<p>Query: " . $query;
            print "<p><pre>";
            print_r($results);
            print_r($data);
            print "</pre></p>";
        }
        // notify admin
        $message = '<h2>The following Registration has been confirmed:</h2>';


        $message = '<a href="' . $domain . $path_parts["dirname"] . '/approve.php?q=' . $key2 . '">CLICK THIS</a>';
        $message .= "<p> to approve this registration for: ";
        $message .= $email;
        $message .= "<p>or copy and paste this url into a web browser: ";
        $message .= $path_parts["dirname"] . '/approve.php?q=' . $key2 . "</p>";

        if ($debug)
            print "<p>" . $message;

        $to = $adminEmail;
        $cc = "";
        $bcc = "";
        $from = "Back Cove Holdings <noreply@BackCove.com>";
        $subject = "New Admin Confirmed: Approve?";

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);

        if ($debug) {
            print "<p>";
            if (!$mailed) {
                print "NOT ";
            }
            print "mailed to admin " . $to . ".</p>";
        }

        // notify user
        $to = $email;
        $cc = "";
        $bcc = "";
        $from = "Back Cove Holdings <noreply@BackCove.com>";
        $subject = "Back Cove Holdings Acount Confirmation";
        $message = "<p>Thank you for taking the time to confirm your account. Once your account has been approved you will be able to get full admin function of the site.</p>";

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);

        print $message;
        if ($debug) {
            print "<p>";
            if (!$mailed) {
                print "NOT ";
            }
            print "mailed to member: " . $to . ".</p>";
        }
    } else {
        print $message;
    }
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