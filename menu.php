<?php
$results5 = $thisDatabase->select("SELECT fldApproved FROM tblRegister where fldEmail like '%$email%'");
$approveList = $results5[0]["fldApproved"];

if ($approveList == '1') {
    ?>
    <nav>
        <ul>
            <li><a href="home.php">Home Page</a></li>
            <li><a href="viewall.php">View Employees</a></li>
            <li><a href="viewadmin.php">View Admins</a></li>
            <li><a href="viewcontact.php">View Contacts</a></li>
            <li><a href="addemployee.php">Add an Employee</a></li>
            <li><a href="removeemployee.php">Delete an Employee</a></li>
            <li><a href="updateemployee.php">Update Employee Info</a></li>
            <li><a href="newaccount.php">Create an Admin Account</a></li>
            <li><a href="removeadmin.php">Delete an Admin Account</a></li>
            <li><a href="updateadmin.php">Update an Admin Account</a></li>
            <li><a href="addcontact.php">Add Admin Contact Info</a></li>
            <li><a href="removecontact.php">Delete Admin Contact Info</a></li>
            <li><a href="updatecontact.php">Update Admin Contact Info</a></li>
            <li><a href="logout.php">Not You?</a></li>
        </ul>
    </nav>  
    <?php
} else {
    ?>
    <nav>
        <ul>

            <li><a href="home.php">Home Page</a></li>
            <li><a href="viewall.php">View Employees</a></li>
            <li><a href="logout.php">Not You?</a></li>
        </ul>
    </nav>
    <?php
}
?>
