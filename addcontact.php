
<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

    include "menu.php";

    $debug = false;

    $yourURL = $domain . $phpSelf;

    $email = "";
    $home = "";
    $cell = "";
    $name = "";
    $emergencynum = "";
    


    $emailERROR = false;
    $homeERROR = false;
    $cellERROR = false;
    $nameERROR = false;
    $emergencynumERROR = false;
    $datehigheredERROR = false;
    $payERROR = false;
    $errorMsg = array();
    $addressERROR = false;


    if (isset($_POST["btnSubmit"])) {

        if (!securityCheck(true)) {
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }

        
        $email = filter_var($_POST["txtemail"], FILTER_SANITIZE_EMAIL);
        $home = filter_var($_POST["txthome"], FILTER_SANITIZE_NUMBER_FLOAT);
        $cell = filter_var($_POST["txtcell"], FILTER_SANITIZE_NUMBER_FLOAT);
        $name = filter_var($_POST["txtname"], FILTER_SANITIZE_STRING);
        $emergencynum = filter_var($_POST["txtnumber"], FILTER_SANITIZE_NUMBER_FLOAT);
        
        $results2 = $thisDatabase->select("SELECT fldEmail FROM tblRegister where fldEmail like '%$email%'");
        $emailList = $results2[0]["fldEmail"];


        if ($email == "") {
            $errorMsg[] = "Please enter your email";
            $emailERROR = true;
        } elseif (!verifyEmail($email)) {
            $errorMsg[] = "Your email appears to be incorrect.";
            $emailERROR = true;
        } elseif ($email != $emailList){
            $errorMsg[] = "Your email appears to be incorrect.";
            $emailERROR = true;
        }
        
        if ($home == "") {
            $errorMsg[] = "Please enter your home number";
            $homeERROR = true;
        } elseif (!verifyNumeric($home)) {
            $errorMsg[] = "Your home number appears to be incorrect.";
            $homeERROR = true;
        }
        if ($cell == "") {
            $errorMsg[] = "Please enter your cell number";
            $cellERROR = true;
        } elseif (!verifyNumeric($cell)) {
            $errorMsg[] = "Your cell number appears to be incorrect.";
            $cellERROR = true;
        }
        if ($emergencynum == "") {
            $errorMsg[] = "Please enter your emergency number";
            $emergencynumERROR = true;
        } elseif (!verifyNumeric($cell)) {
            $errorMsg[] = "Your emergency number appears to be incorrect.";
            $emergencynumERROR = true;
        }
        if ($name == "") {
            $errorMsg[] = "Please enter your full name";
            $nameERROR = true;
        } elseif (!verifyAlphaNum($name)) {
            $errorMsg[] = "Your name appears to be incorrect.";
            $nameERROR = true;
        }

        

        if (!$errorMsg) {
        
            
            $primaryKey = "";
            $dataEntered = false;
            try {
                $thisDatabase->db->beginTransaction();
                $query = 'insert into tblContact SET pmkEmail = ?, fldHomePhone = ?, fldCellPhone = ?, fldEmergencyContactName = ?, fldEmergencyContactNumber = ?';
                $data = array($email,$home, $cell, $name, $emergencynum);

                $results = $thisDatabase->insert($query, $data);

                $primaryKey = $thisDatabase->lastInsert();


// all sql statements are done so lets commit to our changes
                $dataEntered = $thisDatabase->db->commit();
                $dataEntered = true;
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();

                $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
            }
// If the transaction was successful, give success message
            if ($dataEntered) {

//#################################################################
// create a key value for confirmation
//#################################################################
//
                //Put forms information into a variable to print on the screen
//

                $messageA = '<h2>Your new contact info was added!</h2>';
                $messageC .= "<p><b>Email:</b><i>   " . $email . "</i></p>";
                $messageC .= "<p><b>Home Phone:</b><i>   " . $home . "</i></p>";
                $messageC .= "<p><b>Cell Phone:</b><i>   " . $cell . "</i></p>";
                $messageC .= "<p><b>Emergency Contact:</b><i>   " . $name . "</i></p>";
                $messageC .= "<p><b>Emergency Number:</b><i>   " . $emergencynum . "</i></p>";
                

//##############################################################
//
                // email the form's information
//
            } //data entered  
        } // end form is valid
    } // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
    ?>
    <article id="main">
        <?php
//####################################
//
// SECTION 3a.
//
//
//
//
// If its the first time coming to the form or there are errors we are going
// to display the form.
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
            print $messageA . $messageC;
        } else {
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
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

                <legend>Update contact info for admin accounts</legend>
                <span class='required'></span>

                <fieldset class="intro">
                    <legend>Complete the information to update contact info for admin accounts.</legend>

                    <fieldset class="contact">
                        <legend>Contact Information</legend>					
                        <label for="txtemail" class="required">Admin email
                            <input type="text" id="txtemail" name="txtemail" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter your admin email" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                        <label for="txthome" class="required">Home Number
                            <input type="text" id="txthome" name="txthome" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter your house phone" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                        <label for="txtcell" class="required">Cell Number
                            <input type="text" id="txtcell" name="txtcell" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter your cell phone" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                        <label for="txtname" class="required">Emergency Contact Name
                            <input type="text" id="txtname" name="txtname" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter your Emergency Contact Name" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                        <label for="txtnumber" class="required">Emergency Contact Number
                            <input type="text" id="txtnumber" name="txtnumber" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter your Emergency Contact Number" 
                                   autofocus onfocus="this.select()" required>
                        </label>

                    </fieldset>	
                    
                    <fieldset class="buttons">
                        <legend></legend>
                        <input type="submit" id="btnSubmit" name="btnSubmit" value="Create" tabindex="900" class="button" >
                    </fieldset> <!-- ends buttons -->
                </fieldset> <!-- Ends Wrapper -->
            </form>
            <?php
        } // end body submit
        ?>
    </article>
    </body>
    <?php
} else {
    ?>
    <header>
        <h1>You need to log in first to see this page!</h1>
        <h1>please click the link below to log in.</h1>
        <h1><a href="login.php">Login!</a></h1>
    </header>
    <?php
}
include "footer.php";
?>