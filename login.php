<?php
include "top.php";

$debug = false;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";


$yourURL = $domain . $phpSelf;


$email = "youremail@uvm.edu";
$userpass = "Your Password";

$emailERROR = false;
$userpassERROR = false;
$approvListERROR = false;
$confirmListERROR = false;
$loggedin = false;

$errorMsg = array();

$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";

if (isset($_POST["btnSubmit"])) {

    if (!securityCheck(true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }

    $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
    $userpass = filter_var($_POST["txtUserPass"], FILTER_SANITIZE_STRING);

    $results1 = $thisDatabase->select("SELECT distinct(fldEmail) FROM tblRegister where fldEmail like '%$email%'");
    $emailList = $results1[0]["fldEmail"];

    $results4 = $thisDatabase->select("SELECT fldConfirmed FROM tblRegister where fldEmail like '%$email%'");
    $confirmList = $results4[0]["fldConfirmed"];
    
    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    } elseif ($email != $emailList) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    } elseif ($confirmList != "1") {
        $errorMsg[] = "Your account has not been confirmed by you yet, check your email.";
        $emailERROR = true;
    }
    $results2 = $thisDatabase->select("SELECT fldUserPass FROM tblRegister where fldEmail like '%$email%'");
    $PassList = $results2[0]["fldUserPass"];

    if ($userpass == "") {
        $errorMsg[] = "Please enter your password";
        $userpassERROR = true;
    } elseif (!verifyAlphaNum($userpass)) {
        $errorMsg[] = "Your password appears to be incorrect.";
        $userpassERROR = true;
    } elseif ($userpass != $PassList) {
        $errorMsg[] = "Your password appears to be incorrect.";
        $userpassERROR = true;
    }

    

    
    
    
} // ends if form was submitted.

?>
<article id="main">
<?php

if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    session_start();
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $email;
    header("Location: https://mdmartin.w3.uvm.edu/cs148/assignment10/home.php");
} else {

    if ($errorMsg) {
        print '<div id="errors">';
        print "<ol>\n";
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        print "</ol>\n";
        print '</div>';
    }

    ?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">
            <fieldset class="wrapper">
                <legend>Login to your account</legend>
                <p>
                    Employee Management
                </p>
                <fieldset class="wrapperTwo">
                    <legend>Please complete the following form</legend>
                    <fieldset class="contact">
                        <legend>Required Information</legend>

                        <label for="txtEmail" class="required">Enter your Email
                            <input type="text" id="txtEmail" name="txtEmail" style="width: 15em"
                                   value=""
                                   tabindex="120" maxlength="45" placeholder="Enter a valid email address"
    <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtUserPass" class="required">Enter your password
                            <input type="text" id="txtUserPass" name="txtUserPass" style="width: 15em"
                                   value=""
                                   tabindex="120" maxlength="45" placeholder="Enter your password"
    <?php if ($userpassERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                    </fieldset> <!-- ends contact -->
                </fieldset> <!-- ends wrapper Two -->
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Login" tabindex="900" class="button" >
                </fieldset> <!-- ends buttons -->
            </fieldset> <!-- Ends Wrapper -->
        </form>
    <?php
} // end body submit
?>
</article>



<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</body>
</html>