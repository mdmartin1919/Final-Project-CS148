<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
    include "menu.php";
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
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
    $yourURL = $domain . $phpSelf;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
    $email = "youremail@uvm.edu";
    $userpass = "Your Password";
    $username = "Your Full Name";
    $gendermale = "";
    $genderfemale = "";
    $gendernone = "";
    $gender = "";
    
    $employee = "";
    $store = "";
    $status = "";
    $pay = "";
    $day = "";
    $month = "";
    $year = "";
    $address = "";
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
    $emailERROR = false;
    $usernameERROR = false;
    $userpassERROR = false;
    $genderERROR = false;
    $employeeERROR = false;
    $storeERROR = false;
    $statusERROR = false;
    $datehigheredERROR = false;
    $payERROR = false;
    $addressERROR = false;
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
    $errorMsg = array();

// used for building email message to be sent and displayed
    $mailed = false;
    $messageA = "";
    $messageB = "";
    $messageC = "";

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
    if (isset($_POST["btnSubmit"])) {
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
        if (!securityCheck(true)) {
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.

        $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
        $userpass = filter_var($_POST["txtUserPass"], FILTER_SANITIZE_STRING);
        $username = filter_var($_POST["txtusername"], FILTER_SANITIZE_STRING);
        $gendermale = filter_var($_POST["chkgendermale"], FILTER_SANITIZE_STRING);
        $genderfemale = filter_var($_POST["chkgenderfemale"], FILTER_SANITIZE_STRING);
        $gendernone = filter_var($_POST["chkgendernone"], FILTER_SANITIZE_STRING);
        $employee = htmlentities($_POST["txtemployee"], ENT_QUOTES, "UTF-8");
        $address = filter_var($_POST["txtaddress"], FILTER_SANITIZE_STRING);
        $day = htmlentities($_POST["lstday"], ENT_QUOTES, "UTF-8");
        $month = htmlentities($_POST["lstmonth"], ENT_QUOTES, "UTF-8");
        $year = htmlentities($_POST["lstyear"], ENT_QUOTES, "UTF-8");
        $store = htmlentities($_POST["lststore"], ENT_QUOTES, "UTF-8");
        $status = htmlentities($_POST["lststatus"], ENT_QUOTES, "UTF-8");
        $pay = htmlentities($_POST["lstpay"], ENT_QUOTES, "UTF-8");

        $results1 = $thisDatabase->select("SELECT distinct(fldEmail) FROM tblRegister where fldEmail like '%$email%'");
        $emailList = $results1[0]["fldEmail"];
        if ($email == "") {
            $errorMsg[] = "Please enter your email address";
            $emailERROR = true;
        } elseif (!verifyEmail($email)) {
            $errorMsg[] = "Your email address appears to be incorrect.";
            $emailERROR = true;
        } elseif ($email == $emailList) {
            $errorMsg[] = "Your email address already recieves emails from us.";
            $emailERROR = true;
        }

        if ($address == "") {
            $errorMsg[] = "Please enter your address";
            $addressERROR = true;
        } elseif (!verifyAlphaNum($address)) {
            $errorMsg[] = "Your address appears to be incorrect.";
            $addressERROR = true;
        }
        
        if ($userpass == "") {
            $errorMsg[] = "Please enter your password";
            $userpassERROR = true;
        } elseif (!verifyAlphaNum($userpass)) {
            $errorMsg[] = "Your password appears to be incorrect.";
            $userpassERROR = true;
        }

        if ($username == "") {
            $errorMsg[] = "Please enter your first name";
            $usernameERROR = true;
        } elseif (!verifyAlphaNum($username)) {
            $errorMsg[] = "Your first name appears to be incorrect.";
            $usernameERROR = true;
        }
        
        if ($datehighered = "") {
            $errorMsg[] = "You cant leave the hire date blank.";
            $datehigheredERROR = true;
        }

        if ($gendermale == "" & $genderfemale == "" & $gendernone == "") {
            $errorMsg[] = "Please pick a gender";
            $genderERROR = true;
        } elseif ($gendermale == "male" & $genderfemale == "female" & $gendernone == "not given") {
            $errorMsg[] = "Please select just one gender";
            $genderERROR = true;
        } elseif ($gendermale == "male" & $genderfemale == "female" & $gendernone == "") {
            $errorMsg[] = "Please select just one gender";
            $genderERROR = true;
        } elseif ($gendermale == "" & $genderfemale == "female" & $gendernone == "not given") {
            $errorMsg[] = "Please select just one gender";
            $genderERROR = true;
        } elseif ($gendermale == "male" & $genderfemale == "" & $gendernone == "not given") {
            $errorMsg[] = "Please select just one gender";
            $genderERROR = true;
        }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2d Process Form - Passed Validation
//
// Process for when the form passes validation (the errorMsg array is empty)
//
        if (!$errorMsg) {
            $datehighered .= $year;
            $datehighered .= "-";
            $datehighered .= $month;
            $datehighered .= "-";
            $datehighered .= $day;
            $gender = $gendermale . $genderfemale . $gendernone;
            if ($debug)
                print "<p>Form is valid</p>";

            //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
            //
        // SECTION: 2e Save Data
            //

        $primaryKey = "";
            $dataEntered = false;
            try {
                $thisDatabase->db->beginTransaction();
                $query = 'INSERT INTO tblRegister SET fldEmail = ?, fldUserName = ?, fldUserPass = ?';
                $data = array($email, $username, $userpass);
                $results = $thisDatabase->insert($query, $data);
                $primaryKey = $thisDatabase->lastInsert();

                $query1 = "INSERT INTO tblEmployee SET fldName = ?,fldHomeAddress = ?, fldStore = 'Corporate', fldStatus = 'Admin', fldSex = ?, fldDateHigher = ?, fldPay = 40";
                $data1 = array($username,$address, $gender, $datehighered);
                $results8 = $thisDatabase->insert($query1, $data1);

// all sql statements are done so lets commit to our changes
                $dataEntered = $thisDatabase->db->commit();
                $dataEntered = true;
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                if ($debug)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
            }
            // If the transaction was successful, give success message
            if ($dataEntered) {
                if ($debug)
                    print "<p>data entered now prepare keys ";
                //#################################################################
                // create a key value for confirmation

                $query = "SELECT fldDateJoined FROM tblRegister WHERE pmkRegisterId=" . $primaryKey;
                $results = $thisDatabase->select($query);

                $dateSubmitted = $results[0]["fldDateJoined"];

                $key1 = sha1($dateSubmitted);
                $key2 = $primaryKey;

                if ($debug)
                    print "<p>key 1: " . $key1;
                if ($debug)
                    print "<p>key 2: " . $key2;


                //#################################################################
                //
            //Put forms information into a variable to print on the screen
                //

            $messageA = '<h2>Thank you for registering.</h2>';

                $messageB = "<p>Click this link to confirm your registration: ";
                $messageB .= '<a href="' . $domain . $path_parts["dirname"] . '/confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . '">Confirm Registration</a></p>';
                $messageB .= "<p>or copy and paste this url into a web browser: ";
                $messageB .= $domain . $path_parts["dirname"] . '/confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . "</p>";

                $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";
                $messageC .= "<p><b>Name:</b><i>   " . $username . "</i></p>";
                $messageC .= "<p><b>Gender:</b><i>   " . $gender . "</i></p>";
                $messageC .= "<p><b>Address:</b><i>   " . $address . "</i></p>";
                $messageC .= "<p><b>Date Highered:</b><i>   " . $datehighered . "</i></p>";
                $messageC .= "<p><a href='newaccount.php'>Add Another Admin?</a></p>";

                //##############################################################
                //
            // email the form's information
                //
            $to = $email; // the person who filled out the form
                $cc = "";
                $bcc = "";
                $from = "Back Cove <noreply@BackCove.com>";
                $subject = "Back Cove Holding Group Registration";

                $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
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
            print "<h1>Your Request has ";
            if (!$mailed) {
                print "not ";
            }
            print "been processed</h1>";
            print "<p>A copy of this message has ";
            if (!$mailed) {
                print "not ";
            }
            print "been sent</p>";
            print "<p>To: " . $email . "</p>";
            print "<p>Message Contents:</p>";
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
//####################################
//
// SECTION 3c html Form
//
            /* Display the HTML form. note that the action is to this same page. $phpSelf
              is defined in top.php
              NOTE the line:
              value="<?php print $email; ?>
              this makes the form sticky by displaying either the initial default value (line 35)
              or the value they typed in (line 84)
              NOTE this line:
              <?php if($emailERROR) print 'class="mistake"'; ?>
              this prints out a css class so that we can highlight the background etc. to
              make it stand out that a mistake happened here.
             */
            ?>
            <form action="<?php print $phpSelf; ?>"
                  method="post"
                  id="frmRegister">
                <fieldset class="wrapper">
                    <legend>Make a New Admin Account</legend>
                    <p>
                        Please fill out all the fields and confirm with your email to gain access to the employee records.  You wont be able to change records till the administrator approves you.
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
                            <label for="txtusername" class="required">Enter your Full Name
                                <input type="text" id="txtusername" name="txtusername" style="width: 15em"
                                       value=""
                                       tabindex="120" maxlength="45" placeholder="Enter your full name"
                                       <?php if ($usernameERROR) print 'class="mistake"'; ?>
                                       onfocus="this.select()"
                                       >
                            </label>
                            <label for="txtaddress" class="required">Address
                            <input type="text" id="txtaddress" name="txtaddress" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter your address" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                        </fieldset>
                        <fieldset class="lists">	
                        <legend>Please pick the day/month/year of hire</legend>
                        <p>DAY:</p>
                        <select id="lstday" name="lstday" tabindex="281" size="1">
                            <option value="1"  selected="selected"
                            <?php
                            if ($day == "1") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >1</option>
                            <option value="2" 
                            <?php
                            if ($day == "2") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2</option>
                            <option value="3"
                            <?php
                            if ($day == "3") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >3</option>
                            <option value="4"
                            <?php
                            if ($day == "4") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >4</option>
                            <option value="5"
                            <?php
                            if ($day == "5") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >5</option>
                            <option value="6"
                            <?php
                            if ($day == "6") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >6</option>
                            <option value="7"
                            <?php
                            if ($day == "7") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >7</option>
                            <option value="8"
                            <?php
                            if ($day == "8") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >8</option>
                            <option value="9"
                            <?php
                            if ($day == "9") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >9</option>
                            <option value="10"
                            <?php
                            if ($day == "10") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >10</option>
                            <option value="11"
                            <?php
                            if ($day == "11") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >11</option>
                            <option value="12"
                            <?php
                            if ($day == "12") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >12</option>
                            <option value="13"
                            <?php
                            if ($day == "13") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >13</option>
                            <option value="14"
                            <?php
                            if ($day == "14") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >14</option>
                            <option value="15"
                            <?php
                            if ($day == "15") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >15</option>
                            <option value="16"
                            <?php
                            if ($day == "16") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >16</option>
                            <option value="17"
                            <?php
                            if ($day == "17") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >17</option>
                            <option value="18"
                            <?php
                            if ($day == "18") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >18</option>
                            <option value="19"
                            <?php
                            if ($day == "19") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >19</option>
                            <option value="20"
                            <?php
                            if ($day == "20") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >20</option>
                            <option value="21"
                            <?php
                            if ($day == "21") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >21</option>
                            <option value="22"
                            <?php
                            if ($day == "23") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >23</option>
                            <option value="24"
                            <?php
                            if ($day == "24") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >24</option>
                            <option value="25"
                            <?php
                            if ($day == "25") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >25</option>
                            <option value="26"
                            <?php
                            if ($day == "26") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >26</option>
                            <option value="27"
                            <?php
                            if ($day == "27") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >27</option>
                            <option value="28"
                            <?php
                            if ($day == "28") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >28</option>
                            <option value="29"
                            <?php
                            if ($day == "29") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >29</option>
                            <option value="30"
                            <?php
                            if ($day == "30") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >30</option>
                            <option value="31"
                            <?php
                            if ($day == "31") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >31</option>            
                            
                        </select>
                        <p>MONTH:</p>
                        <select id="lstmonth" name="lstmonth" tabindex="281" size="1">
                            <option value="1"  selected="selected"
                            <?php
                            if ($month == "1") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >1</option>
                            <option value="2" 
                            <?php
                            if ($month == "2") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2</option>
                            <option value="3"
                            <?php
                            if ($month == "3") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >3</option>
                            <option value="4"
                            <?php
                            if ($month == "4") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >4</option>
                            <option value="5"
                            <?php
                            if ($month == "5") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >5</option>
                            <option value="6"
                            <?php
                            if ($month == "6") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >6</option>
                            <option value="7"
                            <?php
                            if ($month == "7") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >7</option>
                            <option value="8"
                            <?php
                            if ($month == "8") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >8</option>
                            <option value="9"
                            <?php
                            if ($month == "9") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >9</option>
                            <option value="10"
                            <?php
                            if ($month == "10") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >10</option>
                            <option value="11"
                            <?php
                            if ($month == "11") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >11</option>
                            <option value="12"
                            <?php
                            if ($month == "12") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >12</option>
                        </select>
                        <p>YEAR:</p>
                        <select id="lstyear" name="lstyear" tabindex="281" size="1">
                            <option value="2010"  selected="selected"
                            <?php
                            if ($year == "2010") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2010</option>
                            <option value="2011" 
                            <?php
                            if ($year == "2011") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2011</option>
                            <option value="2012"
                            <?php
                            if ($year == "2012") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2012</option>
                            <option value="2013"
                            <?php
                            if ($year == "2013") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2013</option>
                            <option value="2014"
                            <?php
                            if ($year == "2014") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2014</option>
                            <option value="2015"
                            <?php
                            if ($year == "2015") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2015</option>
                            <option value="2016"
                            <?php
                            if ($year == "2016") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2016</option>
                            <option value="2017"
                            <?php
                            if ($year == "2017") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >2017</option>
                        </select>
                    </fieldset>
                        <fieldset class="checkbox">
                            <legend>Gender:</legend>
                            <label for="chkgendermale"><input type="checkbox" 
                                                              id="chkgendermale" 
                                                              name="chkgendermale" 
                                                              value="male">Male
                            </label>
                            <label for="chkgenderfemale"><input type="checkbox" 
                                                                id="chkgenderfemale" 
                                                                name="chkgenderfemale" 
                                                                value="female">Female
                            </label>
                            <label for="chkgendernone"><input type="checkbox" 
                                                              id="chkgendernone" 
                                                              name="chkgendernone" 
                                                              value="not given">Don't want to share
                            </label>
                        </fieldset>
                    </fieldset> <!-- ends wrapper Two -->
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



    <?php
    include "footer.php";
}else {
    ?>
    <header>
        <h1>You need to log in first to see this page!</h1>
        <h1>please click the link below to log in.</h1>
        <h1><a href="login.php">Login!</a></h1>
    </header>
    <?php
}
?>
