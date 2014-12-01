
<?php
include "top.php";
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
    include "menu.php";
    print "<p><b>You are logged in as: " . $_SESSION['username'] . "!</b></p>";
    ?>
    <body>
        <h1>Welcome to the Employee Management System!</h1>
        <img src="art.jpg" alt="art" style="width:304px;height:190px;padding-left:38%">
        <h2>Please use the menu to the left to navigate the website.</h2>
        <p>This website was designed for Back Cove Holding's administrators.  Employees can be added, deleted, or their status changed on this website.  
        This website is for the exclusive use of Back Cove Holdings.</p>
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

    