<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
    include "menu.php";



    $yourURL = $domain . $phpSelf;
    
    $tableName = "tblRegister";
    $tableNameName = "Tables_in_" . $dbName;
    print "<table>";

    if ($tableName != "") {
        print "<aside id='records'>";
        $query = "SHOW COLUMNS FROM " . $tableName;
        $info = $thisDatabase->select($query);

        $span = count($info);

        //print out the table name and how many records there are
        print "<table border='1'>";
        echo "<tr>";

        

        $query = "SELECT * FROM " . $tableName;
        $a = $thisDatabase->select($query);
        

        echo "</th></tr>";

        //print out the column headings
        print "<tr>";
        $columns = 0;
        foreach ($info as $field) {
            // ok messes up the pk since its not a 3 letter prefix. oh well
            print "<td>";
            $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));

            foreach ($camelCase as $one) {
                print $one . " ";
            }

            "</td>";
            $columns++;
        }
        print "</tr><tr>";

        //now print out each record
        $query = "SELECT * FROM " . $tableName;

        $info2 = $thisDatabase->select($query);

        $highlight = 0; // used to highlight alternate rows
        foreach ($info2 as $rec) {
            $highlight++;
            if ($highlight % 2 != 0) {
                $style = " odd ";
            } else {
                $style = " even ";
            }
            print '<tr class="' . $style . '">';
            for ($i = 0; $i < $columns; $i++) {
                print "<td>" . $rec[$i] . "</td>";
            }
            print "</tr>";
        }

        // all done
        print "</table>";
        print "</aside>";
    }

    
    $number = "";
    $numberERROR = false;


    if (isset($_POST["btnSubmit"])) {

        if (!securityCheck(true)) {
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }
        $number = filter_var($_POST["txtnumber"], FILTER_SANITIZE_NUMBER_INT);
        
        $results1 = $thisDatabase->select("SELECT pmkRegisterId FROM tblRegister where pmkRegisterId = $number");
        $employeeList = $results1[0]["pmkRegisterId"];

        if ($number == "") {
            $errorMsg[] = "Please enter a correct admin number";
            $numberERROR = true;
        } elseif (!verifyNumeric($number)) {
            $errorMsg[] = "That number appears to be incorrect.";
            $numberERROR = true;
        } elseif ($number != $employeeList) {
            $errorMsg[] = "That number appears to be incorrect.";
            $numberERROR = true;
        }

        if (!$errorMsg) {
            
            $results1 = $thisDatabase->select("SELECT fldUserName FROM tblRegister where pmkRegisterId = $number");
            $NameList = $results1[0]["fldUserName"];
            
            $primaryKey = "";
            $dataEntered = false;
            try {
                $thisDatabase->db->beginTransaction();
                $query = 'Delete from tblRegister where pmkRegisterId = ?';
                $data = array($number);
                $results = $thisDatabase->insert($query, $data);
                
                $query = "Delete from tblEmployee where fldName = ? and fldStatus = 'Admin'";
                $data = array($NameList);
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

                $messageA = '<h2>Your Admin with employee number: ' . $number . ' removed!</h2>';
                $messageA .= '<h3>Your table will update when you reload the page.</h2>';
                
                
                
                



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
            print $messageA;
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

                
                <span class='required'></span>

                <fieldset class="intro">
                    <legend>Enter the Admin number you want to delete</legend>

                    <label for="txtnumber" class="required">Admin Number
                            <input type="text" id="txtnumber" name="txtnumber" 
                                   value=""
                                   tabindex="100" maxlength="40" placeholder="Enter the Admin #" 
                                   autofocus onfocus="this.select()" required>
                        </label>
                    <fieldset class="buttons">
                        <legend></legend>
                        <input type="submit" id="btnSubmit" name="btnSubmit" value="Remove" tabindex="900" class="button" >
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