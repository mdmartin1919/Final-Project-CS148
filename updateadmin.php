<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
    include "menu.php";

    $debug = false;

    $yourURL = $domain . $phpSelf;

    $employee = "";
    $name = "";
    $day = "";
    $month = "";
    $year = "";
    $everything = "";
    $address = "";
    $confirmed = "";
    $approved = "";
    $pass = "";
    
    
    $employeeERROR = false;
    $nameERROR = false;
    $storeERROR = false;
    $sexERROR = false;
    $statusERROR = false;
    $datehigheredERROR = false;
    $payERROR = false;
    $everythingERROR = false;
    $addressERROR = false;
    $confirmedERROR = false;
    $approvedERROR = false;
    $passERROR = false;
    $errorMsg = array();


    if (isset($_POST["btnSubmit"])) {

        if (!securityCheck(true)) {
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }
        
        $employee = filter_var($_POST["txtemployee"], FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var($_POST["txtname"], FILTER_SANITIZE_STRING);
        $day = htmlentities($_POST["lstday"], ENT_QUOTES, "UTF-8");
        $month = htmlentities($_POST["lstmonth"], ENT_QUOTES, "UTF-8");
        $year = htmlentities($_POST["lstyear"], ENT_QUOTES, "UTF-8");
        $confirmed = filter_var($_POST["radconfirmed"], FILTER_SANITIZE_NUMBER_INT);
        $approved = filter_var($_POST["radapproved"], FILTER_SANITIZE_NUMBER_INT);
        $pass = filter_var($_POST["txtpass"], FILTER_SANITIZE_STRING);
        
     
        
        
        $results1 = $thisDatabase->select("SELECT pmkRegisterId FROM tblRegister where pmkRegisterId = $employee");
        $employeeList = $results1[0]["pmkRegisterId"];
        
        
        
        if ($employee ==""){
            $errorMsg[] = "You need to enter an employee number.";
            $employeeERROR = true;
        } elseif ($employee != $employeeList){
            $errorMsg[] = "Please enter a correct employee number.";
            $employeeERROR = true;
        }
        
        
        
        if (!verifyAlphaNum($name) & $name != "") {
            $errorMsg[] = "Your first name appears to be incorrect.";
            $nameERROR = true;
        }
        
        
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day == "" & $month == "" & $year == ""){
            $errorMsg[] = "You need to enter at lease one field to update.";
            $everythingERROR = true;  
        }
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day != "" & $month == "" & $year == ""){
            $errorMsg[] = "You need to enter at lease one field to update and make sure the date is fully filled out.";
            $everythingERROR = true;  
        }
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day == "" & $month != "" & $year == ""){
            $errorMsg[] = "You need to enter at lease one field to update and make sure the date is fully filled out.";
            $everythingERROR = true;  
        }
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day == "" & $month == "" & $year != ""){
            $errorMsg[] = "You need to enter at lease one field to update and make sure the date is fully filled out.";
            $everythingERROR = true;  
        }
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day == "" & $month != "" & $year != ""){
            $errorMsg[] = "You need to enter at lease one field to update and make sure the date is fully filled out.";
            $everythingERROR = true;  
        }
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day != "" & $month != "" & $year == ""){
            $errorMsg[] = "You need to enter at lease one field to update and make sure the date is fully filled out.";
            $everythingERROR = true;  
        }
        if ($name == "" & $approved == "" & $confirmed == "" & $pass == "" & $day != "" & $month == "" & $year != ""){
            $errorMsg[] = "You need to enter at lease one field to update and make sure the date is fully filled out.";
            $everythingERROR = true;  
        }
        
        if (!$errorMsg) {
            $datehighered .= $year;
            $datehighered .= "-";
            $datehighered .= $month;
            $datehighered .= "-";
            $datehighered .= $day;
            
            $results10 = $thisDatabase->select("SELECT fldUserName FROM tblRegister where pmkRegisterID = $employee");
            $nameList = $results10[0]["fldUserName"];
            $results16 = $thisDatabase->select("SELECT fldDateJoined FROM tblRegister where pmkRegisterID = $employee");
            $dateList = $results16[0]["fldDateJoined"];
            $results11 = $thisDatabase->select("SELECT fldConfirmed FROM tblRegister where pmkRegisterID = $employee");
            $confirmList = $results11[0]["fldConfirmed"];
            $results12 = $thisDatabase->select("SELECT fldApproved FROM tblRegister where pmkRegisterID = $employee");
            $approveList = $results12[0]["fldApproved"];
            $results13 = $thisDatabase->select("SELECT fldUserPass FROM tblRegister where pmkRegisterID = $employee");
            $passList = $results13[0]["fldUserPass"];
            
            
            if ($name == ""){
                $name = $nameList;
            }
            if ($datehighered == "--"){
                $date = $dateList;
            }
            if ($confirmed == ""){
                $confirmed = $confirmList;
            }
            if ($approved == ""){
                $approved = $approveList;
            }
            if ($pass == ""){
                $pass = $passList;
            }
            
            
            $primaryKey = "";
            $dataEntered = false;
            try {
                $thisDatabase->db->beginTransaction();
                $query = "update tblRegister set fldUserName = ?, fldDateJoined = ?,fldConfirmed = ?, fldApproved = ?, fldUserPass = ? where pmkRegisterId = ?";
                $data = array($name,$date,$confirmed,$approved,$pass,$employee);

                $results = $thisDatabase->insert($query, $data);

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
                
                $messageA = '<h2>Your Admin was updated!</h2>';
                $messageC .= "<p><b>Name:</b><i>   " . $name . "</i></p>";
                $messageC .= "<p><b>Date Joined:</b><i>   " . $date . "</i></p>";
                $messageC .= "<p><b>Confirmed:</b><i>   " . $confirmed . "</i></p>";
                $messageC .= "<p><b>Approved:</b><i>   " . $approved . "</i></p>";
                $messageC .= "<p><b>Password:</b><i>   " . $pass . "</i></p>";
                

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

                <legend>Update an Admin!</legend>
                <span class='required'></span>

                <fieldset class="intro">
                    <legend>Complete the information to update an Admin</legend>

                    <fieldset class="contact">
                        <label for="txtemployee" class="required">Admin Number
                            <input type="text" id="txtemployee" name="txtemployee" 
                                   value=""
                                   tabindex="100" maxlength="40" placeholder="enter an admin #" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                        <legend>Contact Information</legend>					
                        <label for="txtname">Full Name
                            <input type="text" id="txtname" name="txtname" 
                                   value=""
                                   tabindex="100" maxlength="40" placeholder="enter your full name" 
                                   autofocus onfocus="this.select()">
                        </label>
                        <label for="txtpass">password
                            <input type="text" id="txtpass" name="txtpass" 
                                   value=""
                                   tabindex="200" size = "50" maxlength="50" placeholder="enter a password" 
                                   autofocus onfocus="this.select()">
                        </label>
                        
                    </fieldset>	
                    <fieldset class="lists">	
                        <legend>Please pick the day/month/year of higher</legend>
                        <p>DAY:</p>
                        <select id="lstday" name="lstday" tabindex="281" size="1">
                            <option value=""  selected="selected"
                            <?php
                            if ($day == "") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    ></option>
                            <option value="1"
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
                            <option value=""  selected="selected"
                            <?php
                            if ($month == "") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    ></option>
                            <option value="1"
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
                            <option value=""  selected="selected"
                            <?php
                            if ($year == "") {
                                echo 'selected="selected"';
                            }
                            ?>
                                    ></option>
                            <option value="2010"
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
                        <legend>Are they confirmed</legend>
                        <label><input type="radio" id="rad0confirmed" name="radconfirmed" value="0" tabindex="231" 
        <?php
        if ($confirmed == "0") {
            echo 'checked="checked"';
        }
        ?> >Not Confirmed</label>

                        <label><input type="radio" id="rad1confirmed" name="radconfirmed" value="1" tabindex="233" 
                            <?php
                            if ($confirmed == "1") {
                                echo 'checked="checked"';
                            }
                            ?> 
                                      >Confirmed</label>
                    </fieldset>
                    <fieldset class="radio">
                        <legend>Are they confirmed</legend>
                        <label><input type="radio" id="rad0approved" name="radapproved" value="0" tabindex="231" 
        <?php
        if ($approved == "0") {
            echo 'checked="checked"';
        }
        ?> >Not Approved</label>

                        <label><input type="radio" id="rad1approved" name="radapproved" value="1" tabindex="233" 
                            <?php
                            if ($approved == "1") {
                                echo 'checked="checked"';
                            }
                            ?> 
                                      >Approved</label>
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