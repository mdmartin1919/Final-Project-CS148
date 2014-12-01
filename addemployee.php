
<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

    include "menu.php";

    $debug = false;

    $yourURL = $domain . $phpSelf;

    $employee = "";
    $name = "";
    $store = "";
    $sex = "";
    $status = "";
    $pay = "";
    $day = "";
    $month = "";
    $year = "";
    $address = "";


    $employeeERROR = false;
    $nameERROR = false;
    $storeERROR = false;
    $sexERROR = false;
    $statusERROR = false;
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

        $employee = htmlentities($_POST["txtemployee"], ENT_QUOTES, "UTF-8");
        $name = filter_var($_POST["txtname"], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST["txtaddress"], FILTER_SANITIZE_STRING);
        $day = htmlentities($_POST["lstday"], ENT_QUOTES, "UTF-8");
        $month = htmlentities($_POST["lstmonth"], ENT_QUOTES, "UTF-8");
        $year = htmlentities($_POST["lstyear"], ENT_QUOTES, "UTF-8");
        $store = htmlentities($_POST["lststore"], ENT_QUOTES, "UTF-8");
        $status = htmlentities($_POST["lststatus"], ENT_QUOTES, "UTF-8");
        $sex = htmlentities($_POST["radsex"], ENT_QUOTES, "UTF-8");
        $pay = htmlentities($_POST["lstpay"], ENT_QUOTES, "UTF-8");



        if ($name == "") {
            $errorMsg[] = "Please enter your first name";
            $nameERROR = true;
        } elseif (!verifyAlphaNum($name)) {
            $errorMsg[] = "Your first name appears to be incorrect.";
            $nameERROR = true;
        }
        if ($address == "") {
            $errorMsg[] = "Please enter your address";
            $addressERROR = true;
        } elseif (!verifyAlphaNum($address)) {
            $errorMsg[] = "Your address appears to be incorrect.";
            $addressERROR = true;
        }


        if ($sex == "") {
            $errorMsg[] = "please pick your gender.";
            $sexERROR = true;
        }

        if ($datehighered = "") {
            $errorMsg[] = "You cant leave the hire date blank.";
            $datehigheredERROR = true;
        }

        if (!$errorMsg) {

            $datehighered .= $year;
            $datehighered .= "-";
            $datehighered .= $month;
            $datehighered .= "-";
            $datehighered .= $day;
            $primaryKey = "";
            $dataEntered = false;
            try {
                $thisDatabase->db->beginTransaction();
                $query = 'INSERT INTO tblEmployee SET pmkEmployeeNum = ?, fldName = ?,fldHomeAddress = ?, fldStore = ?, fldStatus = ?, fldSex = ?, fldDateHigher = ?, fldPay = ?';
                $data = array($employee, $name, $address, $store, $status, $sex, $datehighered, $pay);

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

                $messageA = '<h2>Your new employee was added!</h2>';
                $messageC .= "<p><b>Name:</b><i>   " . $name . "</i></p>";
                $messageC .= "<p><b>Address:</b><i>   " . $address . "</i></p>";
                $messageC .= "<p><b>Store:</b><i>   " . $store . "</i></p>";
                $messageC .= "<p><b>Status:</b><i>   " . $status . "</i></p>";
                $messageC .= "<p><b>Gender:</b><i>   " . $sex . "</i></p>";
                $messageC .= "<p><b>Date Highered:</b><i>   " . $datehighered . "</i></p>";
                $messageC .= "<p><b>Pay:</b><i>   " . $pay . "</i></p>";
                $messageC .= "<p><a href='addemployee.php'>Add Another Employee?</a></p>";

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

                <legend>Add an Employee!</legend>
                <span class='required'></span>

                <fieldset class="intro">
                    <legend>Complete the information to add a new employee</legend>

                    <fieldset class="contact">
                        <legend>Contact Information</legend>					
                        <label for="txtname" class="required">Full Name
                            <input type="text" id="txtname" name="txtname" 
                                   value=""
                                   tabindex="100" maxlength="40" placeholder="enter your full name" 
                                   autofocus onfocus="this.select()" required>
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

                    <fieldset class="radio">
                        <legend>What is your gender?</legend>
                        <label><input type="radio" id="radmalesex" name="radsex" value="male" tabindex="231" 
                            <?php
                            if ($sex == "male") {
                                echo 'checked="checked"';
                            }
                            ?> >Male</label>

                        <label><input type="radio" id="radfemalesex" name="radsex" value="female" tabindex="233" 
                            <?php
                            if ($sex == "female") {
                                echo 'checked="checked"';
                            }
                            ?> 
                                      >Female</label>
                    </fieldset>

                    <fieldset class="lists">	
                        <legend>Please pick the store</legend>
                        <select id="lststore" name="lststore" tabindex="281" size="1">
                            <option value="Orlando"  selected="selected"
                            <?php
                            if ($store == "Orlando") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >Orlando</option>
                            <option value="Jacksonville" 
                            <?php
                            if ($store == "Jacksonville") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >Jacksonville</option>
                            <option value="Palmbeach"
                            <?php
                            if ($store == "Palmbeach") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >Palmbeach</option>
                            <option value="Bocaraton"
                            <?php
                            if ($store == "Bocaraton") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >Bocaraton</option>
                        </select>
                    </fieldset>
                    <fieldset class="lists">	
                        <legend>Please pick your status</legend>
                        <select id="lststatus" name="lststatus" tabindex="281" size="1">
                            <option value="Manager"  selected="selected"
                            <?php
                            if ($status == "Manager") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >Manager</option>
                            <option value="asst. manager" 
                            <?php
                            if ($status == "asst. manager") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >Asst. Manager</option>
                            <option value="shift leader"
                            <?php
                            if ($status == "shift leader") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >shift leader</option>
                            <option value="crew member"
                            <?php
                            if ($status == "crew member") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >crew member</option>
                        </select>
                    </fieldset>
                    <fieldset class="lists">	
                        <legend>Please pick your hourly pay</legend>
                        <select id="lstpay" name="lstpay" tabindex="281" size="1">
                            <option value="8.00"  selected="selected"
                            <?php
                            if ($pay == "8.00") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >$8.00</option>
                            <option value="8.50" 
                            <?php
                            if ($pay == "8.50") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >$8.50</option>
                            <option value="9.00"
                            <?php
                            if ($pay == "9.00") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >$9.00</option>
                            <option value="9.50"
                            <?php
                            if ($pay == "9.50") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    >$9.50</option>
                        </select>
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